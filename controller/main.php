<?php
namespace Controller\Main;
function main() {
    try {
        $user = \Session\get_user();
        if($user) {
            $posts = array();
            $f = array_map(function($u){
            return $u->id;
            }, \Model\User\get_followings($user->id));
            $all_posts = \Model\Post\list_all("DESC");
            foreach($all_posts as $p) {
            if($p->author->id == $user->id || in_array($p->author->id, $f)) {
                $posts[] = $p;
            }
            }
        }
        else {
            $posts = \Model\Post\list_all("DESC");
        }
        $popular_h = \Model\Hashtag\list_popular_hashtags(10);
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
    require "../view/timeline.php";
}
