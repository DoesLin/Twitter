<?php
namespace Model\Post;

/**
 * Get the list of used hashtags in message
 * @param text the message
 * @return an array of hashtags
 */
function extract_hashtags($text) {
    return array_unique(
        array_map(
            function($el) { return substr($el, 1); },
            array_filter(
                explode(" ", $text),
                function($c) {
                    return $c !== "" && $c[0] == "#";
                }
            )
        )
    );
}

/**
 * Get the list of mentioned users in message
 * @param text the message
 * @return an array of usernames
 */
function extract_mentions($text) {
    return array_unique(
        array_map(
            function($el) { return substr($el, 1); },
            array_filter(
                explode(" ", $text),
                function($c) {
                    return $c !== "" && $c[0] == "@";
                }
            )
        )
    );
}

/**
 * Parse a message text for hashtags (includes links)
 * @param text the message
 * @return the parsed message
 */
function parse_hashtags($text) {
    return implode(
        " ",    
        array_map(
            function($el) {
                if($el !== "" && $el[0] == "#") {
                    $n = substr($el, 1);
                    return '<span class="pseudo-link" onclick="document.location.href = \'/hashtag.php?name='.$n.'\'; return false">'.$el.'</span>';
                }
                return $el;
            },
            explode(" ", $text)
        )
    );
}


/**
 * Parse a message text for mentions (includes links)
 * @param text the message
 * @return the parsed message
 */
function parse_mentions($text) {
    return implode(
        " ",    
        array_map(
            function($el) {
                if($el !== "" && $el[0] == "@") {
                    $n = substr($el, 1);
                    return '<span class="pseudo-link" onclick="document.location.href = \'/user.php?username='.$n.'\'; return false">'.$el.'</a>';
                }
                return $el;
            },
            explode(" ", $text)
        )
    );
}

