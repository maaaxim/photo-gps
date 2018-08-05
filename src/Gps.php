<?php

namespace Maaaxim\Photo;

use Maaaxim\Photo\Exceptions\GpsException;

/**
 * Class Gps
 * @package Maaaxim\Photo
 */
class Gps
{
    /**
     * Default precision
     */
    const PRECISION = 2;

    /**
     * Returns an array of latitude and longitude from the Image file
     *
     * @param string $path path to image
     * @return array
     * @throws GpsException
     */
    public function getImageLocation(string $path): array
    {
        $exif = exif_read_data($path, 0, true);
        if(!$exif)
            throw new GpsException("EXIT data is not found in file");

        if(!isset($exif['GPS']))
            throw new GpsException("Gps data is not found in EXIF");

        $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
        $GPSLatitude    = $exif['GPS']['GPSLatitude'];
        $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
        $GPSLongitude   = $exif['GPS']['GPSLongitude'];

        $latDegrees = count($GPSLatitude) > 0 ? $this->gps2Num($GPSLatitude[0]) : 0;
        $latMinutes = count($GPSLatitude) > 1 ? $this->gps2Num($GPSLatitude[1]) : 0;
        $latSeconds = count($GPSLatitude) > 2 ? $this->gps2Num($GPSLatitude[2]) : 0;

        $lonDegrees = count($GPSLongitude) > 0 ? $this->gps2Num($GPSLongitude[0]) : 0;
        $lonMinutes = count($GPSLongitude) > 1 ? $this->gps2Num($GPSLongitude[1]) : 0;
        $lonSeconds = count($GPSLongitude) > 2 ? $this->gps2Num($GPSLongitude[2]) : 0;

        $latDirection = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
        $lonDirection = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;

        $latitude = $latDirection * ($latDegrees + ($latMinutes / 60) + ($latSeconds / (60*60)));
        $longitude = $lonDirection * ($lonDegrees + ($lonMinutes / 60) + ($lonSeconds / (60*60)));

        return [
            'latitude' => round($latitude, self::PRECISION),
            'longitude' => round($longitude, self::PRECISION)
        ];
    }

    /**
     * @param string $coordinatePart
     * @return float
     */
    protected function gps2Num(string $coordinatePart): float
    {
        $parts = explode('/', $coordinatePart);
        if(count($parts) <= 0)
            return 0;
        if(count($parts) == 1)
            return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
}