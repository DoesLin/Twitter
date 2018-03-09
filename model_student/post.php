<?php
namespace Model\Post;
use \Db;
use \PDOException;
use \PDO;
/**
 * Post
 *
 * This file contains every db action regarding the posts
 */

/**
 * Get a post in db
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 */
function get($id) {
	try {
		$db = \Db::dbc();

		$sql = "SELECT * FROM TWEET WHERE IDTWEET = :id";
		$stmt = $db->prepare($sql);
    $stmt->execute(array(':id' => $id));

    $result = $stmt->fetch();
		$db = NULL;
    if($result==false) {
    	return NULL;
    } else {
    	return (object) array(
    					"id"=>$result['IDTWEET'],
    					"text"=>$result['MICROMES'],
    					"date"=>$result['DATEPUB'],
    					"author"=>\Model\User\get($result['IDUSER'])
    	);
    }
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
}

/**
 * Get a post with its likes, responses, the hashtags used and the post it was the response of
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the likes attribute is an array of users objects
 * @warning the hashtags attribute is an of hashtags objects
 * @warning the responds_to attribute is either null (if the post is not a response) or a post object
 */
function get_with_joins($id) {
	try {
		$db = \Db::dbc();

		$sql = "SELECT * FROM TWEET WHERE IDTWEET = :id";
		$stmt = $db->prepare($sql);
    $stmt->execute(array(':id' => $id));

    $result = $stmt->fetch();
		$db = NULL;
    if($result==false) {
    	return (object) NULL;
    } else {
    	return (object) array(
    					"id"=>$result['IDTWEET'],
    					"text"=>$result['MICROMES'],
    					"date"=>$result['DATEPUB'],
    					"author"=>\Model\User\get($result['IDUSER']),
    					"likes"=>get_likes($id),
    					"hashtags"=>get_hashtags($result['IDTWEET']),
    					"responds_to"=>get($result['IDTWEET_REPONDRE'])
    	);
    }
	} catch (\PDOException $e) {
		$db=NULL;
		echo $e->getMessage();
	}
}

/**
 * Create a post in db
 * @param author_id the author user's id
 * @param text the message
 * @param response_to the id of the post which the creating post responds to
 * @return the id which was assigned to the created post
 * @warning this function computes the date
 * @warning this function adds the mentions (after checking the users' existence)
 * @warning this function adds the hashtags
 * @warning this function takes care to rollback if one of the queries comes to fail.
 */
function create($author_id, $text, $response_to=null) {
	try {
		$db = \Db::dbc();
		$db->beginTransaction();

		//create the post
		$sql="INSERT INTO TWEET (IDUSER, MICROMES) VALUES (:author_id, :text);";
		$stmt=$db->prepare($sql);
		$stmt->execute(
			array(
				':author_id'=>$author_id,
				':text'=>$text
			)
		);
		$lastId=$db->lastInsertId();

		//create the response
		if($response_to!=NULL) {
			$sql = "UPDATE TWEET SET IDTWEET_REPONDRE=:response_to WHERE IDTWEET=:lastId;";
			$stmt=$db->prepare($sql);
			$stmt->execute(
				array(
					':response_to'=>$response_to,
					':lastId'=>$lastId
				)
			);
		}

		//create the mention
    $userMents=get_mentioned($lastId);
    if($userMents!=NULL) {
      foreach($userMents as $userMent) {
      	//use mention_user
        //need to check whether this user name existe
        //this check has been took by get_by_username
        if($userMent==NULL) {
          //wrong user name
          throw new \PDOException("Error: Wrong mentioned user name\n");
        } else {
          //insert into mentionner
          mention_user($lastId, $userMent->id);
        }
      }
  	}

    //create the hashtag
    $hashtags=get_hashtags($lastId);
    if($hashtags!=NULL) {
      foreach($hashtags as $hashtag) {
        //insert into concerner and hashtag
        //use Model\Hashtag\attach
        \Model\Hashtag\attach($lastId, $hashtag);
      }
    }

		$db->commit();
		$db=NULL;
		return $lastId;
	} catch (\PDOException $e) {
		$db->rollback();
		$db=NULL;
		echo $e->getMessage();
	}
}

/**
 * Mention a user in a post
 * @param pid the post id
 * @param uid the user id to mention
 */

function mention_user($pid, $uid) {
  try {
    $db = \Db::dbc();
    $sql="INSERT INTO MENTIONNER (IDUSER, IDTWEET) VALUES (:uid, :pid);";
		$stmt=$db->prepare($sql);
		$stmt->execute(
			array(
				':uid'=>$uid,
				':pid'=>$pid
			)
		);

    $db=NULL;
  } catch (\PDOException $e) {
    $db=NULL;
		echo $e->getMessage();
	}
}

/** Defined by Y. LIN
 * Get hashtags in post
 * @param pid the post id
 * @return the array of user objects hashtags
 */
function get_hashtags($pid) {
	try {
		$db = \Db::dbc();

		$sql="SELECT MICROMES FROM TWEET WHERE IDTWEET=:pid;";
		$stmt=$db->prepare($sql);
		$stmt->execute(array(':pid'=>$pid));

	  $text = $stmt->fetch();
    $hashtags=(array) NULL;
    if($text==false) {
      throw new \PDOException("Error: Wrong post id\n");
    } else {
      $regexp="/#\w*/";
      preg_match_all($regexp, $text[0], $matches);
      foreach($matches[0] as $matche) {
        $hashtags[]=substr($matche, 1);
      }
    }
		$db = NULL;
		return $hashtags;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get mentioned user in post
 * @param pid the post id
 * @return the array of user objects mentioned
 */
function get_mentioned($pid) {
	try {
		$db = \Db::dbc();

		$sql="SELECT MICROMES FROM TWEET WHERE IDTWEET=:pid;";
		$stmt=$db->prepare($sql);
		$stmt->execute(array(':pid'=>$pid));

	  $text = $stmt->fetch();
    $userNames=(array) NULL;
    if($text==false) {
      throw new \PDOException("Error: Wrong post id\n");
    } else {
      /* $pid = Post\create($uid, */
      /*     "@".self::$users[1]->username); */
      $regexp="/@\w*/";
      preg_match_all($regexp, $text[0], $matches);
      foreach($matches[0] as $matche) {
      	//get_mentioned should return user objects
      	$userNames[]=\Model\User\get_by_username(
                    substr($matche, 1));
      }
    }
		$db = NULL;
		return $userNames;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Delete a post in db
 * @param id the id of the post to delete
 */
function destroy($id) {
	//relative tables include:
	//"TWEET", "MENTIONNER", "CONCERNER", "AIMER" -- PK
	//"TWEET", "MENTIONNER", "CONCERNER", "AIMER" --FK
	try {
		$db = \Db::dbc();

		$db->beginTransaction();

		// Deletes the mentions
		$sql = "DELETE FROM MENTIONNER WHERE IDTWEET=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

		// Deletes the hashtags
		$sql = "SELECT TAGNAME FROM CONCERNER WHERE IDTWEET=:id;";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':id'=>$id));

		$results=$stmt->fetchAll();
    if($results==true) {
      foreach($results as $result) {
				$sql = 'DELETE FROM HASHTAG WHERE TAGNAME LIKE "'. $result[0]. '";';
				$stmt = $db->prepare($sql);
				$stmt->execute();
      }
    }

		// Deletes the concerner
		$sql = "DELETE FROM CONCERNER WHERE IDTWEET=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

		// Deletes the likes
		$sql = "DELETE FROM AIMER WHERE IDTWEET=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

		// Deletes the tweets which is responded
		$sql = "UPDATE TWEET SET IDTWEET_REPONDRE = NULL WHERE IDTWEET_REPONDRE=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

		// Deletes the tweets
		$sql = "DELETE FROM TWEET WHERE IDTWEET=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

		$db->commit();
    $db=NULL;
		return true;
	} catch (\PDOException $e) {
		$db->rollback();
    $db=NULL;
		echo $e->getMessage();
		return false;
	}
}

/**
 * Search for posts
 * @param string the string to search in the text
 * @return an array of find objects
 */
function search($string) {
  try {
    $db = \Db::dbc();

  	$sql='SELECT IDTWEET FROM TWEET WHERE MICROMES LIKE "%'. $string. '%";';
    $stmt = $db->prepare($sql);
		$stmt->execute();
		$result = array();
    while($pid = $stmt->fetch()) {
      $result[] = get($pid[0]);
    }
		$db = NULL;
    return $result;
  } catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * List posts
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return an array of the objects of each post
 */
function list_all($date_sorted=false) {
  try {
    $db = \Db::dbc();

  	if($date_sorted==false) {
      $sql="SELECT IDTWEET FROM TWEET;";
      $stmt=$db->prepare($sql);
      $stmt->execute();
    } else if ($date_sorted==="ASC") {
      $sql="SELECT IDTWEET FROM TWEET ORDER BY DATEPUB ASC;";
      $stmt=$db->prepare($sql);
      $stmt->execute();
    } else if ($date_sorted==="DESC") {
      $sql="SELECT IDTWEET FROM TWEET ORDER BY DATEPUB DESC;";
      $stmt=$db->prepare($sql);
      $stmt->execute();
    } else {
      throw new \PDOException("Error: Wrong format\n");
    }

    $results=$stmt->fetchAll();
    if($results==false) {
      $posts=(array) NULL;
    } else {
      foreach($results as $result) {
        $posts[]=get($result[0]);
    	}
    }

		$DB = Null;
    return $posts;
  } catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get a user's posts
 * @param id the user's id
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return the list of posts objects
 */
function list_user_posts($id, $date_sorted="DESC") {
  try {
    $db = \Db::dbc();

  	if($date_sorted==false) {
      $sql="SELECT IDTWEET FROM TWEET WHERE IDUSER=:id;";
      $stmt=$db->prepare($sql);
      $stmt->execute(array(':id' => $id));
    } else if ($date_sorted==="ASC") {
      $sql="SELECT IDTWEET FROM TWEET WHERE IDUSER=:id ORDER BY DATEPUB ASC;";
      $stmt=$db->prepare($sql);
      $stmt->execute(array(':id' => $id));
    } else if ($date_sorted==="DESC") {
      $sql="SELECT IDTWEET FROM TWEET WHERE IDUSER=:id ORDER BY DATEPUB DESC;";
      $stmt=$db->prepare($sql);
      $stmt->execute(array(':id' => $id));
    } else {
      throw new \PDOException("Error: Wrong format\n");
    }

    $results=$stmt->fetchAll();
    if($results==false) {
      $userPosts=(array) NULL;
    } else {
      foreach($results as $result) {
        $userPosts[]=get($result[0]);
      }
    }

		$db = NULL;
    return $userPosts;
  } catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get a post's likes
 * @param pid the post's id
 * @return the users objects who liked the post
 */
function get_likes($pid) {
	try{
		$db = \Db::dbc();

    $sql = "SELECT `IDUSER` FROM `AIMER` WHERE `IDTWEET` = :idtweet;";
		$sth = $db->prepare($sql);
    $sth->execute(array(':idtweet' => $pid));

		$result = array();
		foreach($sth->fetchAll() as $row){
			$o = \Model\User\get($row['IDUSER']);

			$result[] = $o;
		}

		$db = NULL;
    return $result;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get a post's responses
 * @param pid the post's id
 * @return the posts objects which are a response to the actual post
 */
function get_responses($pid) {
	try {
		$db = \Db::dbc();

		$sql = "SELECT `IDTWEET` FROM `TWEET` WHERE `IDTWEET_REPONDRE` = :idrep";
		$sth = $db->prepare($sql);
    $sth->execute(array(':idrep' => $pid));

    $result = array();
		foreach($sth->fetchAll() as $row){
			$o = get($row['IDTWEET']);

			$result[] = $o;
		}

		$db = NULL;
    return $result;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get stats from a post (number of responses and number of likes
 */
function get_stats($pid) {
	try{
		$db = \Db::dbc();

		$sql1 = "SELECT COUNT(*) AS NUML FROM `AIMER` WHERE `IDTWEET` = :idtweet";
		$stmt = $db->prepare($sql1);
		$stmt->execute(array(':idtweet' => $pid,));
		$result = $stmt->fetch();
		$o["nb_likes"] = $result['NUML'];

		$sql2 = "SELECT COUNT(*) AS NUMR FROM `TWEET` WHERE `IDTWEET_REPONDRE` = :idrep";
		$stmt = $db->prepare($sql2);
		$stmt->execute(array(':idrep' => $pid,));
		$result = $stmt->fetch();
		$o["nb_responses"] = $result['NUMR'];

		$db = NULL;
		return (object) $o;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Like a post
 * @param uid the user's id to like the post
 * @param pid the post's id to be liked
 */
function like($uid, $pid) {
	try{
		$db = \Db::dbc();

		$sql = "INSERT INTO `AIMER`(`IDUSER`, `IDTWEET`) VALUES (:iduser, :idtweet)";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute(
			array(
				':iduser' => $uid,
				':idtweet' => $pid
			)
		);
		$db = NULL;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Unlike a post
 * @param uid the user's id to unlike the post
 * @param pid the post's id to be unliked
 */
function unlike($uid, $pid) {
	try{
		$db = \Db::dbc();

		$sql = "DELETE FROM `AIMER` WHERE `IDUSER` = :iduser AND `IDTWEET` = :idtweet";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute(
			array(
				':iduser' => $uid,
				':idtweet' => $pid
			)
		);
		$db = NULL;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}
