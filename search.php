<?php
session_start();
require 'autoload.php';
require 'config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$data = [];
$dat =[];
if (!isset($_SESSION['access_token'])) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    header('Location: ' . $url);
} else {
    $access_token = $_SESSION['access_token'];
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $user = $connection->get("account/verify_credentials");
    $loggedIn_user = $user->screen_name;

    $q = $_REQUEST["q"];
    $hint = "";
    $followers_list = $connection->get('followers/list', ['screen_name' => $loggedIn_user,]);
    $followers = $followers_list->users;

    $c = count($followers_list->users);
    for ($count = 0; $count < $c; $count ++) {
        $data['name'] = $followers[$count]->name;
        $data['screen_name'] = $followers[$count]->screen_name;
        $dat[]=$data;
    }
    $fdata = [];
    $fd = [];
    if ($q !== "") {
        $q = strtolower($q);
        $len = strlen($q);
        foreach ($dat as $name) {
            $pos = strpos(strtolower(trim($name['name'])), $q);
            if ($pos !== FALSE) {
                $fd['name'] = $name['name'];
                $fd['screen_name'] = $name['screen_name'];
                $fdata[]=$fd;
                if ($hint === "") {
                    $hint .= $name;
                } else {
                    $hint .= ", $name";
                }
            }
        }
    }
}
echo json_encode($fdata);
?>