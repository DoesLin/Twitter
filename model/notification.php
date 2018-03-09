<?php
namespace Model\Notification;
use \Db;
use \PDOException;
/**
 * Notification model
 *
 * This file contains every db action regarding the notifications
 */

/**
 * Get all the notifications sorted by time (descending order)
 * @param uid the user id
 * @return a sorted list of every notifications objects
 */
function list_all_notifications($uid) {
    $ary = array_merge(get_liked_notifications($uid), get_followed_notifications($uid), get_mentioned_notifications($uid));
    usort(
        $ary,
        function($a, $b) {
            return $b->date->format('U') - $a->date->format('U');
        }
    );
    return $ary;
}


/**
 * Mark a notification as read (with date of reading)
 * @param uid the user to whom modify the notifications
 * @param notification the notification object to mark as seen
 */
function notification_seen($uid, $notification) {
    switch($notification->type) {
        case "liked":
            return liked_notification_seen($notification->post->id, $notification->liked_by->id);
        break;
        case "mentioned":
            return mentioned_notification_seen($uid, $notification->post->id);
        break;
        case "followed":
            return followed_notification_seen($uid, $notification->user->id);
        break;
    }
}
