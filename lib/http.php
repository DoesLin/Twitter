<?php
function headerm($message, $b, $code) {
    header($message, $b, $code);
    print($message);
}

