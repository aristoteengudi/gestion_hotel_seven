<?php

/**
 * @author Aristote ENGUDI
 * @version 0.1
 * @copyright 2020
 */
namespace App\Classes;


class Uploader
{

    private $files; // complete $_FILES instance
    private $filenames; // file name
    private $directory; // target directory for store the files

    /**
     * Uploader constructor.
     * @param $files
     * @param $filename
     * @param string $directory
     */
    public function __construct($files,$filename,$directory = '/var/')
    {
        $this->files        = $files;
        $this->filenames    = $filename;
        $this->directory    = $directory;
    }

    /**
     * process uploading files
     * @return bool
     */
    public function upload(){

        $files = $this->files;
        $filename = $this->filenames;
        $directory = $this->directory;
        $target_dir = __DIR__.'/../..'.$directory;

        $extension = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION)); // get extension

        if (!file_exists(__DIR__.'/../..'.$directory)){
            mkdir(__DIR__.'/../..'.$directory,0777,true);
        }

        $path_and_name = $target_dir.'/'.$filename.'.'.$extension;

        if (move_uploaded_file($files["tmp_name"],$path_and_name)){
            return $filename.'.'.$extension; // return file name with her extension
        }
        return false;
    }

}