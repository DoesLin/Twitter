<?php 
include "main.php";
main_template(get_defined_vars(), function($vars) {
    extract($vars);
?>
    <div id="list" class="pure-u-1">
        <div class="pure-g">
            <div class="pure-u-1-6 pure-menu search-options">
                <ul class="pure-menu-list">
                      <li class="pure-menu-item"><a href="search.php?query=<?php echo htmlspecialchars($query_txt); ?>&post" class="pure-menu-link">Twirps</a></li>
                      <li class="pure-menu-item pure-menu-selected"><a href="search.php?query=<?php echo htmlspecialchars($query_txt); ?>&user" class="pure-menu-link">Users</a></li>
                  </ul>
            </div>
            <div class="pure-u-5-6">
                <div class="pure-g">
                    <?php
                        foreach($users as $user) {
                    ?>
                    <div class="block user pure-u-1-3">
                        <div class="inner-block user-head">
                            <div class="user-avatar">
                                <a href="user.php?username=<?php echo htmlspecialchars($user->username); ?>">
                                    <img class="email-avatar" src="<?php
        $avatar = empty($user->avatar) ? '/images/default.jpg' : $user->avatar;
        echo htmlspecialchars($avatar); 
                                    ?>" height="64" width="64">

                                </a>
                            </div>

                            <div class="user-name">
                                <a href="user.php?username=<?php echo htmlspecialchars($user->username); ?>">
                                    <?php echo htmlspecialchars($user->name); ?> (<?php echo htmlspecialchars($user->username); ?>)
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
});
?>
