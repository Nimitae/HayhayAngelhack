<?php

// Load libraries
require_once('lib/XMPPHP/XMPP.php');
require_once('config.inc.php');

/**
 * Updates everyone's user interface with a message
 * @var $message
 */
function send_notification($message) {

    // Load configuration
    global $CONFIG;
    $conn = new XMPPHP_XMPP(
        $CONFIG['connect']->host,
        5222,
        $CONFIG['connect']->user,
        $CONFIG['connect']->pass,
        $CONFIG['connect']->resc);
    try {
        $conn->useEncryption(false);
        $conn->connect();
        $conn->processUntil('session_start');
        $conn->presence();
        $conn->message($CONFIG['receive'], $message);
        $conn->disconnect();
    } catch(XMPPHP_Exception $e) {
        error_log($e->getMessage());
    }
}

function get_last_feed_item() {
    global $CONFIG;         // Load configuration
    $rss = new lastRSS;     // Initialize lastRSS
    $rss->CDATA = 'content';
    if ($rs = $rss->get($CONFIG['rss'])) {
        if (isset($rs['items'][0]))
            return $rs['items'][0];
        else
            return false;
    }
    return false;
}

function feed_has_changed($url) {

    global $CONFIG;
    $changed = false;

    // Check to see if the file exists, and if it does, if
    // the URL has changed
    if (!file_exists($CONFIG['cachedir'] . 'cache.txt')) {
        $changed = true;
    } else if (file_get_contents($CONFIG['cachedir'] . 'cache.txt') != $url) {
        $changed = true;
    }

    // If the URL has indeed changed, update the version in the cache
    // and return true
    if ($changed) {
      //  file_put_contents($CONFIG['cachedir'] . 'cache.txt', $url);
        return true;
    }

    // Otherwise return false
    return false;

}

function last_item_html($item) {
    return <<< END
    <div class="item">
        <div class="item_title">
            <h2><a href="{$item['link']}"
 target="_blank">{$item['title']}</a></h2>
        </div>
        <div class="item_body">
            {$item['description']}
        </div>
    </div>
END;
}