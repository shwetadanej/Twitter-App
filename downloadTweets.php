<?php

session_start();
require 'autoload.php';
require 'config.php';
require 'lib/fpdf.php';

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

    if (isset($_REQUEST['download_home_tweets'])) {
        $tweets = $connection->get('statuses/home_timeline', ['count' => 200, 'exclude_replies' => true, 'screen_name' => $loggedIn_user, 'include_rts' => false]);
        $totalTweets[] = $tweets;
        $tweets_data = [];

        for ($count = 1; $count <= 195; $count ++) {
            foreach ($totalTweets as $items) {
                $data['user_screen_name'] = $items[$count]->user->screen_name;
                $data['user_name'] = $items[$count]->user->name;
                $tweet_text = $items[$count]->text;
                $tweet_text = preg_replace('@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@', '<a href="$1">$1</a>', $tweet_text);
                $tweet_text = preg_replace('/@(\w+)/', '<a href="http://twitter.com/$1">@$1</a>', $tweet_text);
                $tweet_text = preg_replace('/\s+#(\w+)/', ' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $tweet_text);
                $data['tweet_text'] = $tweet_text;
                $tweets_data[] = $data;
            }
        }
        $type = $_REQUEST["dType"];

        if ($type == "json") {

            $fp = fopen("tweets.json", "w");
            fwrite($fp, json_encode($tweets_data));
            fclose($fp);
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename=' . basename('tweets.json'));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize('tweets.json'));
            readfile('tweets.json');
            ignore_user_abort(true);
            unlink('tweets.json');
            exit();
        } else if ($type == "csv") {

            header('Content-Disposition: attachment; filename="tweets.csv";');
            header("Pragma: no-cache");
            header("Expires: 0");
            $fp = fopen('tweets.csv', 'w');
            $title = array_keys($tweets_data[0]);
            fputcsv($fp, $title);
            foreach ($tweets_data as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
            readfile('tweets.csv');
            ignore_user_abort(true);
            unlink('tweets.csv');
            exit();
        }
    }

    $u_name = $_REQUEST['uname'];
    if (isset($u_name)) {
        
        $total_tweets = array();
        $current_tweets = array();
        $last_id = 0;
        $user_tweets = $connection->get('statuses/user_timeline', array('screen_name' => $u_name, 'count' => 200));
        $last_id = end($user_tweets)->id_str;
        foreach ($user_tweets as $value) {
            if (isset($value->text)) {
                $current_tweets[] = $value->text;
            }
        }
        $total_tweets = array_merge($total_tweets, $current_tweets);
        while (count($current_tweets) > 1) {
            $new_user_tweets = $connection->get('statuses/user_timeline', array('screen_name' => $u_name, 'count' => 200, 'max_id' => $last_id));
            $current_tweets = array();
            foreach ($new_user_tweets as $value) {
                if (isset($value->text)) {
                    $current_tweets[] = $value->text;
                }
            }
            $last_id = end($new_user_tweets)->id_str;
            $total_tweets = array_merge($total_tweets, $current_tweets);
        }
        array_pop($total_tweets);
        $total_tweets = $total_tweets;

        $f_name = "tweets_of_" . $u_name . ".pdf";
        $title = "Tweets of " . $u_name;
        $fp = fopen($f_name, 'w');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetTitle($title);
        $pdf->Cell(80);
        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(30, 10, $title, 0, 0, 'C');
        $pdf->Ln(20);

        $i = 1;
        foreach ($total_tweets as $val) {
            $tweet = $i . ") " . $val;
            $pdf->SetFont('Times', '', 12);
            $pdf->MultiCell(190, 5, $tweet, 0);
            $pdf->Ln(7);
            $i++;
        }

        $pdf->Output("D", $f_name);
        header("Content-Disposition: attachment; filename=" . urlencode($f_name));
        header("Content-Type: application/octet-stream");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize($f_name));
        fclose($fp);
        readfile($f_name);
        unlink($f_name);
        exit();
    }

}

