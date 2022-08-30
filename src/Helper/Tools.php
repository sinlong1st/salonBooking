<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helper;

/**
 * Description of Function
 *
 * @author trieu
 */
class Tools {

    /**
     * Calculate current date time available
     * @param \DateInterval $serviceHrs Shop Service Hours
     * @param \DateInterval $defaultStartTime Shop open open
     * @param \DateInterval $defaultEndTime Shop clost time
     * @param array $existingBooking List Booking booked current date
     * @return array
     */
    public static function getTimeRangeAvailable(\DateTimeInterface $serviceHrs, \DateTimeInterface $defaultStartTime, \DateTimeInterface $defaultEndTime, array $existingBooking) {
        $currentDate = new \DateTime($defaultStartTime->format('Y-m-d 00:00:00'));
        $rs = [];
        $serviceMinutes = self::covertTimeToMinutes($serviceHrs);
        //24*60=1440
        //
        $existingBookingIndex = 0;
        $minTime = self::covertTimeToMinutes($defaultStartTime);

        $maxTime = self::covertTimeToMinutes($defaultEndTime);
        while ($minTime < $maxTime) {
            $endTimeAvailable = ($minTime + $serviceMinutes);
            //have next booking record
            if (isset($existingBooking[$existingBookingIndex])) {
                $existingBookingDetail = $existingBooking[$existingBookingIndex];

                $existingBookingBeginTime = self::covertTimeToMinutes($existingBookingDetail['start_time']);
                //not enough time to do service
                if ($existingBookingBeginTime <= $endTimeAvailable) {
                    $existingBookingIndex++;
                    $minTime = self::covertTimeToMinutes($existingBookingDetail['end_time']);
                    continue;
                } else {
                    $rs[] = $minTime;
                    $minTime += 15;
                }
            } else {
                //date do not have any booking
                $rs[] = $minTime;
                $minTime += 15;
            }
        }
        if (count($rs) > 0) {
            $resultConverted = [];
            foreach ($rs as $available) {
                $timeInteger = self::convertDayMinutesToTime($available);
                $resultConverted[$timeInteger] = self::convertIntegerTimeToString($timeInteger);
            }
            return $resultConverted;
        }
        return [];
    }

    public static function covertTimeToMinutes(\DateTimeInterface $time) {
        $hours = (int) $time->format('H');

        $min = $time->format('i');
        $totalMinutes = $hours * 60 + $min;
        return $totalMinutes;
    }

    public static function convertDayMinutesToTime(int $min) {
        return ((floor($min / 60)) * 100) + ($min % 60);
    }

    public static function convertIntegerTimeToString(int $time) {
        $min = $time % 100;
        $hrs = floor($time / 100);
        return sprintf('%d:%d', $hrs, $min);
    }

}
