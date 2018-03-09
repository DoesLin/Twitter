<?php
namespace View\Partials\Post;

function post($post, $html_classes="") {
?>
                    <div class="post inner-block <?php echo $html_classes; ?>">
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
                                <a class="link-author" href="user.php?username=<?php echo htmlspecialchars($post->author->username); ?>">
                                    <?php echo htmlspecialchars($post->author->name); ?> (<?php echo htmlspecialchars($post->author->username); ?>)
                                </a>
                            </div>
                            <a class="link-post" href="post.php?id=<?php echo htmlspecialchars($post->id); ?>">
                                <div class="text"><?php
    $p = htmlspecialchars($post->text);
    $p = \Model\Post\parse_mentions($p);
    $p = \Model\Post\parse_hashtags($p);
    echo $p;
?></div>
                            </a>
                        </div>

                    </div>
<?php
}
?>
