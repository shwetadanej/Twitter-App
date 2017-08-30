<?php

session_start();
require 'autoload.php';
require 'config.php';
require 'lib/fpdf.php';

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

    // Download Home Tweets

    if (isset($_REQUEST['download_home_tweets'])) {
        $total_tweets = array();
        $current_tweets = array();
        $data = array();
        $last_id = 0;
        $home_tweets = $connection->get('statuses/home_timeline', array('screen_name' => $loggedIn_user, 'count' => 200));
        $last_id = end($home_tweets)->id_str;
        foreach ($home_tweets as $value) {
            if (isset($value->text)) {
                $data['user_screen_name'] = $value->user->screen_name;
                $data['user_name'] = $value->user->name;
                $data['tweet_text'] = $value->text;
                $current_tweets[] = $data;
            }
        }
        $total_tweets = array_merge($total_tweets, $current_tweets);
        while (count($current_tweets) > 1) {
            $new_home_tweets = $connection->get('statuses/home_timeline', array('screen_name' => $loggedIn_user, 'count' => 200, 'max_id' => $last_id));
            $current_tweets = array();
            foreach ($new_home_tweets as $value) {
                if (isset($value->text)) {
                    $data['user_screen_name'] = $value->user->screen_name;
                    $data['user_name'] = $value->user->name;
                    $data['tweet_text'] = $value->text;
                    $current_tweets[] = $data;
                }
            }
            $last_id = end($new_home_tweets)->id_str;
            $total_tweets = array_merge($total_tweets, $current_tweets);
        }
        array_pop($total_tweets);
        $total_tweets = $total_tweets;
        $type = $_REQUEST["dType"];

        if ($type == "json") {

            $fName = "json_home_tweets" . time() . ".json";
            $fp = fopen($fName, "w");
            fwrite($fp, json_encode($total_tweets));
            fclose($fp);
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename=' . basename($fName));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fName));
            readfile($fName);
            ignore_user_abort(true);
            unlink($fName);
            exit();
        } 
        else if ($type == "csv") {
            $fName = "csv_home_tweets" . time() . ".csv";
            header('Content-Disposition: attachment; filename=' . $fName);
            header("Pragma: no-cache");
            header("Expires: 0");
            $fp = fopen($fName, 'w');
            $title = array_keys($total_tweets[0]);
            fputcsv($fp, $title);
            foreach ($total_tweets as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
            readfile($fName);
            ignore_user_abort(true);
            unlink($fName);
            exit();
        } 
        elseif ($type == "pdf") {

            $f_name = "pdf_home_tweets" . time() . ".pdf";
            $fp = fopen($f_name, 'w');

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetTitle("Home Tweets");
            $pdf->Cell(80);
            $pdf->SetFont('Times', 'B', 16);
            $pdf->Cell(30, 10, $title, 0, 0, 'C');
            $pdf->Ln(20);

            $i = 1;
            foreach ($total_tweets as $val) {
                $uName = $i . ")Screen Name: " . $val['user_screen_name'];
                $tweet = "Tweet: " . $val['tweet_text'];
                $pdf->SetFont('Times', '', 12);
                $pdf->MultiCell(190, 5, $uName, 0);
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

    // Download User Tweets

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

        $f_name = "tweets_of_" . $u_name . time() . ".pdf";
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

