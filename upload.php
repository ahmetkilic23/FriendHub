<?php

class Upload {
    public $filename = null ;
    public $error = null ;
    const MAX_FILESIZE = 1024 * 1024 ; 

    public function __construct($filebox, $uploadFolder)
    {
        if (!empty($_FILES[$filebox]["name"])) {
           // a file uploaded
           extract($_FILES[$filebox]) ;

           $ext = strtolower(pathinfo( $name, PATHINFO_EXTENSION)) ;
           $whitelist = ["png", "jpg", "jpeg"] ; 
           if ( !in_array($ext, $whitelist)) {
             $this->error = "Not an image file" ;
           } else if ($size > self::MAX_FILESIZE ) {
              $this->error = "Too big for this app." ;
           } else {
              // file is valid to be used in the app
              $this->filename = sha1(uniqid() . $tmp_name . $name . $size) . ".$ext" ;  // hash = 40 hex digits
              if ( !move_uploaded_file($tmp_name, $uploadFolder."/".$this->filename)){
                $this->error = "Move/Copy Error" ;
                $this->filename = null ; 
              }
            }
        } else {
            // upload failed or no file selected
            $this->error = "No file uploaded" ; 
        }
    }
}
