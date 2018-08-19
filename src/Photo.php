<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 3:41 PM
 */

namespace Maaaxim\Photo;

use Maaaxim\Photo\Exception\UnsupportedExtensionException;

/**
 * Class Photo - context
 * @package Maaaxim\Photo
 */
class Photo
{
    /**
     * @var
     */
    protected $file;

    /**
     * Photo constructor.
     * @param $path
     * @throws UnsupportedExtensionException
     */
    public function __construct($path)
    {
        $pathInfo = pathinfo($path);
        $pathInfo["extension"] = ucfirst(strtolower($pathInfo["extension"]));
        $formatClassName = __NAMESPACE__ . '\\Format\\' . $pathInfo["extension"];
        if(!class_exists($formatClassName))
            throw new UnsupportedExtensionException();
        $this->file = new $formatClassName($path);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function setGps(float $latitude, float $longitude): void
    {
        $gps = new Gps($latitude, $longitude);
        $this->file->setGps($gps);
    }

    /**
     * @return Gps
     */
    public function getGps(): Gps
    {
        return $this->file->getGps();
    }
}