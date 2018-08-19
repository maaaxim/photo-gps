<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 8/19/18
 * Time: 3:52 PM
 */

namespace Maaaxim\Photo\Format;

use Maaaxim\Photo\Exception\ExifException;
use Maaaxim\Photo\Gps;
use lsolesen\pel\PelIfdException;
use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;

/**
 * Class Jpg
 * @package Maaaxim\Photo\Format
 */
class Jpg implements Format
{
    /**
     * @var
     */
    protected $path;

    /**
     * Jpg constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return Gps
     * @throws ExifException
     */
    public function getGps(): Gps
    {
        $exif = exif_read_data($this->path, 0, true);
        if(!$exif)
            throw new ExifException("EXIF data is not found in file");
        if(!isset($exif['GPS']))
            throw new ExifException("Gps data is not found in EXIF");
        $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
        $GPSLatitude    = $exif['GPS']['GPSLatitude'];
        $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
        $GPSLongitude   = $exif['GPS']['GPSLongitude'];
        return new Gps(
            Gps::convertDMSLatitudeToDecimal($GPSLatitudeRef, $GPSLatitude),
            Gps::convertDMSLongitudeToDecimal($GPSLongitudeRef, $GPSLongitude)
        );
    }

    /**
     * @param Gps $gps
     * @throws ExifException
     */
    public function setGps(Gps $gps): void
    {
        try {
            $this->addGpsInfo(
                $this->path,
                $this->path,
                "",
                "",
                "",
                $gps->getLongitude(),
                $gps->getLatitude(),
                "",
                date('Y:m:d h:i:s')
            );
        } catch (PelIfdException $e) {
            throw new ExifException("Ifd error");
        } catch (PelInvalidArgumentException $e) {
            throw new ExifException("Exif error");
        } catch (PelInvalidDataException $e) {
            throw new ExifException("Data error");
        }
    }

    /**
     * Add GPS information to an image basic metadata.
     * Any old Exif data
     * is discarded.
     *
     * @param $input
     * @param $output
     * @param $description
     * @param $comment
     * @param $model
     * @param $longitude
     * @param $latitude
     * @param $altitude
     * @param $date_time
     * @throws \lsolesen\pel\PelIfdException
     * @throws \lsolesen\pel\PelInvalidArgumentException
     * @throws \lsolesen\pel\PelInvalidDataException
     */
    protected function addGpsInfo($input, $output, $description, $comment, $model, $longitude, $latitude, $altitude, $date_time)
    {
        /* Load the given image into a PelJpeg object */
        $jpeg = new PelJpeg($input);

        /*
         * Create and add empty Exif data to the image (this throws away any
         * old Exif data in the image).
         */
        $exif = new PelExif();
        $jpeg->setExif($exif);

        /*
         * Create and add TIFF data to the Exif data (Exif data is actually
         * stored in a TIFF format).
         */
        $tiff = new PelTiff();
        $exif->setTiff($tiff);

        /*
         * Create first Image File Directory and associate it with the TIFF
         * data.
         */
        $ifd0 = new PelIfd(PelIfd::IFD0);
        $tiff->setIfd($ifd0);

        /*
         * Create a sub-IFD for holding GPS information. GPS data must be
         * below the first IFD.
         */
        $gps_ifd = new PelIfd(PelIfd::GPS);
        $ifd0->addSubIfd($gps_ifd);

        /*
         * The USER_COMMENT tag must be put in a Exif sub-IFD under the
         * first IFD.
         */
        $exif_ifd = new PelIfd(PelIfd::EXIF);
        $exif_ifd->addEntry(new PelEntryUserComment($comment));
        $ifd0->addSubIfd($exif_ifd);

        $inter_ifd = new PelIfd(PelIfd::INTEROPERABILITY);
        $ifd0->addSubIfd($inter_ifd);

        $ifd0->addEntry(new PelEntryAscii(PelTag::MODEL, $model));
        $ifd0->addEntry(new PelEntryAscii(PelTag::DATE_TIME, $date_time));
        $ifd0->addEntry(new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $description));

        $gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_VERSION_ID, 2, 2, 0, 0));

        /*
         * Use the convertDecimalToDMS function to convert the latitude from
         * something like 12.34� to 12� 20' 42"
         */
        list ($hours, $minutes, $seconds) = Gps::convertDecimalToDMS($latitude);

        /* We interpret a negative latitude as being south. */
        $latitude_ref = ($latitude < 0) ? 'S' : 'N';

        $gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LATITUDE_REF, $latitude_ref));
        $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LATITUDE, $hours, $minutes, $seconds));

        /* The longitude works like the latitude. */
        list ($hours, $minutes, $seconds) = Gps::convertDecimalToDMS($longitude);
        $longitude_ref = ($longitude < 0) ? 'W' : 'E';

        $gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LONGITUDE_REF, $longitude_ref));
        $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LONGITUDE, $hours, $minutes, $seconds));

        /*
         * Add the altitude. The absolute value is stored here, the sign is
         * stored in the GPS_ALTITUDE_REF tag below.
         */
        $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_ALTITUDE, array(
            abs($altitude),
            1
        )));
        /*
         * The reference is set to 1 (true) if the altitude is below sea
         * level, or 0 (false) otherwise.
         */
        $gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_ALTITUDE_REF, (int) ($altitude < 0)));

        /* Finally we store the data in the output file. */
        file_put_contents($output, $jpeg->getBytes());
    }
}