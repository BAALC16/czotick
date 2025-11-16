<?php

use Illuminate\Support\Carbon;

function isExpired($endDay){ //$date1 = dateNow; $date2 = endDay
    $dateNow=Carbon::now();
    $timestamp1 = strtotime($dateNow); 
    $timestamp2 = strtotime($endDay); 
    if ($timestamp1 > $timestamp2)
        return true;
    else
        return false;

}
