<?php
namespace Model\Notification;
use \Db;
use \PDOException;
use \PDO;
/**
 * Notification model
 *
 * This file contains every db action regarding the notifications
 */

/**
 * Get a liked notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the liked_by attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the reading_date attribute is either a DateTime object or null (if it hasn't been read)
 */
function get_liked_notifications($uid) {
	try {
    $db = \Db::dbc();
    $sql="SELECT IDTWEET, A.IDUSER, DATEAIME, LUAIME FROM TWEET T INNER JOIN AIMER A USING(IDTWEET) WHERE T.IDUSER = :uid";
    $stmt=$db->prepare($sql);
    $stmt->execute(array(':uid'=>$uid));

    $results = $stmt->fetchAll();
    if($results==false) {
      $aimeNoti=(array) NULL;
    } else {
      foreach($results as $result) {
        $aimeNoti[]=(object) array(
              			"type"=>"liked",
                    "post"=>\Model\Post\get($result["IDTWEET"]),
                    "liked_by"=>\Model\User\get($result["IDUSER"]),
                    "date"=>new \DateTime($result["DATEAIME"]),
                    "reading_date"=>$result["LUAIME"] == NULL ? NULL : new \DateTime($result["LUAIME"])
        );
      }
    }

		$db = NULL;
		return $aimeNoti;
  } catch (\PDOException $e) {
    $db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Mark a like notification as read (with date of reading)
 * @param pid the post id that has been liked
 * @param uid the user id that has liked the post
 */
function liked_notification_seen($pid, $uid) {
	try{
		$db = \Db::dbc();

		$sql = "UPDATE `AIMER` SET `LUAIME`= CURRENT_TIMESTAMP WHERE `IDUSER` = :iduser AND `IDTWEET` = :idtweet";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute(
			array(
				':iduser' => $uid,
				':idtweet' => $pid
			)
		);
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
	$db = NULL;
}

/**
 * Get a mentioned notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the mentioned_by attribute is a user object
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_mentioned_notifications($uid) {
  try {
    $db = \Db::dbc();
  	$sql="SELECT IDTWEET, T.IDUSER, DATEPUB, LUMENT FROM TWEET T INNER JOIN MENTIONNER M USING(IDTWEET) WHERE M.IDUSER=:uid";
    $stmt=$db->prepare($sql);
    $stmt->execute(array(':uid'=>$uid));

  	$results = $stmt->fetchAll();
    if($results == false) {
      $mentNoti=(array) NULL;
    } else {
      foreach($results as $result) {
        $mentNoti[]=(object) array(
                		"type"=>"mentioned",
                    "post"=>\Model\Post\get($result[0]),
                    "mentioned_by"=>\Model\User\get($result[1]),
                    "date"=>new \DateTime($result[2]),
                    "reading_date"=>$result[3] == NULL ? NULL : new \DateTime($result[3])
        );
      }
    }

		$db = NULL;
    return $mentNoti;
  } catch (\PDOException $e) {
  	$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Mark a mentioned notification as read (with date of reading)
 * @param uid the user that has been mentioned
 * @param pid the post where the user was mentioned
 */
function mentioned_notification_seen($uid, $pid) {
  try {
    $db = \Db::dbc();

    $sql="UPDATE MENTIONNER SET LUMENT = CURRENT_TIMESTAMP WHERE IDUSER=:uid AND IDTWEET=:pid;";
    $stmt=$db->prepare($sql);
    $stmt->execute(array(
          	':uid'=>$uid,
            ':pid'=>$pid
        ));
  } catch (\PDOException $e) {
		echo $e->getMessage();
	}
	$db=NULL;
}

/**
 * Get a followed notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the user attribute is a user object which corresponds to the user following.
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_followed_notifications($uid) {
	try {
    $db = \Db::dbc();

    $sql="SELECT IDUSERFAN, DATESUIT, LUSUIT FROM SUIVRE WHERE IDUSERSUIT=:uid";
    $stmt=$db->prepare($sql);
    $stmt->execute(array(':uid'=>$uid));

    $results = $stmt->fetchAll();
    if($results==false) {
      $followNoti=(array) NULL;
    } else {
      foreach($results as $result) {
        $followNoti[]=(object) array(
                    "type"=>"followed",
                    "user"=>\Model\User\get($result["IDUSERFAN"]),
                    "date"=>new \DateTime($result["DATESUIT"]),
                    "reading_date"=>$result["LUSUIT"] == NULL ? NULL : new \DateTime($result["LUSUIT"])
        );
      }
    }

		$db = NULL;
    return $followNoti;
  } catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Mark a followed notification as read (with date of reading)
 * @param followed_id the user id which has been followed
 * @param follower_id the user id that is following
 */
function followed_notification_seen($followed_id, $follower_id) {
	try {
  	$db = \Db::dbc();

    $sql="UPDATE SUIVRE SET LUSUIT = CURRENT_TIMESTAMP WHERE IDUSERSUIT = :idsuit AND IDUSERFAN = :idfan;";
    $stmt=$db->prepare($sql);
    $stmt->execute(array(
            ':idsuit'=>$followed_id,
            ':idfan'=>$follower_id
        ));
  } catch (\PDOException $e) {
		echo $e->getMessage();
	}
	$db = NULL;
}
