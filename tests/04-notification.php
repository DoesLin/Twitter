<?php
use PHPUnit\Framework\TestCase;
use \Model\Post;
use \Model\User;
use \Model\Notification;

class NotificationTest extends TestCase
{
    protected static $pids;
    protected static $uids;
    public static function setUpBeforeClass()
    {
        \Db::flush();
        self::$uids = [];
        self::$uids[] = User\create(
            "userpost1",
            "User 1",
            "password",
            "user1@mail.com",
            ""
        );
        
        self::$uids[] = User\create(
            "userpost2",
            "User 2",
            "password",
            "user2@mail.com",
            ""
        );
        self::$pids = [];
        
        self::$pids[] = Post\create(self::$uids[0], "this is a searchid1 test");
        self::$pids[] = Post\create(self::$uids[1], "this searchid2 is a test");
    }

    public function testLikedNotification()
    {
        Post\like(self::$uids[0], self::$pids[1]);
        $n = Notification\get_liked_notifications(self::$uids[1]);
        $this->assertEquals(1, count($n), "get_liked_notifications should return an array of liked notifications");
        $this->assertEquals(self::$pids[1], $n[0]->post->id, "post in 'liked' notification should be the one liked");
        $this->assertEquals(self::$uids[0], $n[0]->liked_by->id, "liked_by in 'liked' notification should be the user who liked the post");
        $this->assertEquals("liked", $n[0]->type, "the notification should have a type attribute equal to 'liked'");
        $this->assertObjectHasAttribute('date', $n[0], "the liked notification should have a date attribute");
        $this->assertObjectHasAttribute('reading_date', $n[0], "the liked notification should have a reading_date attribute");

        $this->assertNull($n[0]->reading_date, "reading_date should be equal to null if the notification hasn't been seen");
        Notification\liked_notification_seen(self::$pids[1], self::$uids[0]);
        $n = Notification\get_liked_notifications(self::$uids[1]);
        $this->assertNotNull($n[0]->reading_date, "reading date from get_liked_notifications shouldn't be null if the notification has been seen");
        
        Post\unlike(self::$uids[0], self::$pids[1]);
        $n = Notification\get_liked_notifications(self::$uids[1]);
        $this->assertEmpty($n, "get_liked_notification should not return a notification if a like has been undone");
    }

    /**
     * @depends testLikedNotification
     */  
    public function testMentionedNotification()
    {
        Post\mention_user(self::$pids[0], self::$uids[1]);
        $n = Notification\get_mentioned_notifications(self::$uids[1]);
        $this->assertEquals(1, count($n), "get_mentioned_notifications should return an array of mentioned notifications");
        $this->assertEquals(self::$uids[0], $n[0]->mentioned_by->id, "mentioned_by should be the person who created the post in which the user was mentioned");
        $this->assertEquals(self::$pids[0], $n[0]->post->id, "post should be the post in which the user was mentioned");
        $this->assertEquals("mentioned", $n[0]->type, "the notification should have a type attribute equal to 'mentioned'");
        $this->assertObjectHasAttribute('date', $n[0], "the mentioned notification should have a date attribute");
        $this->assertObjectHasAttribute('reading_date', $n[0]);

        $this->assertNull($n[0]->reading_date, "reading_date should be equal to null if the notification hasn't been seen");
        Notification\mentioned_notification_seen(self::$uids[1], self::$pids[0]);
        $n = Notification\get_mentioned_notifications(self::$uids[1]);
        $this->assertNotNull($n[0]->reading_date, "reading date from get_mentioned_notifications shouldn't be null if the notification has been seen");

        Post\destroy(self::$pids[0]);
        $n = Notification\get_mentioned_notifications(self::$uids[1]);
        $this->assertEmpty($n, "get_mentioned_notification should not return a notification if the post where the mention was has been destroyed");
    }

    /**
     * @depends testMentionedNotification
     */
    public function testFollowedNotification()
    {
        User\follow(self::$uids[0], self::$uids[1]);
        $n = Notification\get_followed_notifications(self::$uids[1]);
        $this->assertEquals(1, count($n), "get_followed_notifications should return an array of followed notifications");
        $this->assertEquals(self::$uids[0], $n[0]->user->id, "user should be the person who followed the user");
        $this->assertEquals("followed", $n[0]->type, "the followed notification should have a type attribute equal to 'followed'");
        $this->assertObjectHasAttribute('date', $n[0], "the followed notification should have a date attribute");

        $this->assertNull($n[0]->reading_date, "reading_date should be equal to null if the notification hasn't been seen");
        Notification\followed_notification_seen(self::$uids[1], self::$uids[0]);
        $n = Notification\get_followed_notifications(self::$uids[1]);
        $this->assertNotNull($n[0]->reading_date, "reading date from get_followed_notifications shouldn't be null if the notification has been seen");
        
        User\unfollow(self::$uids[0], self::$uids[1]);
        $n = Notification\get_followed_notifications(self::$uids[1]);
        $this->assertEmpty($n, "get_followed_notification should not return a notification if the other user is not following anymore");
    }

    /**
     * @depends testFollowedNotification
     */  
    public function testListAllNotifications()
    {
        Post\mention_user(self::$pids[1], self::$uids[1]);
        sleep(1);
        User\follow(self::$uids[0], self::$uids[1]);
        sleep(1);
        Post\like(self::$uids[0], self::$pids[1]);
        
        $n = Notification\list_all_notifications(self::$uids[1]);
        $this->assertEquals(3, count($n), "list_all_notifications should be able to get every notification types");
        $this->assertEquals("mentioned", $n[2]->type, "list_all notifications should be able to sort notifications by dates (with datetime objects)");
        $this->assertEquals("followed", $n[1]->type, "list_all notifications should be able to sort notifications by dates (with datetime objects)");
        $this->assertEquals("liked", $n[0]->type, "list_all notifications should be able to sort notifications by dates (with datetime objects)");
    }

    public static function tearDownAfterClass()
    {
    }

}
?>
