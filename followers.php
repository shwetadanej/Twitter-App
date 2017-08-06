<?php

session_start();
require 'autoload.php';
require 'config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$data = [];
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

    $profiles = array();
    $ids = $connection->get('followers/list');

    foreach ($ids->users as $implode) {
        $results = $connection->get('users/lookup', array('user_id' => $implode->id));
        foreach ($results as $profile) {
            $name = [
                'name' => $profile->name,
                'screen_name' => $profile->screen_name,
            ];
            $data[] = $name;
        }
    }
    $fdata = [];
    $q = 'ri';
    if ($q !== "") {
        $q = strtolower($q);
        $len = strlen($q);
        foreach ($data as $name) {
            $pos = strpos(strtolower(trim($name['name'])), $q);
            if ($pos !== FALSE) {
                $fdata[] = $name['name'];
                if ($hint === "") {
                    $hint .= $name;
                } else {
                    $hint .= ", $name";
                }
            }
        }
    }

    echo json_encode($fdata);
}