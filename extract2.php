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


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $uploadDir = './uploads/';
    $uploadFile = $uploadDir . basename($_FILES['imageFile']['name']);
//    echo '<pre>';
    if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $uploadFile)) {
//        echo "File is valid, and was successfully uploaded.\n";
        file_put_contents('file_info.txt', $uploadFile);
    } else {
//        echo "Possible file upload attack!\n";
    }
//    echo 'Here is some more debugging info:';
//    print_r($_FILES);
//    print "</pre>";
}

$ratio = $_GET['ratio'] ?? 7;
$colorsQuantity = $_GET['colorsQuantity'] ?? 5;

 $sourceImage = 'uploads/' . basename(file_get_contents('file_info.txt'));
 if (!file_exists($sourceImage)) {
     $sourceImage = 'bulb.jpeg';
 }

 $dominantColor = ColorThief::getColor($sourceImage, 1, null, 'hex');
// $palette = ColorThief::getPalette($sourceImage, 8);
// var_dump($sourceImage);die;


$palette = ColorThief::getPalette($sourceImage, $colorsQuantity, 1, null, 'hex');

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
    <link rel="stylesheet" href="mystyle.css">
    <title>Extract colors!</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <?php
                echo '<img src="' . $sourceImage . '" />';
            ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="exampleFormControlFile1">Please upload file</label>
                    <input type="file" class="form-control-file" id="exampleFormControlFile1" name="imageFile">
                </div>
                <button type="submit" class="btn btn-primary">Upload file</button>
            </form>

            <h4 class="mt-5">Configuration</h4>

            <form action="" method="GET">
                <div class="form-group">
                    <label for="colorsQuantity">Colors quantity</label>
                    <input type="text" class="form-control" id="colorsQuantity" aria-describedby="colorsQuantity"
                           placeholder="Enter colors quantity" value="<?=$colorsQuantity?>" name="colorsQuantity">
                    <small id="emailHelp" class="form-text text-muted">Please enter colors quantity</small>
                </div>
                <div class="form-group">
                    <label for="ratio">Ratio</label>
                    <select class="form-control" id="ratio" name="ratio">
                        <option value="7">7</option>
                        <option value="4.5">4.5</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>
        <div class="col-lg-2">


            <h4>Dominant color</h4>
            <table class="table">
                <tr>
                    <td style="background: <?=$dominantColor?>; width:36px;"></td><td><?=$dominantColor?></td>
                </tr>
            </table>
            <h4>Color palette</h4>
            <?php
            echo '<table class="table table-hover">';
            foreach ($palette as $color) {

                echo '<tr><td style="background:' . $color . '; width:36px;"></td><td>' . $color . '</td></tr>';
            }
            echo '</table>';
            ?>

        </div>
        <div class="col-lg-6">
            <h4>The contrast calculation based on the WCAG 2.0.</h4>
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

            echo '<table class="table table-hover">';
            foreach ($combinations as $combination) {


                // echo $combination->getContrast();die;

                // var_dump($combination->getBackground());die;
                // printf("#%s on the Background color #%s has a contrast value of %f \n", $combination->getForeground(), $combination->getBackground(), $combination->getContrast());

                echo '<tr onclick="showProposedColors(this)">
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


            <div id="textExample" style="display: none">
                <h1>Lorem ipsum dolor sit amet.</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus blanditiis dolores eius excepturi omnis quo! Adipisci enim fuga fugiat iste labore omnis, optio voluptas? Dolore esse est ex omnis quia! Aliquam aperiam asperiores atque autem corporis debitis deserunt dolor esse et in ipsum itaque labore minima mollitia nemo officia officiis perferendis, perspiciatis praesentium quia quisquam rerum sint sunt totam ullam ut vitae voluptatem! A aperiam at atque consectetur culpa deleniti earum eius esse eveniet harum, ipsa nesciunt nihil odio optio pariatur quisquam, reiciendis rem temporibus vel voluptatum! Atque commodi deleniti eligendi et facilis maxime numquam odit, omnis quibusdam rerum tenetur?</p>
                <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Expedita, laboriosam, totam! Inventore ipsam magnam officia?</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus adipisci aliquam asperiores aspernatur aut, commodi consectetur corporis dolor doloremque dolores, doloribus eaque eligendi ex explicabo facilis fugit hic illo impedit inventore ipsa maiores modi molestias mollitia natus nobis odio odit officiis omnis praesentium quis reiciendis rem similique soluta voluptate voluptatum.</p>
            </div>

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
<script type="application/javascript">
    function showProposedColors(rowWithColors){
        let colors = rowWithColors.innerHTML.match(/[a-f0-9]{6}/gi)
        let textExample = document.getElementById("textExample");
        textExample.style.backgroundColor = "#" + colors[2];
        textExample.style.color = "#" + colors[0];
        textExample.style.display = "block";
    }
</script>
</body>
</html>
