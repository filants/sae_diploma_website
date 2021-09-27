<?php

function cleanString ($str) {
    $str = trim($str);
    $str = stripslashes($str);
    $str = filter_var($str, FILTER_SANITIZE_STRING);
    $str = strip_tags($str);
    return $str;
}


?>