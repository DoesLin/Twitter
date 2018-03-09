<?php
namespace Model\Hashtag;
use \Db;
use \PDOException;
use \PDO;
/**
 * Hashtag model
 *
 * This file contains every db action regarding the hashtags
 */

/**
 * Attach a hashtag to a post
 * @param pid the post id to which attach the hashtag
 * @param hashtag_name the name of the hashtag to attach
 */
function attach($pid, $hashtag_name) {
  try {
    $db = \Db::dbc();

    //check whether the hashtag already existe
    $sql='SELECT * FROM HASHTAG WHERE TAGNAME LIKE "'. $hashtag_name. '";';
    $stmt=$db->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch();
    if($result==false) {
      //insert into hashtag
      $sql="INSERT INTO HASHTAG (TAGNAME) VALUES (:hashtag_name);";
      $stmt=$db->prepare($sql);
      $stmt->execute(array(':hashtag_name'=>$hashtag_name));
    }

    //insert into concerner
    $sql="INSERT INTO CONCERNER (IDTWEET, TAGNAME) VALUES (:pid, :hashtag_name);";
    $stmt=$db->prepare($sql);
    $stmt->execute(
            array(
                ':pid'=>$pid,
                ':hashtag_name'=>$hashtag_name
            )
        );
  } catch (\PDOException $e) {
		echo $e->getMessage();
	}
  $db = NULL;
}

/**
 * List hashtags
 * @return a list of hashtags names
 */
function list_hashtags() {
  try {
    $db = \Db::dbc();

    $sql="SELECT TAGNAME FROM HASHTAG;";
    $stmt=$db->prepare($sql);
		$stmt->execute();

    $results = $stmt->fetchAll();
    if($results==false) {
      $hashtags=(array) NULL;
    } else {
      foreach($results as $result) {
        $hashtags[]=$result[0];
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
 * List hashtags sorted per popularity (number of posts using each)
 * @param length number of hashtags to get at most
 * @return a list of hashtags
 */
function list_popular_hashtags($length) {
  //top $length popular hashtags
  //create a view
  //drop view myView;
  try {
    $db = \Db::dbc();

    $sql="CREATE OR REPLACE VIEW TOPHASHTAGS (TAGNAME, NBR) AS SELECT TAGNAME, COUNT(*) FROM CONCERNER GROUP BY TAGNAME;";
    $stmt=$db->prepare($sql);
		$stmt->execute();

    $sql="SELECT TAGNAME FROM TOPHASHTAGS ORDER BY NBR DESC;";
    $stmt=$db->prepare($sql);
		$stmt->execute();

    $results=$stmt->fetchAll();
    if($results==false) {
      $topHashtags=(array) NULL;
    } else {
      foreach($results as $result) {
        if($length--<=0) {
          break;
        }
        $topHashtags[]=$result[0];
      }
    }

    $sql="DROP VIEW TOPHASHTAGS;";
    $stmt=$db->prepare($sql);
		$stmt->execute();

    $db = NULL;
    return $topHashtags;
  } catch (\PDOException $e) {
    $db = NULL;
		echo $e->getMessage();
	}
}

/**
 * Get posts for a hashtag
 * @param hashtag the hashtag name
 * @return a list of posts objects or null if the hashtag doesn't exist
 */
function get_posts($hashtag_name) {
	try{
	  $db = \Db::dbc();

    $sql = "SELECT * FROM `CONCERNER` NATURAL JOIN `TWEET` WHERE `TAGNAME` = :tagname";
	  $sth = $db->prepare($sql);
    $sth->execute(array(':tagname' => $hashtag_name));

    $result = array();
	  foreach($sth->fetchAll() as $row){
		  $o = (object) array(
     	 	    "id"=>$row['IDTWEET'],
    		    "text"=>$row['MICROMES'],
    		    "date"=>$row['DATEPUB'],
    		    "author"=>\Model\User\get($row['IDUSER'])
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

/** Get related hashtags
 * @param hashtag_name the hashtag name
 * @param length the size of the returned list at most
 * @return an array of hashtags names
 */
function get_related_hashtags($hashtag_name, $length) {
	try{
	  $db = \Db::dbc();

    $sql = "SELECT B.`TAGNAME` FROM `CONCERNER` A, `CONCERNER` B WHERE A.`IDTWEET` = B.`IDTWEET` AND A.`TAGNAME` = :tagname AND B.`TAGNAME` <> A.`TAGNAME`";
	  $sth = $db->prepare($sql);
    $sth->execute(array(':tagname' => $hashtag_name));
    $result = array();

    $i = 0;
	  foreach($sth->fetchAll() as $row){
		  $result[] = $row[0];
		  if($i++ >= $length) {break;}
	  }

    $db = NULL;
    return $result;
	} catch (\PDOException $e) {
    $db = NULL;
		echo $e->getMessage();
	}
}
