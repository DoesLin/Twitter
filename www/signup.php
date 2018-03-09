<?php
require '../lib/main.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Controller\User\signup($_POST, $_FILES);
}
else {
    Controller\User\signup_page();
}
require '../lib/closure.php';
