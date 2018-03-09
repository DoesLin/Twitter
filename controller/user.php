<?php
namespace Controller\User;

function user_page($username) {
    try {
        $user = \Model\User\get_by_username($username);
        if(!$user) {
            \Session\set_error("The user you've been searching for doesn't exist.");
            header("Location: index.php");
            return;
        }
        $stats = \Model\User\get_stats($user->id);
        $posts = \Model\Post\list_user_posts($user->id);
        $followings = \Model\User\get_followings($user->id);
        $followers = \Model\User\get_followers($user->id);
        if (\Session\is_authentificated()) {
            $me = \Session\get_user();
            $editable = $me->id == $user->id;
            $followable = !$editable;
            if ($followable) {
                $followed = in_array($me->id, array_map(function($u) { return $u->id; }, $followers));
            }
        }
        else {
            $editable = false;
            $followable = false;
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
    require "../view/user.php";
}

function follow($username) {
    try {
        if(!\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }
        $user = \Session\get_user();
        $to_follow = \Model\User\get_by_username($username);
        if(!$to_follow) {
            \Session\set_error("The user you've been searching for doesn't exist.");
            header("Location: index.php");
            return;
        }
        \Model\User\follow($user->id, $to_follow->id);
        \Session\set_success("You are following " . $to_follow->username . ".");
        header("Location: user.php?username=".$to_follow->username);
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function unfollow($username) {
    try {
        if(!\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }
        $user = \Session\get_user();
        $to_unfollow = \Model\User\get_by_username($username);
        if(!$to_unfollow) {
            \Session\set_error("The user you've been searching for doesn't exist.");
            header("Location: index.php");
            return;
        }
        \Model\User\unfollow($user->id, $to_unfollow->id);
        \Session\set_success("You unfollowed " . $to_unfollow->username . ".");
        header("Location: user.php?username=".$to_unfollow->username);
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function profile() {
    try {
        if(!\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }

        $user = \Session\get_user();
        $posts = \Model\Post\list_user_posts($user->id);
        $stats = \Model\User\get_stats($user->id);
        $followings = \Model\User\get_followings($user->id);
        $followers = \Model\User\get_followers($user->id);
        $editable = true;
        $followable = false;
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
    require "../view/user.php";
}

function signup_page() {
    try {
        if(\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }

    require "../view/signup.php";
}

function signup($form, $files) {
    try {
        if(\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }

        extract($form);
        if(\Model\User\get_by_username($username)) {
            \Session\set_error("There was an error during signup : the username you've chosen has already been taken.");
            header("Location: signup.php");
            return;
        }
        if($files["avatar"]["error"] != \UPLOAD_ERR_NO_FILE) {
            if($files["avatar"]["error"] != \UPLOAD_ERR_OK) {
                \Session\set_error("There was an error during signup : the avatar couldn't be uploaded.");
                header("Location: signup.php");
                return;
            }
            if (($avatar = \Model\User\handle_avatar($files["avatar"]["name"], $files["avatar"]["tmp_name"], $username)) === null) {
                \Session\set_error("There was an error during signup : the avatar couldn't be uploaded.");
                header("Location: signup.php");
                return;
            }
        }
        else {
            $avatar = "";
        }
        if(\Model\User\create($username, $name, $password, $email, $avatar)) {
            \Session\set_success("Your account has been created. You can log in !");
            header("Location: index.php");
        }
        else {
            \Session\set_error("There was an error during signup.");
            header("Location: signup.php");
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function login($username, $password) {
    try {
        if(\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }

        $u = \Model\User\check_auth($username, $password);
        if($u) {
            \Session\set_session_infos($u->id, \Model\User\hash_password($password));
            header("Location: index.php");
        }
        else {
            \Session\set_error("Login failed.");
            header("Location: login.php");
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function login_page() {
    try {
        if(\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }

    require "../view/login.php";
}

function logout() {
    try {
        \Session\destroy();
        header("Location: index.php");
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function update_profile($form, $files) {
    try {
        if(!\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }

        extract($form);
        $u = \Session\get_user();
        if($username != $u->username && \Model\User\get_by_username($username)) {
            \Session\set_error("There was an error during profile update : the username you've chosen has already been taken.");
            header("Location: update_profile.php");
            return;
        }
        if ($password !== '') {
            \Model\User\change_password($u->id, $password);
            \Session\set_password(\Model\User\hash_password($password));
        }
        if($files["avatar"]["error"] != \UPLOAD_ERR_NO_FILE) {
            if($files["avatar"]["error"] != \UPLOAD_ERR_OK) {
                \Session\set_error("There was an error during signup : the avatar couldn't be uploaded.");
                header("Location: signup.php");
                return;
            }
            if (($avatar = \Model\User\handle_avatar($files["avatar"]["name"], $files["avatar"]["tmp_name"], $username)) === null) {
                \Session\set_error("There was an error during signup : the avatar couldn't be uploaded.");
                header("Location: signup.php");
                return;
            }
            \Model\User\change_avatar($u->id, $avatar);
        }
        \Model\User\modify($u->id, $username, $name, $email);
        \Session\set_success("Profile has been modified successfully");
        header("Location: update_profile.php");
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
}

function update_profile_page() {
    try {
        if(!\Session\is_authentificated()) {
            header("Location: index.php");
            return;
        }

        $user = \Session\get_user();
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
    require "../view/update_profile.php";
}

