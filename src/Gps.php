<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 4:16 PM
 */

namespace Maaaxim\Photo;

/**
 * Class Gps
 * @package Maaaxim\Photo
 */
class Gps
{
    /**
     * @var
     */
    public $latitude;

    /**
     * @var
     */
    public $longitude;

    /**
     * Gps constructor.
     * @param $latitude
     * @param $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return json_decode(json_encode($this), true);
    }

    /**
     * @param string $coordinatePart
     * @return float
     */
    public static function gps2Num(string $coordinatePart): float
    {
        $parts = explode('/', $coordinatePart);
        if(count($parts) <= 0)
            return 0;
        if(count($parts) == 1)
            return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }

    /**
     * Convert a decimal degree into degrees, minutes, and seconds.
     *
     * @param
     *            int the degree in the form 123.456. Must be in the interval
     *            [-180, 180].
     *
     * @return array a triple with the degrees, minutes, and seconds. Each
     *         value is an array itself, suitable for passing to a
     *         PelEntryRational. If the degree is outside the allowed interval,
     *         null is returned instead.
     */
    public static function convertDecimalToDMS($degree)
    {
        if ($degree > 180 || $degree < - 180) {
            return null;
        }

        $degree = abs($degree); // make sure number is positive
        // (no distinction here for N/S
        // or W/E).

        $seconds = $degree * 3600; // Total number of seconds.

        $degrees = floor($degree); // Number of whole degrees.
        $seconds -= $degrees * 3600; // Subtract the number of seconds
        // taken by the degrees.

        $minutes = floor($seconds / 60); // Number of whole minutes.
        $seconds -= $minutes * 60; // Subtract the number of seconds
        // taken by the minutes.

        $seconds = round($seconds * 100, 0); // Round seconds with a 1/100th
        // second precision.

        return array(
            array(
                $degrees,
                1
            ),
            array(
                $minutes,
                1
            ),
            array(
                $seconds,
                100
            )
        );
    }

    /**
     * @param $degree
     * @return string
     */
    public static function convertDecimalToDMSString($degree)
    {
        $dmsArray = self::convertDecimalToDMS($degree);
        $dmsString = "";
        foreach($dmsArray as $item){
            if(strlen($dmsString) > 0)
                $dmsString .= ",";
            $dmsString .= $item[0] . "/" . $item[1];
        }
        return $dmsString;
    }

    /**
     * @param $GPSLatitudeRef
     * @param $GPSLatitude
     * @return float
     */
    public static function convertDMSLatitudeToDecimal($GPSLatitudeRef, $GPSLatitude): float
    {
        $latDegrees = count($GPSLatitude) > 0 ? Gps::gps2Num($GPSLatitude[0]) : 0;
        $latMinutes = count($GPSLatitude) > 1 ? Gps::gps2Num($GPSLatitude[1]) : 0;
        $latSeconds = count($GPSLatitude) > 2 ? Gps::gps2Num($GPSLatitude[2]) : 0;
        $latDirection = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
        $latitude = $latDirection * ($latDegrees + ($latMinutes / 60) + ($latSeconds / (60*60)));
        return $latitude;
    }

    /**
     * @param $GPSLongitudeRef
     * @param $GPSLongitude
     * @return float
     */
    public static function convertDMSLongitudeToDecimal($GPSLongitudeRef, $GPSLongitude): float
    {
        $lonDegrees = count($GPSLongitude) > 0 ? Gps::gps2Num($GPSLongitude[0]) : 0;
        $lonMinutes = count($GPSLongitude) > 1 ? Gps::gps2Num($GPSLongitude[1]) : 0;
        $lonSeconds = count($GPSLongitude) > 2 ? Gps::gps2Num($GPSLongitude[2]) : 0;
        $lonDirection = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
        $longitude = $lonDirection * ($lonDegrees + ($lonMinutes / 60) + ($lonSeconds / (60*60)));
        return $longitude;
    }
}