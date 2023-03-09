<?php
/**
 * 
 * https://bookofzeus.com/articles/php/get-the-color-palette-for-an-image-using-php/
 * 
 * https://www.youtube.com/watch?v=apWba5R1upc
 */

function detectColors($image, $num, $level = 5) {
    $level = (int)$level;
    $palette = array();
    $size = getimagesize($image);
    // var_dump($size);
    if(!$size) {
      return FALSE;
    }
    switch($size['mime']) {
      case 'image/jpeg':
        $img = imagecreatefromjpeg($image);
        break;
      case 'image/png':
        $img = imagecreatefrompng($image);
        break;
      case 'image/gif':
        $img = imagecreatefromgif($image);
        break;
      default:
        return FALSE;
    }
    if(!$img) {
      return FALSE;
    }
    for($i = 0; $i < $size[0]; $i += $level) {
      for($j = 0; $j < $size[1]; $j += $level) {
        $thisColor = imagecolorat($img, $i, $j);
        $rgb = imagecolorsforindex($img, $thisColor);
        $color = sprintf('%02X%02X%02X', (round(round(($rgb['red'] / 0x33)) * 0x33)), round(round(($rgb['green'] / 0x33)) * 0x33), round(round(($rgb['blue'] / 0x33)) * 0x33));
        $palette[$color] = isset($palette[$color]) ? ++$palette[$color] : 1;
      }
    }
    arsort($palette);
    return array_slice(array_keys($palette), 0, $num);
  }
  
  $img = 'download_1.png';
  $palette = detectColors($img, 100, 5);
//   var_dump($palette);die;

  echo '<img src="' . $img . '" />';
  echo '<table>';
  foreach($palette as $color) {
    echo '<tr><td style="background:#' . $color . '; width:36px;"></td><td>#' . $color . '</td></tr>';
  }
  echo '</table>';
