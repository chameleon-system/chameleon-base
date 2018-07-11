<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$jpegImage = dirname(__FILE__).'/playerdummy.jpg';

if (empty($_GET['width']) || empty($_GET['height'])) {
    header('Content-type: image/jpeg');
    $im = imagecreatefromjpeg($jpegImage);
    imagejpeg($im);
} else {
    // Content type
    header('Content-type: image/jpeg');

    if (!empty($_GET['desc'])) {
        $imageDesc = $_GET['desc'];
    } else {
        $imageDesc = 'Titel unbekannt';
    }

    // Get new sizes
    list($width, $height) = getimagesize($jpegImage);
    $newwidth = $_GET['width'];
    $newheight = $_GET['height'];

    // Load
    $outputImage = imagecreatetruecolor($newwidth, $newheight);
    $im = imagecreatefromjpeg($jpegImage);

    // Resize
    imagecopyresized($outputImage, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    $bgc = imagecolorallocate($outputImage, 222, 225, 225);
    $tc = imagecolorallocate($outputImage, 0, 0, 0);
    imagefilledrectangle($outputImage, 0, 0, $newwidth, 20, $bgc);
    /* Output an errmsg */
    imagestring($outputImage, 2, 5, 5, $imageDesc, $tc);

    // Output
    imagejpeg($outputImage);
}
