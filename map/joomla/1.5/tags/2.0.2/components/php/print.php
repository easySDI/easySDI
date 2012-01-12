<?php
  $TEMP_DIR = 'C:\www\Site\Joomla\tmp';
  $TEMP_URL = 'tmp/';
  // fetch the request params, and generate the name of the tempfile and its URL
  $width    = @$_REQUEST['width'];  if (!$width) $width = 1024;
  $height   = @$_REQUEST['height']; if (!$height) $height = 768;
  $tiles    = json_decode(@$_REQUEST['tiles']);
  //$tiles    = json_decode(stripslashes(@$_REQUEST['tiles'])); // use this if you use magic_quotes_gpc
  $random   = md5(microtime().mt_rand());
  $file     = sprintf("%s/%s.jpg", $TEMP_DIR, $random );
  $url      = sprintf("%s/%s.jpg", $TEMP_URL, $random );

  // lay down an image canvas
  // Notice: in MapServer if you have set a background color
  // (eg. IMAGECOLOR 60 100 145) that color is your transparent value
  // $transparent = imagecolorallocatealpha($image,60,100,145,127);
  $image = imagecreatetruecolor($width,$height);
  imagefill($image,0,0, imagecolorallocate($image,255,255,255) ); // fill with white
  $transparent = imagecolorallocatealpha($image,255,255,255,127);
  imagealphablending($image,true);


  // loop through the tiles, blitting each one onto the canvas
  foreach ($tiles as $tile) {
     // try to convert relative URLs into full URLs
     // this could probably use some improvement
     $tile->url = urldecode($tile->url);
     if (substr($tile->url,0,4)!=='http') {
        $tile->url = preg_replace('/^\.\//',dirname($_SERVER['REQUEST_URI']).'/',$tile->url);
        $tile->url = preg_replace('/^\.\.\//',dirname($_SERVER['REQUEST_URI']).'/../',$tile->url);
        $tile->url = sprintf("%s://%s:%d/%s", isset($_SERVER['HTTPS'])?'https':'http', $_SERVER['SERVER_ADDR'], $_SERVER['SERVER_PORT'], $tile->url);
     }
     $tile->url = str_replace(' ','+',$tile->url);

     // fetch the tile into a temp file, and analyze its type; bail if it's invalid
     $tempfile =  sprintf("%s/%s.img", TEMP_DIR, md5(microtime().mt_rand()) );
     file_put_contents($tempfile,file_get_contents($tile->url));
     list($tilewidth,$tileheight,$tileformat) = @getimagesize($tempfile);
     if (!$tileformat) continue;

     // load the tempfile's image, and blit it onto the canvas
     switch ($tileformat) {
        case IMAGETYPE_GIF:
           $tileimage = imagecreatefromgif($tempfile);
           break;
        case IMAGETYPE_JPEG:
           $tileimage = imagecreatefromjpeg($tempfile);
           break;
        case IMAGETYPE_PNG:
           $tileimage = imagecreatefrompng($tempfile);
           break;
     }
    imagealphablending($tileimage,true);
    imagecopy($image, $tileimage, $tile->x, $tile->y, 0, 0, $tilewidth, $tileheight);
  }

  // save to disk and tell the client where they can pick it up
  imagejpeg($image,$file);
  
  print $url;
  ?>
