<?php

/* Simple class for Captcha script
 * by Lawrence Okoth-Odida
 */
 
class SimpleCaptcha {
  /* properties */
  private $length;
  private $fontDir;
  private $dataDir;
  private $imageDir;
  private $fontConfig;
  private $font;
  private $image;
  private $color;
  
  /* methods */
  # constructor
  public function __construct($length=5, $fontConfig=array(30, 0, 10, 40, '#000'), $fontDir='fonts', $dataDir='data', $imgDir='image', $urlPath='') {
    // config
    $this->length     = $length;
    $this->fontDir    = $fontDir;
    $this->dataDir    = $dataDir;
    $this->urlPath    = $urlPath;
    $this->fontConfig = $fontConfig;
    if (!is_writable($dataDir)) chmod($dataDir, 0777);
    
    // load current font (random)
    $font = glob($fontDir.'/*.ttf');
    shuffle($font);
    $this->font = $font[0];
    $this->color = $this->hex2rgb($fontConfig[4]);
    
    // load current background image (random)
    $image = glob($imgDir.'/*.png');
    shuffle($image);
    $this->image = $image[0];
  }
  
  # random string generator
  private function randomString($length = 10) {
    // thanks to Pr07o7yp3 on StackOverflow @ http://stackoverflow.com/questions/4356289/php-random-string-generator (edited by me)
    
    // character base
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    // new string
    $string = '';
    for ($i = 0; $i < $length; $i++) {
      $string .= $chars;
      $string = str_shuffle($string);
    }
    
    // return finally shuffled random string
    return substr($string, 0, $length);
  }
  
  # convert hexadecimal to rgb
  private function hex2rgb($hex) {
    // thanks to c.bavota @ http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
    $hex = str_replace("#", "", $hex);

    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    }
    else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);
    
    return $rgb; // returns an array with the rgb values
  }
  
  # clear captcha images from data path
  public function clear() {
    $return = array();
    $images = glob($this->dataDir.'/captcha_*.png');
    foreach ($images as $img) {
      $return[] = unlink($img);
    }
    if (!in_array(false, $return)) return true;
    else return false;
  }
  
  # validate
  public function validate($var) {
    if (isset($_SESSION['captcha']) && $_SESSION['captcha'] === $var) {
      return true;
    }
    else return false;
  }
  
  
  # captcha image
  public function image() {
    // session
    $_SESSION['captcha'] = $this->randomString($this->length);
    
    // configure image
    $image = imagecreatefrompng($this->image); 
    $color = imagecolorallocate($image, $this->color[0], $this->color[1], $this->color[2]); 
    imagettftext ($image, $this->fontConfig[0], $this->fontConfig[1], $this->fontConfig[2], $this->fontConfig[3], $color, $this->font, $_SESSION['captcha']);  
    
    // cache image
    $file = '/captcha_'.(sha1(time())).'.png';
    $url = $this->dataDir.$file;
    imagepng($image, $url);
    imagedestroy($image);
    return $this->urlPath.$file;
  }
}

?>