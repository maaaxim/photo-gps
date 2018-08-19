<?php

use Maaaxim\Photo\Format\Jpg as JpgFormat;
use Maaaxim\Photo\Gps;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 6:46 PM
 */

class Jpg extends TestCase
{
    /**
     * @throws \Maaaxim\Photo\Exception\ExifException
     */
    public function testGetGps()
    {
        $jpg = new JpgFormat(dirname(__FILE__) . '/../images/IMG_0450.JPG');
        $gps = $jpg->getGps();
        $this->assertEquals($gps->getLatitude(), 50.458333333333);
        $this->assertEquals($gps->getLongitude(), 30.613666666667);
    }

    /**
     * @throws \Maaaxim\Photo\Exception\ExifException
     */
    public function testSetGps()
    {
        $jpg = new JpgFormat(dirname(__FILE__) . '/../images/IMG_0450.JPG');
        $gps = new Gps(50.458333333333, 30.613666666667);
        $jpg->setGps($gps);
    }
}