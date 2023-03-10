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

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $uploadDir = __DIR__ . '/uploads/';
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
$palette = ColorThief::getPalette($sourceImage, $colorsQuantity, 1, null, 'hex');

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
                    <input type="number" min="3" max="20" class="form-control" id="colorsQuantity"
                           aria-describedby="colorsQuantity"
                           placeholder="Enter colors quantity" value="<?= $colorsQuantity ?>" name="colorsQuantity">
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
                    <td style="background: <?= $dominantColor ?>; width:36px;"></td>
                    <td><?= $dominantColor ?></td>
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
            try {
                $contrast->addColors($palette);
            } catch (\ColorContrast\Exception\InvalidColorException $e) {
                die($e->getMessage());
            }
            $combinations = $contrast->getCombinations($ratio);


            echo '<table class="table table-hover">';
            foreach ($combinations as $combination) {
                echo '<tr onclick="showProposedColors(this)">
                <td style="background: #' . $combination->getForeground() . '; width:36px;"></td><td> Text color #' . $combination->getForeground() . ' on the Background color </td>
                <td style="background: #' . $combination->getBackground() . '; width:36px;"></td><td>#' . $combination->getBackground() . '</td>
                <td>has a contrast value of ' . $combination->getContrast() . '</td>
              </tr>';
            }
            echo '</table>';


            /**
             * https://packagist.org/packages/harmstyler/contrast-ratio-calculator
             */

            use HarmsTyler\ContrastRatioCalculator\Color;
            use HarmsTyler\ContrastRatioCalculator\ContrastRatio;
            use HarmsTyler\ContrastRatioCalculator\WCAGContrastRating;

            echo '<h4>Lib 1: Contrast ratios of colors as well as rates the contrast ratio against WCAG standards.</h4><table class="table table-bordered">';
            $colorsCount = count($palette);
            $rowCount = 1;
            foreach ($palette as $item) {
                for ($i = 0; $i < $colorsCount; $i++) {
                    echo '';
                    $primaryColor = new Color();
                    $secondaryColor = new Color();
                    $primaryColor->setHex($item);
                    $secondaryColor->setHex($palette[$i]);
                    $contrastRatio = new ContrastRatio($primaryColor, $secondaryColor);
                    $rating = new WCAGContrastRating();
                    if($rating->rateContrastRatio($contrastRatio) === WCAGContrastRating::AAA ||
                        $rating->rateContrastRatio($contrastRatio) === WCAGContrastRating::AA &&
                        $contrastRatio->getRatio() >= $ratio) {
                        echo '<tr>
                                <td>' . $rowCount . '</td>
                                <td style="background:' . $primaryColor->getHex() . '; width:36px;"></td><td>' . $primaryColor->getHex() . '</td>
                                <td style="background:' . $secondaryColor->getHex() . '; width:36px;"></td><td>' . $secondaryColor->getHex() . '</td>
                                <td>Calculated ratio: ' . $contrastRatio->getRatio() . '</td>
                                <td>WCAGContrast grade: <b>' . $rating->rateContrastRatio($contrastRatio) . '</b></td>
                              </tr>';
                        }
                    ++$rowCount;
                }
            }
            echo '</table>';


            //            /**
            //             * https://github.com/breadthe/php-contrast
            //             */
            //
            //            use Breadthe\PhpContrast\HexColor;
            //
            //            // factory
            //            use Breadthe\PhpContrast\HexColorPair;
            //
            //            // hex pair utilities
            //            use Breadthe\PhpContrast\TailwindColor;
            //
            //            // Tailwind color pair utilities
            //
            //            echo '<h4>Lib 2: Contrast ratios of colors as well as rates the contrast ratio against WCAG standards.</h4><table class="table table-bordered">';
            //            $colorsCount = count($palette);
            //            for ($i = 0; $i <= $colorsCount; $i++) {
            //                if ($i + 1 === $colorsCount) {
            //                    break;
            //                }
            //                $hexColorPair = HexColorPair::make(HexColor::make($palette[$i]), HexColor::make($palette[$i + 1]));
            //                echo '<tr>
            //                        <td style="background:' . $hexColorPair->fg->hex . '; width:36px;"></td><td>' . $hexColorPair->fg->hex . '</td>
            //                        <td style="background:' . $hexColorPair->bg->hex . '; width:36px;"></td><td>' . $hexColorPair->bg->hex . '</td>
            //                        <td>Calculated ratio: ' . $hexColorPair->ratio . '</td>
            //                      </tr>';
            //
            //                $color = HexColorPair::minContrast(4.5)->getSibling($palette[$i])->hex;
            //                //Get a random accessible sibling for the given color, with minimum specified contrast ratio 4.5
            ////                echo '<tr>
            ////                        <td style="background:' . $palette[$i] . '; width:36px;"></td><td>' . $palette[$i] . '</td>
            ////                        <td style="background:' . $color . '; width:36px;"></td><td>' . $color . '</td>
            ////                      </tr>';
            //
            //            }


            ?>


            <div id="textExample" style="display: none">
                <h1>Lorem ipsum dolor sit amet.</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus blanditiis dolores eius excepturi
                    omnis quo! Adipisci enim fuga fugiat iste labore omnis, optio voluptas? Dolore esse est ex omnis
                    quia! Aliquam aperiam asperiores atque autem corporis debitis deserunt dolor esse et in ipsum itaque
                    labore minima mollitia nemo officia officiis perferendis, perspiciatis praesentium quia quisquam
                    rerum sint sunt totam ullam ut vitae voluptatem! A aperiam at atque consectetur culpa deleniti earum
                    eius esse eveniet harum, ipsa nesciunt nihil odio optio pariatur quisquam, reiciendis rem temporibus
                    vel voluptatum! Atque commodi deleniti eligendi et facilis maxime numquam odit, omnis quibusdam
                    rerum tenetur?</p>
                <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Expedita, laboriosam, totam! Inventore
                    ipsam magnam officia?</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus adipisci aliquam asperiores
                    aspernatur aut, commodi consectetur corporis dolor doloremque dolores, doloribus eaque eligendi ex
                    explicabo facilis fugit hic illo impedit inventore ipsa maiores modi molestias mollitia natus nobis
                    odio odit officiis omnis praesentium quis reiciendis rem similique soluta voluptate voluptatum.</p>
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
    function showProposedColors(rowWithColors) {
        let colors = rowWithColors.innerHTML.match(/[a-f0-9]{6}/gi)
        let textExample = document.getElementById("textExample");
        textExample.style.backgroundColor = "#" + colors[2];
        textExample.style.color = "#" + colors[0];
        textExample.style.display = "block";
    }
</script>
</body>
</html>
