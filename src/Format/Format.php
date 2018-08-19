<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 3:38 PM
 */

namespace Maaaxim\Photo\Format;

use Maaaxim\Photo\Gps;

interface Format
{
    /**
     * Format constructor.
     * @param string $path
     */
    public function __construct(string $path);

    /**
     * @return Gps
     */
    public function getGps(): Gps;

    /**
     * @param Gps $gps
     */
    public function setGps(Gps $gps): void;
}