<?php
use PHPUnit\Framework\TestCase;
use Model\User;

class UserTest extends TestCase
{
    public function testCreate()
    {
        \Db::flush();
        $uid = User\create(
            "user1",
            "User 1",
            "password",
            "user1@mail.com",
            ""
        );
        $this->assertNotNull($uid, "create user should return the new user id");
        $user = User\get($uid);
        $this->assertNotNull($user, "get should return a user object");
        $this->assertEquals($uid, $user->id, "user object id should be matching the one returned by create");
        $this->assertObjectHasAttribute("id", $user, "User object should have an id attribute");
        $this->assertEquals("user1", $user->username, "Username should be user1");
        $this->assertObjectHasAttribute("name", $user, "User object should have a username attribute");
        $this->assertEquals("User 1", $user->name, "Name should be 'User 1' instead of '$user->name'");
        $this->assertObjectHasAttribute("email", $user, "User object should have an email attribute");
        $this->assertEquals("user1@mail.com", $user->email, "Email should be 'user1@mail.com' instead of '$user->email'");
        $this->assertEquals($user, User\check_auth($user->username, "password"), "check_auth not working : should return a user object");
        $this->assertEquals($user, User\check_auth_id($uid, User\hash_password("password")), "check_auth_id not working : should return a user object");

        $this->assertNull(User\get(-1), "get should return null if no user were fould");
        return $uid;
    }

    /**
     * @depends testCreate
     */
    public function testModify($uid)
    {
        $user = User\get($uid);
        User\modify(
            $uid,
            "user2",
            "User 2",
            "user2@mail.com"
        );
        $user = User\get($uid);
        $this->assertEquals("user2", $user->username, "modify should set the new username");
        $this->assertEquals("User 2", $user->name, "modify should set the new name");
        $this->assertEquals("user2@mail.com", $user->email, "modify should set the new username");
        $this->assertEquals($user, User\check_auth($user->username, "password"), "modify should not alter the password");
        $this->assertEquals($user, User\check_auth_id($uid, User\hash_password("password")), "modify should not alter the password");
        return $uid;
    }

    /**
     * @depends testModify
     */
    public function testDestroy($uid) 
    {
        $duid = User\create(
            "user1todelete",
            "User 1todelete",
            "passwordlol",
            "user1@mail.comlol",
            ""
        );
        User\destroy($duid);
        $this->assertNull(User\get($duid), "Deleted user should no more exist");
        return $uid;
    }    

    /**
     * @depends testDestroy
     */
    public function testChangePassword($uid)
    {
        User\change_password(
            $uid,
            "new password"
        );
        $user = User\get($uid);
        $this->assertEquals($user, User\check_auth($user->username, "new password"), "Modify should set the new password properly");
        $this->assertEquals($user, User\check_auth_id($uid, User\hash_password("new password")), "Modify should set the new password properly");
        return $uid;
    }
    
    /**
     * @depends testChangePassword
     */
    public function testListAll($uid)
    {
        $existing_user = User\get($uid);
        $new_uid = User\create(
            "user1",
            "User 1",
            "password",
            "user1@mail.com",
            ""
        );
        $new_user = User\get($new_uid);
        $l = User\list_all();
        $this->assertEquals(2, count($l), "list_all should list every user");
        $this->assertContains($new_user, $l, "list_all should list every user in user objects", false, false);
        $this->assertContains($existing_user, $l, "list_all should list every user in user objects", false, false);
        return [$existing_user, $new_user];
    }

    /**
     * @depends testListAll
     */
    public function testGetByUsername($users)
    {
        foreach($users as $u) {
            $this->assertEquals($u, User\get_by_username($u->username), "get_by_username should return the user object if a user was found");
        }

        $this->assertNull(User\get_by_username("nothing called like it"), "get_by_username should return null if no user with a given username were found");
        return $users;
    }

    /**
     * @depends testGetByUsername
     */
    public function testSearch($users)
    {
        foreach($users as $u) {
            $r = User\search($u->name);

            $this->assertEquals(1, count($r), "search should search users with name");
            $this->assertEquals($u, $r[0]);
        }
        foreach($users as $u) {
            $r = User\search($u->username);

            $this->assertEquals(1, count($r), "search should search users with username");
            $this->assertEquals($u, $r[0]);
        }

        $r = User\search("user");

        $this->assertEquals(2, count($r), "search should do substring comparisons");
        
        return $users;
    }

    /**
     * @depends testSearch
     */
    public function testFollow($users)
    {
        User\follow($users[0]->id, $users[1]->id);

        $l = User\get_followers($users[1]->id);
        $this->assertEquals(1, count($l), "get_followers should return every follower users");
        $this->assertEquals($l[0], $users[0], "get_followers should return user objects");

        $l = User\get_followings($users[0]->id);
        $this->assertEquals(1, count($l), "get_followings should return every following users");
        $this->assertEquals($l[0], $users[1], "get_followings should return user objects");

        User\unfollow($users[0]->id, $users[1]->id);
        $this->assertEmpty(User\get_followings($users[0]->id), "unfollow should make that a user is unfollowed");
        $this->assertEmpty(User\get_followers($users[1]->id));
        return $users;
    }

    public static function tearDownAfterClass()
    {
    }
}
?>
