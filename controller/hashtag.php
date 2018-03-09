<?php
namespace Controller\Hashtag;

function hashtag_page($name) {
    try {    
        $posts = \Model\Hashtag\get_posts($name);
        if(!$posts) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return;
        }
        $related_h = \Model\Hashtag\get_related_hashtags($name, 10);
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }

    require "../view/hashtag.php";
}
