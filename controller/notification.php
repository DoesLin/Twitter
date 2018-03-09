<?php
namespace Controller\Notification;

function notification_page() {
    try {
        $user = \Session\get_user();
        if(!$user) {
            \headerm($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
            return;
        }
        $notifications = \Model\Notification\list_all_notifications($user->id);
        foreach($notifications as $notification) {
            \Model\Notification\notification_seen($user->id, $notification);
        }
    }
    catch(\Exception $e) {
        \display_exception($e);
        exit();
    }
    
    require '../view/notification.php';
}
