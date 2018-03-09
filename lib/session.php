<?php
namespace Session;
session_start();
function get_session_infos() {
    return array(
        "id" => $_SESSION["id"],
        "password" => $_SESSION["password"]
    );
}

function set_session_infos($id, $password) {
    $_SESSION["id"] = $id;
    $_SESSION["password"] = $password;
}

function set_password($password) {
    $_SESSION["password"] = $password;
}

function destroy() {
    session_destroy();
}

function get_user() {
    if (isset($_SESSION["id"]) && isset($_SESSION["password"])) {
        return \Model\User\check_auth_id($_SESSION["id"], $_SESSION["password"]);
    }
    return null;
}

function is_authentificated() {
    return get_user() !== null;
}

function set_error($content) {
    set_modal("error", $content);
}

function set_success($content) {
    set_modal("success", $content);
}

function set_info($content) {
    set_modal("info", $content);
}

function set_modal($type, $content) {
    $_SESSION["modal_type"] = $type;
    $_SESSION["modal"] = $content;
}

function get_modal() {
    if (isset($_SESSION["modal"])) {
        $m = (object) array(
            "type" => $_SESSION["modal_type"],
            "content" => $_SESSION["modal"]
        );
        unset($_SESSION["modal"]);
        unset($_SESSION["modal_type"]);
        return $m;
    }
    return null;
}
