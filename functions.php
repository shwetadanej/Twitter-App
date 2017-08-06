<?php

session_start();
require 'autoload.php';
require 'config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

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
    
    $userInfo = $_REQUEST['loggedIn'];
    $homeTweetsPost = $_REQUEST['homeTweeetPost'];
    $username = $_REQUEST['username'];
    $q = $_REQUEST["q"];
    $fList = $_REQUEST["fList"];
    
    // user info
    
    if(isset($userInfo)){
        $data[];
        $uInfo[];
        $data['screen_name'] = $user->screen_name;
        $data['profile_img'] = $user->profile_image_url;
        $uInfo[] = $data;
        echo json_encode($uInfo);
    }

    
    // display home tweets
    
    if (isset($homeTweetsPost)) {
        $data = [];
        $tweets = $connection->get('statuses/home_timeline', ['count' => 50, 'exclude_replies' => true, 'screen_name' => $loggedIn_user, 'include_rts' => false]);
        $totalTweets[] = $tweets;
        $tweets_data = [];

        for ($count = 1; $count <= 10; $count ++) {
            foreach ($totalTweets as $items) {
                $data['user_screen_name'] = $items[$count]->user->screen_name;
                $data['user_name'] = $items[$count]->user->name;
                $data['verified'] = $items[$count]->user->verified;
                $data['user_profile_image'] = $items[$count]->user->profile_image_url;
                $tweet_text = $items[$count]->text;
                $tweet_text = preg_replace('@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@', '<a href="$1">$1</a>', $tweet_text);
                $tweet_text = preg_replace('/@(\w+)/', '<a href="http://twitter.com/$1">@$1</a>', $tweet_text);
                $tweet_text = preg_replace('/\s+#(\w+)/', ' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $tweet_text);
                $data['tweet_text'] = $tweet_text;
                $data['date_time'] = strftime('%h %d', strtotime($items[$count]->created_at));
                $data['tweet_id'] = $items[$count]->id;
                $data['retweet_count'] = $items[$count]->retweet_count;
                $data['likes_count'] = $items[$count]->favorite_count;
                $data['media_image'] = $items[$count]->entities->media;
                $data['link_to_tweet'] = $items[$count]->entities->urls;
                $data['link_to_tweet_url'] = $link_to_tweet->url;
                $tweets_data[] = $data;
            }
        }
        echo json_encode($tweets_data);
    }

    // display user tweets
    
    if (isset($username)) {
        $data = [];
        $tweets = $connection->get('statuses/user_timeline', ['count' => 50, 'exclude_replies' => true, 'screen_name' => $username, 'include_rts' => false]);
        $totalTweets[] = $tweets;
        $c = count($totalTweets);
        $tweets_data = [];
        $c = $c <= 9 ? $c = $c - 1 : $c = 9;

        for ($count = 1; $count <= $c; $count ++) {
            foreach ($totalTweets as $items) {
                $data['user_screen_name'] = $items[$count]->user->screen_name;
                $data['user_name'] = $items[$count]->user->name;
                $data['verfied'] = $items[$count]->user->verified;
                $data['user_profile_image'] = $items[$count]->user->profile_image_url;
                $tweet_text = $items[$count]->text;
                $tweet_text = preg_replace('@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@', '<a href="$1">$1</a>', $tweet_text);
                $tweet_text = preg_replace('/@(\w+)/', '<a href="http://twitter.com/$1">@$1</a>', $tweet_text);
                $tweet_text = preg_replace('/\s+#(\w+)/', ' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $tweet_text);
                $data['tweet_text'] = $tweet_text;
                $data['date_time'] = strftime('%h %d', strtotime($items[$count]->created_at));
                $data['tweet_id'] = $items[$count]->id;
                $data['retweet_count'] = $items[$count]->retweet_count;
                $data['likes_count'] = $items[$count]->favorite_count;
                $data['media_image'] = $items[$count]->entities->media; //array (media_url) , for img 
                $data['reply_count'] = $items[$count]->favorite_count;
                $data['link_to_tweet'] = $items[$count]->entities->urls; //array (url) , for single tweet
                $data['link_to_tweet_url'] = $link_to_tweet->url;
                $tweets_data[] = $data;
            }
        }
        echo json_encode($tweets_data);
    }

    // search
    
    if (isset($q)) {
        $data = [];
        $dat = [];
        $hint = "";
        $followers_list = $connection->get('followers/list', ['screen_name' => $loggedIn_user,]);
        $followers = $followers_list->users;

        $c = count($followers_list->users);
        for ($count = 0; $count < $c; $count ++) {
            $data['name'] = $followers[$count]->name;
            $data['screen_name'] = $followers[$count]->screen_name;
            $dat[] = $data;
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
                    $fdata[] = $fd;
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

    // disply followers
    
    if (isset($fList)) {
        $data = [];
        $followers_list = $connection->get('followers/list', ['screen_name' => $loggedIn_user,]);
        $followers = $followers_list->users;
        $f[];

        $c = count($followers);
        $c = $c <= 9 ? $c = $c - 1 : $c = 9;
        for ($count = 0; $count <= $c; $count ++) {

            $data['profile_img_url'] = $followers[$count]->profile_image_url;
            $data['name'] = $followers[$count]->name;
            $data['screen_name'] = $followers[$count]->screen_name;
            $f[] = $data[];
        }

        echo json_encode($f);
    }
    
    
}
?>

