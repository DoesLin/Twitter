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
                        <?php \View\Partials\Hashtag\hashtag_list($related_h); ?>
                    </div>
                </div>
            </div>
            <div class="pure-u-2-3">
                <div class="block">
                    <form class="pure-form write-twirp inner-block" action="post.php" method="post">
                        <fieldset>
                            <textarea name="text" rows="1">#<?php echo htmlspecialchars($name); ?> </textarea>
                            <button type="submit" class="pure-button pure-button-primary">Twirp</button>
                        </fieldset>
                    </form>

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
