<?php
require '../lib/main.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    \Controller\User\login($_POST["username"], $_POST["password"]);    
}
else {
    \Controller\User\login_page();
}
require '../lib/closure.php';
