<?php 
include "main.php";
main_template(get_defined_vars(), function($vars) {
    extract($vars);
?>
    <div id="list" class="pure-u-1">
        <div class="pure-g">
            <div class="pure-u-1-6 pure-menu search-options">
                <ul class="pure-menu-list">
                      <li class="pure-menu-item pure-menu-selected"><a href="search.php?query=<?php echo htmlspecialchars($query_txt); ?>&post" class="pure-menu-link">Twirps</a></li>
                      <li class="pure-menu-item"><a href="search.php?query=<?php echo htmlspecialchars($query_txt); ?>&user" class="pure-menu-link">Users</a></li>
                  </ul>
            </div>
            <div class="pure-u-5-6">
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
