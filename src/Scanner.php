<?php

namespace Maaaxim\Photo;

/**
 * Helps to get files in directory
 *
 * Class Scanner
 * @package Maaaxim\Photo
 */
class Scanner
{
    /**
     * @param string $path
     * @return array
     */
    public function scanDirectory(string $path = "photos"): array
    {
        $jpegFiles = [];
        $files = scandir($path);
        foreach($files as $file){
            if($file !== '.' && $file !== '..'){
                $pathToImage = $path."/".$file;
                $jpegFiles[] = $pathToImage;
            }
        }
        return $jpegFiles;
    }
}