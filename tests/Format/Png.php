<?php

use Maaaxim\Photo\Format\Png as PngFormat;
use Maaaxim\Photo\Gps;

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 6:47 PM
 */

class Png extends \PHPUnit\Framework\TestCase
{
    /**
     *
     */
    public function testGetGps()
    {
        $jpg = new PngFormat(dirname(__FILE__) . '/../images/sample.png');
        $gps = $jpg->getGps();
        $this->assertEquals($gps->getLatitude(), 59.966901900002);
        $this->assertEquals($gps->getLongitude(), 10.674008216653);
    }

    /**
     *
     */
    public function testSetGps()
    {
        $jpg = new PngFormat(dirname(__FILE__) . '/../images/sample.png');
        $gps = new Gps(59.966901900002, 10.674008216653);
        $jpg->setGps($gps);
    }
}