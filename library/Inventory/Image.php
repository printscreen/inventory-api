<?php
class Inventory_Image
{
     private $image;
     private $imageType;

     public function load($filename)
     {
         $imageInfo = getimagesize($filename);
         $this->image_type = $imageInfo[2];
         if( $this->image_type == IMAGETYPE_JPEG ) {
             $this->image = imagecreatefromjpeg($filename);
         } elseif( $this->image_type == IMAGETYPE_GIF ) {
             $this->image = imagecreatefromgif($filename);
         } elseif( $this->image_type == IMAGETYPE_PNG ) {
             $this->image = imagecreatefrompng($filename);
         }
     }

     public function loadFromBinaries($binaries)
     {
         $temp = tmpfile();
         $meta_data = stream_get_meta_data($temp);
         $file  = $meta_data["uri"];
         file_put_contents($file, $binaries);
         $this->load($file);
     }

     public function save($filename, $imageType=IMAGETYPE_JPEG, $compression=75, $permissions=null)
     {
         if( $imageType == IMAGETYPE_JPEG ) {
             imagejpeg($this->image,$filename,$compression);
         } elseif( $imageType == IMAGETYPE_GIF ) {
             imagegif($this->image,$filename);
         } elseif( $imageType == IMAGETYPE_PNG ) {
             imagepng($this->image,$filename);
         }
         if( $permissions != null) {
             chmod($filename,$permissions);
         }
     }

     public function output($imageType=IMAGETYPE_JPEG, $imageQuality = 100)
     {
         $temp = tmpfile();
         $meta_data = stream_get_meta_data($temp);
         $file  = $meta_data["uri"];

         if( $imageType == IMAGETYPE_JPEG ) {
             imagejpeg($this->image, $file, $imageQuality);
         } elseif( $imageType == IMAGETYPE_GIF ) {
             imagegif($this->image, $file);
         } elseif( $imageType == IMAGETYPE_PNG ) {
             imagepng($this->image, $file, $imageQuality);
         }
         rewind($temp);
         return array(
             'filename' => $file,
             'resource' => $temp
         );
     }

     public function getWidth()
     {
         return imagesx($this->image);
     }

     public function getHeight()
     {
         return imagesy($this->image);
     }

     public function resizeToHeight($height)
     {
         $ratio = $height / $this->getHeight();
         $width = $this->getWidth() * $ratio;
         $this->resize($width,$height);
     }

     public function resizeToWidth($width)
     {
         $ratio = $width / $this->getWidth();
         $height = $this->getheight() * $ratio;
         $this->resize($width,$height);
     }

     public function shrinkToSize($maxWidth, $maxHeight)
     {
         $widthRatioDifference = ($this->getWidth() - $maxWidth) / $this->getWidth();
         $heightRatioDifference = ($this->getHeight() - $maxHeight) / $this->getHeight();

         if($widthRatioDifference < 0 && $heightRatioDifference < 0) {
             return false;
         }
         if($widthRatioDifference > $heightRatioDifference) {
             return $this->resizeToWidth($maxWidth);
         } else {
             return $this->resizeToHeight($maxHeight);
         }
     }

     public function scale($scale)
     {
         $width = $this->getWidth() * $scale/100;
         $height = $this->getheight() * $scale/100;
         $this->resize($width,$height);
     }

    public function rotate($degrees)
     {
         $this->image = imagerotate($this->image, $degrees, 0);
     }

     public function resize($width, $height, $x = 0, $y = 0, $sourceWidth = null, $sourceHeight = null)
     {
         $new_image = imagecreatetruecolor($width, $height);
         imagecopyresampled(
               $new_image
             , $this->image
             , 0
             , 0
             , $x
             , $y
             , $width
             , $height
             , is_null($sourceWidth) ? $this->getWidth() : $sourceWidth
             , is_null($sourceHeight) ? $this->getHeight() : $sourceHeight
         );
         $this->image = $new_image;
     }
}