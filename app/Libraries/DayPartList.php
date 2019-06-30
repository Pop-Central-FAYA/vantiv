<?php

namespace Vanguard\Libraries;

class DayPartList
{
    const DAYPARTS = array(
        "Late Night" => array("21:00", "00:00"),
        "Overnight" => array("00:00", "05:00"),
        "Breakfast" => array("05:00", "09:00"),
        "Late Breakfast" => array("09:00", "12:00"),
        "Afternoon" => array("12:00", "17:00"),
        "Primetime" => array("17:00", "21:00")
    );

    const DAYPARTLIST = array(
        "Late Night" => array("21:00", "23:59"),
        "Overnight" => array("00:00", "05:00"),
        "Breakfast" => array("05:00", "09:00"),
        "Late Breakfast" => array("09:00", "12:00"),
        "Afternoon" => array("12:00", "17:00"),
        "Primetime" => array("17:00", "21:00")
    );
}