<?php

/**
 * https://github.com/davidgorges/color-contrast-php
 *
 * https://github.com/breadthe/php-contrast
 *
 */

require_once 'vendor/autoload.php';

use ColorContrast\ColorContrast;
use ColorThief\ColorThief;


// use Breadthe\PhpContrast\HexColor; // factory

// use Breadthe\PhpContrast\HexColorPair; // hex pair utilities

// use Breadthe\PhpContrast\TailwindColor; // Tailwind color pair utilities


// use HarmsTyler\ContrastRatioCalculator\Color;
// use HarmsTyler\ContrastRatioCalculator\ContrastRatio;
// use HarmsTyler\ContrastRatioCalculator\WCAGContrastRating;

// $primaryColor = new Color();
// $primaryColor->setHex('#ecaada');
// $secondaryColor = new Color();
// $secondaryColor->setHex('#000000');

// $contrastRatio = new ContrastRatio($primaryColor, $secondaryColor);

// echo $contrastRatio->getRatio(); // floating decimal point of calculated ratio

// $rating = new WCAGContrastRating();
// echo $rating->rateContrastRatio($contrastRatio); // the WCAGContrast grade, either 'fail', 'aa-large', 'aa', or 'aaa'
// die("STOP");
// print_r($_POST);
print_r($_FILES);

$sourceImage = null;
$ratio = $_GET['ratio'] ?? 7;
$colorsQuantity = $_GET['colorsQuantity'] ?? 10;


$sourceImage = $_FILES['full_path'] ?? null;


// $ratio = match(){
//   ColorContrast::MIN_CONTRAST_AAA => 7,
//   ColorContrast::MIN_CONTRAST_AA_LARGE => 3,
//   ColorContrast::MIN_CONTRAST_AAA_LARGE => 4.5,
//   default =>7
// }


 $sourceImage = 'download_1.png';
//$sourceImage = 'my_image.jpeg';
// $sourceImage = $file;


// $dominantColor = ColorThief::getColor($sourceImage);
// var_dump($dominantColor);


// $palette = ColorThief::getPalette($sourceImage, 8);
// var_dump($sourceImage);die;
$palette = ColorThief::getPalette($sourceImage, $colorCount = $colorsQuantity, $quality = 1, $area = null, $outputFormat = 'hex', null);

// var_dump($palette);


?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Extract colors!</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col">


            <!-- <form action="" method="GET" enctype="multipart/form-data"> -->
            <form action="" method="GET">
                <div class="form-group">
                    <label for="colorsQuantity">Colors quantity</label>
                    <input type="text" class="form-control" id="colorsQuantity" aria-describedby="colorsQuantity"
                           placeholder="Enter colors quantity" value="<?=$colorsQuantity?>" name="colorsQuantity">
                    <small id="emailHelp" class="form-text text-muted">Please enter colors quantity</small>
                </div>

                <!-- <div class="form-group">
                  <label for="exampleFormControlFile1">Example file input</label>
                  <input type="file" class="form-control-file" id="exampleFormControlFile1" name="imageFile">
                </div> -->

                <div class="form-group">
                    <label for="ratio">Ratio</label>
                    <select class="form-control" id="ratio" name="ratio">
                        <option value="7">7</option>
                        <option value="4.5">4.5</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <!-- <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="exampleCheck1">
                  <label class="form-check-label" for="exampleCheck1">Check me out</label>
                </div> -->
                <button type="submit" class="btn btn-primary">Submit</button>
                <!-- <input type="submit" class="btn btn-primary"> -->
            </form>

            <div class="row">
                <div class="col-lg-8">
                    <?php
                        echo '<img src="' . $sourceImage . '" />';
                    ?>
                </div>
                <div class="col-lg-4">
                    <h4>Color palette</h4>
                    <?php
                    echo '<table>';
                    foreach ($palette as $color) {

                        echo '<tr><td style="background:' . $color . '; width:36px;"></td><td>' . $color . '</td></tr>';
                    }
                    echo '</table>';
                    ?>
                </div>
            </div>

        </div>
        <div class="col">
            <?php


            $contrast = new ColorContrast();
            $contrast->addColors($palette);
            $combinations = $contrast->getCombinations($ratio);


            // var_dump($combinations);die;

            // $result = [];
            // foreach ($combinations as $combination) {
            //   $result[] = [
            //     'color' => $combination->getForeground(),
            //     'background' => $combination->getBackground(),
            //     'contrast' => $combination->getContrast()
            //   ];
            // }


            // var_dump($result);die;
            // arsort($combinations);

            echo '<table class="table">';
            foreach ($combinations as $combination) {


                // echo $combination->getContrast();die;

                // var_dump($combination->getBackground());die;
                // printf("#%s on the Background color #%s has a contrast value of %f \n", $combination->getForeground(), $combination->getBackground(), $combination->getContrast());

                echo '<tr>
                <td style="background: #' . $combination->getForeground() . '; width:36px;"></td><td> Text color #' . $combination->getForeground() . ' on the Background color </td>
                <td style="background: #' . $combination->getBackground() . '; width:36px;"></td><td>#' . $combination->getBackground() . '</td>
                <td>has a contrast value of ' . $combination->getContrast() . '</td>
              </tr>';
                // printf("#%s on the Background color #%s has a contrast value of %f \n", $combination->getForeground(), $combination->getBackground(), $combination->getContrast());
            }
            echo '</table>';

            // $hexColorPair = HexColorPair::make(HexColor::make('000000'), HexColor::make('ffffff'));
            // $hexColorPair->ratio; // 21
            // $hexColorPair->fg; // '#000000'
            // $hexColorPair->bg; // '#ffffff'

            // $twColor = TailwindColor::random();
            // $twColor->hex; // '#e2e8f0'
            // $twColor->name; // 'gray-300'

            // $twColorpair = TailwindColor::minContrast(4.5)->getRandomPair();
            // $twColorpair->ratio; // 7.0
            // $twColorpair->fg->hex; // '#faf5ff'
            // $twColorpair->fg->name; // 'purple-100'
            // $twColorpair->bg->hex; // '#9b2c2c'
            // $twColorpair->bg->name; // 'red-800'


            // // var_dump($twColorpair);


            // // Minimum 3:1 contrast ratio

            // $siblings = [];
            // // $color = '1a91d2';
            // for($i=0; $i<10; $i++){
            //   $siblings[] = HexColorPair::sibling('1a91d2')->hex;
            //   $siblings[] = HexColorPair::sibling('1a91d2')->hex;
            //   $siblings[] = HexColorPair::sibling('1a91d2')->hex;
            //   $siblings[] = HexColorPair::sibling('1a91d2')->hex;
            //   $siblings[] = HexColorPair::sibling('1a91d2')->hex;
            // }
            // // print_r($siblings);

            // echo '<table>';
            // foreach($siblings as $color) {

            //   echo '<tr><td style="background:' . $color . '; width:36px;"></td><td>' . $color . '</td></tr>';
            // }
            // echo '</table>';


            // var_dump($x);
            // // Minimum specified contrast ratio (no less than 3:1)
            // $y = HexColorPair::minContrast(4.5)->getSibling('000000')->hex; // '#ffffff'

            // var_dump($y);

            ?>
        </div>
    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>
