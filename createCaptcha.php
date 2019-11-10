<?php
# read background image
function makeImgCaptcha()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $image = ImageCreateFromPng("resources/captcha100x40.png");

    # randomise colour for text
    $red = rand(80, 130);
    $green = rand(80, 130);
    $blue = 320 - $red - $green;
    $textColour = ImageColorAllocate($image, $red, $green, $blue);

    # randomly select character array
    $charArray = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '6', '7', '8', '9');
    shuffle($charArray);
    $captchaString = $charArray[0];
    $letters = $charArray[0];
    ob_start();
    for ($i = 1; $i < 5; $i++) {
        $captchaString .= ' ' . $charArray[$i];
        $letters .= $charArray[$i];
    }
    $_SESSION["captcha"] = $letters;
    # Edit and output the image
    $x = rand(3, 18);
    $y = rand(3, 18);
    ImageString($image, 5, $x, $y, $captchaString, $textColour);
    $bigImage = imagecreatetruecolor(200, 80);
    imagecopyresized($bigImage, $image, 0, 0, 0, 0, 200, 80, 100, 40);
    Imagejpeg($bigImage, NULL, 8);
    ImageDestroy($image);
    ImageDestroy($bigImage);
    $i = ob_get_clean();
    return base64_encode($i);
}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"])) {
    echo json_encode(array("imageCode" => makeImgCaptcha()));
}
