<?php function main_template($vars, $content) { ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A layout example that shows off a responsive email layout.">

    <title>Twirper</title>

    <link rel="stylesheet" href="/css/pure/pure-min.css">
    <link rel="stylesheet" href="/css/custom.css"> 
</head>
<body>
<div id="layout" class="content pure-g">
    <div id="nav" class="pure-u-1">
        <div class="nav-inner">
            <div class="pure-menu-horizontal">
                <ul class="pure-menu-list">
                    <li class="pure-menu-item"><a href="index.php" class="pure-menu-link">Home</a></li>
                    <?php if(Session\is_authentificated()) {
                        $n = \Model\Notification\list_all_notifications(Session\get_user()->id);
                        $nb_notif = count(array_filter($n,
                            function($el) {
                                return $el->reading_date === null;
                            }
                        ));
                        $nb_notif = $nb_notif > 0 ? " ($nb_notif)" : "";
                    ?>
                    <li class="pure-menu-item"><a href="notification.php" class="pure-menu-link">Notifications<?php echo $nb_notif; ?></a></li>
                    <?php }?>
                    <li class="pure-menu-item-separator"></li>
                    <li class="pure-menu-item">
                        <div class="search-bar">
                            <form class="pure-form" action="search.php" method="get">
                                <input type="hidden" name="post"/>
                                <input type="text" name="query" class="pure-input-rounded" placeholder="Search"/>
                            </form>
                        </div>
                    </li>
                    <?php if(Session\is_authentificated()) { ?>
                    <li class="pure-menu-item">
                        <a class="pure-menu-link" href="profile.php">My profile</a>
                    </li>
                    <li class="pure-menu-item">
                        <a class="pure-menu-link" href="logout.php">Log out</a>
                    </li>
                    <?php } else {?>
                    <li class="pure-menu-item">
                        <a class="pure-menu-link" href="login.php">Log in</a>
                    </li>
                    <li class="pure-menu-item">
                        <a class="pure-menu-link" href="signup.php">Sign up</a>
                    </li>
                    <?php } ?>
                </ul>                
            </div>
        </div>
    </div>
    <?php
        $modal = Session\get_modal();
        if($modal) {
    ?>
    <div class="pure-u-1-3"></div>
    <div class="pure-u-1-3 block modal modal-<?php echo $modal->type; ?>">
        <div class="inner-block"><?php echo $modal->content; ?></div>
    </div>
    <div class="pure-u-1-3"></div>
    <?php     
        }
    ?>
<?php $content($vars); ?>
</div>

<script src="https://yui-s.yahooapis.com/3.17.2/build/yui/yui-min.js"></script>

<script>
    YUI().use('node-base', 'node-event-delegate', function (Y) {

        var menuButton = Y.one('.nav-menu-button'),
            nav        = Y.one('#nav');

        // Setting the active class name expands the menu vertically on small screens.
        menuButton.on('click', function (e) {
            nav.toggleClass('active');
        });

        // Your application code goes here...

    });
</script>
</body>
</html>

<?php } ?>
