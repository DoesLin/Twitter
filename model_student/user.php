<?php
namespace Model\User;
use \Db;
use \PDOException;
/**
 * User model
 *
 * This file contains every db action regarding the users
 */

/**
 * Get a user in db
 * @param id the id of the user in db
 * @return an object containing the attributes of the user or null if error or the user doesn't exist
 */
function get($id) {
  try {
    $db = \Db::dbc();

    $sql = 'SELECT * FROM UTILISATEUR WHERE iduser = :id';
    $sth = $db->prepare($sql);
    $sth->execute(array(':id' => $id));

    $result = $sth->fetch();
    if(!$result){
        return NULL;
    }
    $o = (object) array(
			"id" => $result['IDUSER'],
        "username" => $result['USERNAME'],
        "name" => $result['NAME'],
        "dateIns"=>$result['DATEINS'],
        "password" => $result['MOTPASS'],
        "email" => $result['ADRESSEML'],
        "avatar" => $result['AVATAR']
    );
    $db = NULL;
    return $o;
  } catch (\PDOException $e) {
    $db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Create a user in db
 * @param username the user's username
 * @param name the user's name
 * @param password the user's password
 * @param email the user's email
 * @param avatar_path the temporary path to the user's avatar
 * @return the id which was assigned to the created user, null if an error occured
 * @warning this function doesn't check whether a user with a similar username exists
 * @warning this function hashes the password
 */
function create($username, $name, $password, $email, $avatar_path) {
	try{
		$db = \Db::dbc();

		$sql = "INSERT INTO `UTILISATEUR` (`USERNAME`, `NAME`, `ADRESSEML`, `MOTPASS`, `AVATAR`) VALUES (:username, :name, :email, :password, :avatar_path);";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute(
			array(
				':username' => $username,
				':name' => $name,
				':email' => $email,
				':password' => hash_password($password),
				':avatar_path' => $avatar_path
			)
		);

		$last_id=$db->lastInsertId();
		$db=NULL;
		return $last_id;
	} catch (\PDOException $e) {
		$db=NULL;
		echo $e->getMessage();
	}
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param username the user's username
 * @param name the user's name
 * @param email the user's email
 * @warning this function doesn't check whether a user with a similar username exists
 */
function modify($uid, $username, $name, $email) {
	try {
		$db = \Db::dbc();

		$sql = "UPDATE UTILISATEUR SET USERNAME=:username, NAME=:name, ADRESSEML=:email WHERE IDUSER=:uid";
		$stmt = $db->prepare($sql);
		$stmt->execute(
			array(
				':username'=>$username,
				':name'=>$name,
				':email'=>$email,
				':uid'=>$uid
			)
		);
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
	$db=NULL;
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @warning this function hashes the password
 */
function change_password($uid, $new_password) {
	try {
		$db = \Db::dbc();

		$sql = "UPDATE UTILISATEUR SET MOTPASS=:new_password WHERE IDUSER=:uid";
		$stmt = $db->prepare($sql);
		$stmt->execute(
			array(
				':new_password'=>hash_password($new_password),
				':uid'=>$uid
			)
		);
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
	$db=NULL;
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param avatar_path the temporary path to the user's avatar
 */
function change_avatar($uid, $avatar_path) {
	try {
		$db = \Db::dbc();

		$sql = "UPDATE UTILISATEUR SET AVATAR=:avatar_path WHERE IDUSER=:uid";
		$stmt = $db->prepare($sql);
		$stmt->execute(
			array(
				':avatar_path'=>$avatar_path,
				':uid'=>$uid
			)
		);
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
	$db=NULL;
}

/**
 * Delete a user in db
 * @param id the id of the user to delete
 * @return true if the user has been correctly deleted, false else
 */
function destroy($id) {
	//relative tables include:
	//"SUIVRE", "UTILISATEUR", "MENTIONNER", "AIMER" -- PK
	//"TWEET", "SUIVRE", "MENTIONNER", "AIMER" --FK
	try {
		$db = \Db::dbc();

    // Transaction has already began
		// $db->beginTransaction();

    // Deletes his tweets
    $sql = "SELECT IDTWEET FROM TWEET WHERE IDUSER=:id;";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':id'=>$id));

    $results=$stmt->fetchAll();
    if($results==true) {
      foreach($results as $result) {
        \Model\Post\destroy($result[0]);
      }
    }

    // Deletes his records of being suite and fans
		$sql = "DELETE FROM SUIVRE WHERE IDUSERSUIT=:id OR IDUSERFAN=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

    // Deletes his records of being mentioned
		$sql = "DELETE FROM MENTIONNER WHERE IDUSER=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

    // Deletes his records of liking
		$sql = "DELETE FROM AIMER WHERE IDUSER=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

    // Deletes his records
		$sql = "DELETE FROM UTILISATEUR WHERE IDUSER=:id;";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(':id'=>$id));

		// $db->commit();
    return true;
	} catch (\PDOException $e) {
		// $db->rollback();
		echo $e->getMessage();
    return false;
	}
	$db=NULL;
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
  // return password_hash($password, PASSWORD_DEFAULT);
	return hash('md5', $password);
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
	try{
		$db = \Db::dbc();

    $sql = 'SELECT * FROM UTILISATEUR WHERE USERNAME LIKE "%'. $string. '%" OR NAME LIKE "%'. $string. '%";';
    $sth = $db->prepare($sql);
    $sth->execute();

		$result = array();
		foreach($sth->fetchAll() as $row){
			$o = (object) array(
				"id" => $row['IDUSER'],
				"username" => $row['USERNAME'],
				"name" => $row['NAME'],
				"dateIns"=>$row['DATEINS'],
				"password" => $row['MOTPASS'],
				"email" => $row['ADRESSEML'],
				"avatar" => $row['AVATAR']
			);

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
 * List users
 * @return an array of the objects of every users
 */
function list_all() {
	try{
	   $db = \Db::dbc();

     $sql = 'SELECT * FROM UTILISATEUR';
	    $sth = $db->query($sql);

	    $result = array();
	    foreach($sth->fetchAll() as $row){
		      $o = (object) array(
			         "id" => $row['IDUSER'],
			         "username" => $row['USERNAME'],
			         "name" => $row['NAME'],
			         "dateIns"=>$row['DATEINS'],
			         "password" => $row['MOTPASS'],
			         "email" => $row['ADRESSEML'],
			         "avatar" => $row['AVATAR']
		           );

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
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) {
	try{
		$db = \Db::dbc();

    $sql = 'SELECT * FROM UTILISATEUR WHERE USERNAME = :username';
		$sth = $db->prepare($sql);
    $sth->execute(array(':username' => $username));

    $result = $sth->fetch();
    if(!$result){
      return null;
    }
    $o = (object) array(
          "id" => $result['IDUSER'],
          "username" => $result['USERNAME'],
          "name" => $result['NAME'],
          "dateIns"=>$result['DATEINS'],
          "password" => $result['MOTPASS'],
          "email" => $result['ADRESSEML'],
          "avatar" => $result['AVATAR']
    	   );
    $db = NULL;
    return $o;
	} catch (\PDOException $e) {
    $db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
	try{
		$db = \Db::dbc();

    $sql = "SELECT `IDUSERFAN` FROM `SUIVRE` WHERE `IDUSERSUIT` = :userid;";
		$sth = $db->prepare($sql);
    $sth->execute(array(':userid' => $uid));

		$result = array();
		foreach($sth->fetchAll() as $row){
			$o = get($row['IDUSERFAN']);
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
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
	try{
		$db = \Db::dbc();

    $sql = "SELECT `IDUSERSUIT` FROM `SUIVRE` WHERE `IDUSERFAN` = :userid;";
		$sth = $db->prepare($sql);
    $sth->execute(array(':userid' => $uid));

		$result = array();
		foreach($sth->fetchAll() as $row){
			$o = get($row['IDUSERSUIT']);
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
 * Get a user's stats
 * @param uid the user's id
 * @return an object which describes the stats
 */
function get_stats($uid) {
	try{
    $db = \Db::dbc();

		$sql1 = "SELECT COUNT(*) AS NUMS FROM `SUIVRE` WHERE `IDUSERSUIT` = :idusersuit";
		$stmt = $db->prepare($sql1);
		$stmt->execute(array(':idusersuit' => $uid,));
		$result = $stmt->fetch();
		$o["nb_followers"] = $result['NUMS'];

		$sql2 = "SELECT COUNT(*) AS NUMF FROM `SUIVRE` WHERE `IDUSERFAN` = :iduserfan";
		$stmt = $db->prepare($sql2);
		$stmt->execute(array(':iduserfan' => $uid,));
		$result = $stmt->fetch();
		$o["nb_following"] = $result['NUMF'];

		$sql3 = "SELECT COUNT(*) AS NUMT FROM `TWEET` WHERE `IDUSER` = :iduser";
		$stmt = $db->prepare($sql3);
		$stmt->execute(array(':iduser' => $uid,));
		$result = $stmt->fetch();
		$o["nb_posts"] = $result['NUMT'];

    $db = NULL;
		return (object) $o;
	} catch (\PDOException $e) {
		$db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Verify the user authentification
 * @param username the user's username
 * @param password the user's password
 * @return the user object or null if authentification failed
 * @warning this function must perform the password hashing
 */
function check_auth($username, $password) {
	try {
		$userObj=get_by_username($username);
		$userPwd=hash_password($password);
    // This way of authentification is not safe!
    // Hashing a same password in different times should
    // has different result.
		if($userObj->password===$userPwd) {
    // if(password_verify($password, $userObj->password)) {
			return $userObj;
		} else {
      return NULL;
		}
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
}

/**
 * Verify the user authentification based on id
 * @param id the user's id
 * @param password the user's password (already hashed)
 * @return the user object or null if authentification failed
 */
function check_auth_id($id, $password) {
	try {
		$userObj=get($id);
		if($userObj->password===$password) {
			return $userObj;
		} else {
      return NULL;
		}
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 */
function follow($id, $id_to_follow) {
	try{
		$db = \Db::dbc();

		$sql = "INSERT INTO `SUIVRE` (`IDUSERSUIT`, `IDUSERFAN`) VALUES (:idusersuit, :iduserfan);";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute(
			array(
				':idusersuit' => $id_to_follow,
				':iduserfan' => $id
			)
		);
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
  $db = NULL;
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 */
function unfollow($id, $id_to_unfollow) {
	try{
		$db = \Db::dbc();

		$sql = "DELETE FROM `SUIVRE` WHERE `IDUSERSUIT` = :idusersuit AND `IDUSERFAN` = :iduserfan";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute(
			array(
				':idusersuit' => $id_to_unfollow,
				':iduserfan' => $id
			)
		);
	} catch (\PDOException $e) {
		echo $e->getMessage();
	}
  $db = NULL;
}
