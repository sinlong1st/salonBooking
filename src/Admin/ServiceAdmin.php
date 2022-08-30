<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Admin;

/**
 * Description of ServiceAdmin
 *
 * @author trieu
 */
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Services;
use App\Entity\ShopService;
use Symfony\Component\Validator\Constraints\File;

class ServiceAdmin extends AbstractAdmin {

    private $container;

    protected function configureFormFields(FormMapper $form): void {
        $request = $this->getRequest();
        $form->add('Name', TextType::class, ['required' => true]);
        $form->add('Description', TextareaType::class, ['required' => false]);
        $thumbnailForm = ['required' => false, 'label' => 'Thumbnail', 'attr' => ['accept' => 'image/*'], 'constraints' => [new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/*'
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image',
        ])]];
        if ($this->hasSubject()) {
            $subject = $this->getSubject();
            if (!empty($subject->getThumbnail())) {
                $thumbnailForm['help'] = '<img src="' . $request->getSchemeAndHttpHost() . DIRECTORY_SEPARATOR . Services::SERVER_PATH_TO_IMAGE_FOLDER . DIRECTORY_SEPARATOR . $subject->getThumbnail() . '" width="150px" class="admin-preview"/>';
                $thumbnailForm['help_html'] = true;
            }
        }

        $form->add('Thumbnailfile', FileType::class, $thumbnailForm);
        $form->add('Active', ChoiceType::class, ['required' => true,
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void {
        $datagrid->add('Name');
        $datagrid->add('Description');
        $datagrid->add('Active');
    }

    protected function configureListFields(ListMapper $list): void {
        $list->addIdentifier('id', null, ['route' => ['name' => 'edit']]);
        $list->addIdentifier('Name', null, ['route' => ['name' => 'edit']]);
        $list->add('Description');
        $list->add('Active');
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

    protected function preRemove(object $object): void {
        $shopServices = $this->getModelManager(ShopService::class)->findBy(ShopService::class, ['Service' => $object->getId()]);
        if (count($shopServices) > 0) {
            foreach ($shopServices as $shopservice) {
                $object->removeService($shopservice);
            }
        }
    }

    protected function prePersist(object $object): void {
        $this->manageFileUpload($object);
    }

    protected function preUpdate(object $object): void {
        $this->manageFileUpload($object);
    }

    private function manageFileUpload(object $object): void {
        $rootDirectory = $this->container->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . Services::SERVER_PATH_TO_IMAGE_FOLDER;

        if (null === $object->getThumbnailfile()) {
            return;
        }

        if (!file_exists($rootDirectory)) {
            mkdir($rootDirectory, 0777, true);
        }

        // move takes the target directory and target filename as params
        $object->getThumbnailfile()->move(
                $rootDirectory,
                $object->getThumbnailfile()->getClientOriginalName()
        );
        //unlink old file
        if (!empty($object->getThumbnail()) && file_exists($rootDirectory . DIRECTORY_SEPARATOR . $object->getThumbnail())) {
            unlink($rootDirectory . DIRECTORY_SEPARATOR . $object->getThumbnail());
        }
        // set the path property to the filename where you've saved the file
        $object->setThumbnail($object->getThumbnailfile()->getClientOriginalName());
        // clean up the file property as you won't need it anymore
        $object->setThumbnailfile(null);
    }

}
