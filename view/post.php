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
                    <div class="post inner-block main-post">
                        <div class="post-avatar">
                            <a href="user.php?username=<?php echo htmlspecialchars($post->author->username); ?>">
                                <img class="email-avatar" src="<?php
    $avatar = empty($post->author->avatar) ? '/images/default.jpg' : $post->author->avatar;
    echo htmlspecialchars($avatar); 
                                ?>" height="64" width="64">
                            </a>
                        </div>

                        <div class="post-content">
                            <div class="actions">
                                <?php if(\Session\is_authentificated() && $post->author->id == \Session\get_user()->id) { ?>
                                <a href="/post.php?id=<?php echo htmlspecialchars($post->id); ?>&destroy">×</a>
                                <?php } ?>
                            </div>
                            <div class="post-author">
                                <a href="user.php?username=<?php echo htmlspecialchars($post->author->username);?>">
                                    <?php echo $post->author->name; ?> (<?php echo htmlspecialchars($post->author->username); ?>)
                                </a>
                            </div>
                            <div class="text"><?php
    $p = htmlspecialchars($post->text);
    $p = \Model\Post\parse_mentions($p);
    $p = \Model\Post\parse_hashtags($p);
    echo $p;
?></div>
                        </div>
                        <div class="pure-g post-actions">
                            <?php
                                if($user) {
                                    if($liked) {?>
                            <div class="pure-u-1-3"><a href="post.php?id=<?php echo $post->id; ?>&unlike">Unlike</a> (<?php echo $stats->nb_likes; ?>)</div>
                            <?php   }
                                else {?>
                            <div class="pure-u-1-3"><a href="post.php?id=<?php echo $post->id; ?>&like">Like</a> (<?php echo $stats->nb_likes; ?>)</div>
                            <?php   }
                                }?>
                        </div>
                    </div>
                    <form class="pure-form write-twirp answer-twirp inner-block" action="post.php?id=<?php echo $post->id;?>" method="post">
                        <fieldset>
                            <textarea name="text" rows="1">@<?php echo htmlspecialchars($post->author->username); ?> </textarea>
                            <button type="submit" class="pure-button pure-button-primary">Respond</button>
                        </fieldset>
                    </form>

                    <?php foreach($responses as $response) {
                        \View\Partials\Post\post($response);
                        foreach($response->responses as $r) {
                            \View\Partials\Post\post($r);
                        }
                    ?>
                    <div class="thread-separator"></div>
                    <?php
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
