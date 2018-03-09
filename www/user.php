<?php
require "../lib/main.php";
if(!isset($_GET["username"])) {
    header("Location: index.php");
}
elseif(isset($_GET["follow"])) {
    Controller\User\follow($_GET["username"]);
}
elseif(isset($_GET["unfollow"])) {
    Controller\User\unfollow($_GET["username"]);
}
else {
    Controller\User\user_page($_GET["username"]);
}
require "../lib/closure.php";
