<?php 
include "main.php";
main_template(get_defined_vars(), function($vars) {
    extract($vars);
?>
    <div id="list" class="pure-u-1">
        <div class="pure-g">
            <div class="pure-u-1-6">
            </div>
            <div class="pure-u-2-3">
                <div class="block">
                    <?php
                        foreach($notifications as $notification) {
                            $new_class = $notification->reading_date ? "" : " notification-new";
                            switch($notification->type) {
                            case "liked":
                                $avatar = empty($notification->liked_by->avatar) ? '/images/default.jpg' : $notification->liked_by->avatar;
?>
                    <div class="inner-block notification notification-like<?php echo $new_class; ?>">
                        <div class="notification-content"><?php echo htmlspecialchars($notification->liked_by->name); ?> liked a <a href="post.php?id=<?php echo htmlspecialchars($notification->post->id); ?>">twirp</a> you wrote</div>
                        <div class="notification-users"><a href="user.php?username=<?php echo htmlspecialchars($notification->liked_by->username); ?>"><img src="<?php echo htmlspecialchars($avatar); ?>" width="32px" height="32px"/></a></div>
                    </div>
                                    <?php
                                break;
                                case "mentioned":
                                    \View\Partials\Post\post($notification->post, "notification notification-mention" . $new_class);
                                break;
                                case "followed":
                                    $avatar = empty($notification->user->avatar) ? '/images/default.jpg' : $notification->user->avatar;
                                    ?>
                    <div class="inner-block notification notification-followed<?php echo $new_class; ?>">
                        <div class="notification-content"><?php echo htmlspecialchars($notification->user->name); ?> is following you</div>
                        <div class="notification-users"><a href="user.php?username=<?php echo htmlspecialchars($notification->user->username); ?>"><img src="<?php echo htmlspecialchars($avatar); ?>" width="32px" height="32px"/></a></div>
                    </div>
                                    <?php
                                break;
                            }
                        }
                    ?>
                    <div class="innerblock end"></div>
                </div>
            </div>
            <div class="pure-u-1-6">
            </div>            
        </div>
    </div>
<?php
});
?>
