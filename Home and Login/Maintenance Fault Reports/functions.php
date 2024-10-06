<?php
// functions.php

if (!function_exists('getSemester')) {
    /**
     * Determines the current semester based on a given date.
     *
     * @param string $date The date to evaluate.
     * @return int|null Returns 1 for First Semester, 2 for Second Semester, or null if out of semester range.
     */
    function getSemester($date)
    {
        $semester1Start = strtotime("12 February 2024");
        $semester1End = strtotime("14 June 2024");
        $semester2Start = strtotime("8 July 2024");
        $semester2End = strtotime("15 November 2024");

        $currentDate = strtotime($date);

        if ($currentDate >= $semester1Start && $currentDate <= $semester1End) {
            return 1;
        } elseif ($currentDate >= $semester2Start && $currentDate <= $semester2End) {
            return 2;
        } else {
            return null; // Out of semester, should not count
        }
    }
}
