<?php
require '../lib/main.php';
if (isset($_GET["query"])) {
    if(isset($_GET["post"])) {
        Controller\Search\search_post($_GET["query"]);
    }
    elseif(isset($_GET["user"])) {
        Controller\Search\search_user($_GET["query"]);
    }
    else {
        headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    }
}
else {
    \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
}

require '../lib/closure.php';
