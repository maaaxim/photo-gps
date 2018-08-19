<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 3:56 PM
 */

namespace Maaaxim\Photo\Format;

use Imagick;
use Maaaxim\Photo\Gps;

/**
 * Class Png
 * exif is not supported.
 * Use properties instead
 *
 * @package Maaaxim\Photo\Format
 */
class Png implements Format
{
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getGps(): Gps
    {
        $handle = fopen($this->path, 'rb');
        $img = new Imagick();
        $img->readImageFile($handle);
        $exif = $img->getImageProperties("exif:*");
        $GPSLatitudeRef = $exif['exif:GPSLatitudeRef'];
        $GPSLatitude    = explode(",", $exif['exif:GPSLatitude']);
        $GPSLongitudeRef= $exif['exif:GPSLongitudeRef'];
        $GPSLongitude   = explode("," ,$exif['exif:GPSLongitude']);
        return new Gps(
            Gps::convertDMSLatitudeToDecimal($GPSLatitudeRef, $GPSLatitude),
            Gps::convertDMSLongitudeToDecimal($GPSLongitudeRef, $GPSLongitude)
        );
    }

    /**
     * @param Gps $gps
     */
    public function setGps(Gps $gps): void
    {
        $handle = fopen($this->path, 'rb');
        $img = new Imagick();
        $img->readImageFile($handle);
        $img->setImageProperty("exif:GPSDateStamp", date('Y:m:d'));
        $img->setImageProperty("exif:GPSLatitude", Gps::convertDecimalToDMSString($gps->getLatitude()));
        $img->setImageProperty("exif:GPSLatitudeRef", "N");
        $img->setImageProperty("exif:GPSLongitude", Gps::convertDecimalToDMSString($gps->getLongitude()));
        $img->setImageProperty("exif:GPSLongitudeRef", "E");
        $img->writeImage($this->path);
        fclose($handle);
    }
}