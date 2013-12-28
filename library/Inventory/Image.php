<?php
class Inventory_Image
{
     private $image;
     private $imageType;
     private $width;
     private $height;

     public function load($filename)
     {
         $imageInfo = getimagesize($filename);

         $this->width = $imageInfo[0];
         $this->height = $imageInfo[1];
         $this->imageType = $imageInfo[2];

         if( $this->imageType == IMAGETYPE_JPEG ) {
             $this->image = imagecreatefromjpeg($filename);
         } elseif( $this->imageType == IMAGETYPE_GIF ) {
             $this->image = imagecreatefromgif($filename);
         } elseif( $this->imageType == IMAGETYPE_PNG ) {
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

     public function createThumbnail($thumbnailWidth = 100, $thumbnailHeight = 225)
     {
        $oldWidth = $this->width / $thumbnailWidth;
        $oldHeight = $this->height / $thumbnailHeight;

        $newWidth = round($this->width / max($oldWidth, $oldHeight), 0);
        $newHeight = round($this->height / max($oldHeight, $oldWidth), 0);

        $newImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        $background = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $background);

        imagecopyresampled(
            $newImage
          , $this->image
          , ($thumbnailWidth - $newWidth) / 2
          , ($thumbnailHeight - $newHeight) / 2
          , 0
          , 0
          , $newWidth
          , $newHeight
          , $this->width
          , $this->height
        );
        $this->image = $newImage;
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
         $newImage = imagecreatetruecolor($width, $height);
         imagecopyresampled(
               $newImage
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
         $this->image = $newImage;
     }
}