<?php


namespace App\Classes;


class Images extends db
{

    public $file_name;
    public $file;
    public $path;
    public $fk_chambre_uniqid;


    public function InsertImage(){


        $Uploader = new \App\Classes\Uploader($this->file['chambre_photo'],$this->file_name,$this->path);


        $this->db->beginTransaction();

        try{

            $filename_extension = $Uploader->upload();


            $this->db->insert('t_images',
                array(
                    'images'                => $filename_extension,
                    'path'                  => $this->path,
                    'fk_chambre_uniqid'     => $this->fk_chambre_uniqid,
                    'created_at'            => $this->getCreatedAt(),
                    'updated_at'            => $this->getUpdatedAt()));


            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function getCreatedAt(){
        return date('Y-m-d H:i:s');

    }

    private function getUpdatedAt(){

        return date('Y-m-d H:i:s');
    }
}