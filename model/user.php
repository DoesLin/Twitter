<?php
namespace Model\User;

function handle_avatar($filename, $tmpfilename, $username) {
    $allowed =  array('gif','png' ,'jpg');
    $ext = \pathinfo($filename, \PATHINFO_EXTENSION);
    if(!in_array($ext,$allowed) ) {
        return null;
    }
    $newname = 'images/'.$username.'.pic.'.$ext;
    move_uploaded_file($tmpfilename, $newname);
    return $newname;
}
