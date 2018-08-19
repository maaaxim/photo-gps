<?php

use Maaaxim\Photo\Photo as PhotoContext;

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 6:46 PM
 */

/**
 * Class Photo
 */
class Photo extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \Maaaxim\Photo\Exception\UnsupportedExtensionException
     */
    public function testSetGps()
    {
        $photo = new PhotoContext(dirname(__FILE__) . '/images/IMG_0450.JPG');
        $photo->setGps(50.458333333333, 30.613666666667);
    }

    /**
     * @throws \Maaaxim\Photo\Exception\UnsupportedExtensionException
     */
    public function testGetGps()
    {
        $photo = new PhotoContext(dirname(__FILE__) . '/images/IMG_0450.JPG');
        $gps = $photo->getGps();
        $this->assertEquals($gps->getLatitude(), 50.458333333333);
        $this->assertEquals($gps->getLongitude(), 30.613666666667);
    }
}