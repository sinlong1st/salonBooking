
$(document).ready(function () {

    $(document).on('change', '.shop-service-selected', function () {
        if ($(this).is(':checked')) {
            $(this).closest('.service-group').find('.shop-service-selected-price').removeClass('d-none');
        } else {
            $(this).closest('.service-group').find('.shop-service-selected-price').addClass('d-none');
        }
    });
});