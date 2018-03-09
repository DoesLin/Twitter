<?php 
include "main.php";
main_template(get_defined_vars(), function($vars) {
    extract($vars);
?>
    <div id="list" class="pure-u-1">
        <div class="pure-g">
            <div class="pure-u-1-3">
                <div class="block sidebar">
                    <div class="inner-block">
                        <div class="user-badge">
                            <div class="user-head">
                                <div class="user-avatar">
                                    <img src="<?php
        $avatar = empty($user->avatar) ? '/images/default.jpg' : $user->avatar;
        echo htmlspecialchars($avatar); 
                                    ?>" height="64" width="64">
                                </div> 
                                <div class="user-name"><?php echo htmlspecialchars($user->name); ?> (<?php echo htmlspecialchars($user->username); ?>)</div>
                            </div>

                            <div class="user-infos pure-g">
                                <div class="pure-u-1-3"><?php echo $stats->nb_posts; ?><br/>twirps</div>
                                <div class="pure-u-1-3"><?php echo $stats->nb_followers; ?><br/>followers</div>
                                <div class="pure-u-1-3"><?php echo $stats->nb_following; ?><br/>following</div>
                            </div>

                            <div class="user-actions">
                                <?php if ($followable) { 
                                    if ($followed) { ?>
                                <a class="pure-button" href="?username=<?php echo htmlspecialchars($user->username);?>&unfollow">Unfollow</a>
                                <?php
                                    }
                                    else {
                                ?>
                                <a class="pure-button" href="?username=<?php echo htmlspecialchars($user->username);?>&follow">Follow</a>
                                <?php }
                                }
                                if ($editable) { ?>
                                <a class="pure-button" href="update_profile.php">Edit profile</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pure-u-2-3">
                <div class="block">
                    <?php
                        foreach($posts as $post) {
                            \View\Partials\Post\post($post);
                        }
                    ?>
                    <div class="innerblock end"></div>
                </div>
            </div>
        </div>
    </div>
<?php
});
?>
