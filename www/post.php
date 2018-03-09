<?php
require '../lib/main.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['id'])) {
        Controller\Post\respond($_GET['id'], $_POST);
    }
    else {
        Controller\Post\post($_POST);
    }
}
elseif(isset($_GET['id'])) {
    if(isset($_GET['like'])) {
        Controller\Post\like($_GET['id']);
    }
    else if(isset($_GET['unlike'])) {
        Controller\Post\unlike($_GET['id']);
    }
    else if(isset($_GET['destroy'])) {
        Controller\Post\destroy($_GET['id']);
    }
    else {
        Controller\Post\post_page($_GET['id']);
    }
}
else {
    \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
}
require '../lib/closure.php';
