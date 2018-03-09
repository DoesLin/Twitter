<?php
namespace Controller\Post;

function post_page($id) {
    try {
        $post = \Model\Post\get_with_joins($id);
        if(!$post) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return;
        }
        $responses = \Model\Post\get_responses($id);
        $stats = \Model\Post\get_stats($id);
        $user = \Session\get_user();
        if($user) {
            $liked = in_array($user->id, array_map(function($el) { return $el->id; }, $post->likes));
        }
        foreach($responses as $response) {
            $response->responses = \Model\Post\get_responses($response->id);
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
    require '../view/post.php';
}

function post($form) {
    try {
        $user = \Session\get_user();
        if(!$user) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        }
        else {
            extract($form);
            if($pid = \Model\Post\create($user->id, $text)) {
                \Session\set_success("Your twirp has been published.");
                header("Location: post.php?id=".$pid);
            }
            else {
                \Session\set_error("An error occured while trying to publish your twirp.");
                header("Location: index.php");
            }
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function respond($id, $form) {
    try {
        $post = \Model\Post\get($id);
        if(!$post) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return;
        }
        $user = \Session\get_user();
        if(!$user) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
            return;
        }
        extract($form);
        if(\Model\Post\create($user->id, $text, $id)) {
            \Session\set_success("Your response has been published.");
            header("Location: post.php?id=".$id);
        }
        else {
            \Session\set_error("An error occured while trying to publish your twirp.");
            header("Location: post.php?id=".$id);        
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function destroy($id) {
    try {
        $post = \Model\Post\get($id);
        if(!$post) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return;
        }
        $user = \Session\get_user();
        if(!$user || $user->id !== $post->author->id) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
            return;
        }
        \Model\Post\destroy($id);
        \Session\set_success("Your twirp has been deleted.");
        header("Location: index.php");
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function like($id) {
    try {
        $post = \Model\Post\get($id);
        if(!$post) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return;
        }
        $user = \Session\get_user();
        if(!$user) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
            return;
        }
        \Model\Post\like($user->id, $id);
        \Session\set_success("Your like was counted.");
        header("Location: post.php?id=".$id);
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function unlike($id) {
    try {
        $post = \Model\Post\get($id);
        if(!$post) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            return;
        }
        $user = \Session\get_user();
        if(!$user) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
            return;
        }
        \Model\Post\unlike($user->id, $id);
        \Session\set_success("Your like was removed.");
        header("Location: post.php?id=".$id);
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

