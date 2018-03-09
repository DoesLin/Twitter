<?php
require '../lib/main.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Controller\User\update_profile($_POST, $_FILES);
}
else {
    Controller\User\update_profile_page();
}
require '../lib/closure.php';
