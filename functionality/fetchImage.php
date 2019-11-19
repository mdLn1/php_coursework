<?php
    function getImage($sqliDb,$image_id){
    $sql = 'SELECT img_name,img_type,img FROM images WHERE pic_id="' . $image_id . '"';
    $result = $sqliDb->query($sql);
    $row = $result->fetch_assoc();
    return "<img class='extra-img' src='data:". trim($row["img_type"]) . ";base64," . base64_encode($row["img"]) . "' alt='" . $row["img_name"] . "' />";
    }
?>