<?php
/*
  Method Description :
  1. Here i am using multiple ajax call to get data and merge those data into common variable in ajax success.
  1. After getting total status_count of user and dividing it by 3200(limit) to get total loop count and store it in session.
  2. create another session varible called current loop with value 1.
  3. check if is not max id set then get 3200 tweets , and increament current_loop value by 1 and store max_id in session
  4. In next ajax call , script will check current_loop value is less than or equal to total loop,
  if yes then next 3200 tweets will fetched ,
  5. When current and total loop becomes equal, it will go in else condition and replace current value by 0
  and pass success in json encode
  6. At last in ajax success, check if current loop value is 0 then store total data into pdf file.
 */


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

    if (isset($_REQUEST['all_tweets'])) {

        $getUserInfo = $connection->get("users/show", array('screen_name' => 'tamannaahspeaks'));
        $status_count = $getUserInfo->statuses_count;
        $_SESSION['total_loop'] = ceil($status_count / 3200);
        $current_loop = 1;

        if (!isset($_SESSION['max_id'])) {
            $tweets = $connection->get('statuses/user_timeline', ['count' => 200, 'screen_name' => 'iamsrk']);
            $totalTweets[] = $tweets;
            $page = 0;
            for ($count = 0; $count < 3200; $count += 200) {
                $max = count($totalTweets[$page]) - 1;
                $_SESSION['max_id'] = $totalTweets[$page][$max]->id_str;
                $tweets = $connection->get('statuses/user_timeline', ['count' => 200, 'max_id' => $_SESSION['max_id'], 'screen_name' => 'tamannaahspeaks']);
                $totalTweets[] = $tweets;
                $page += 1;
            }
            foreach ($totalTweets as $page) {
                foreach ($page as $key) {
                    $data['tweet_text'] = $key->text;
                    $_SESSION['max_id'] = $key->id_str;
                    $totalData[] = $data;
                }
            }
            $_SESSION['current_loop'] = $current_loop + 1;
            echo json_encode($totalData);
            die;
        } else if ($_SESSION['current_loop'] <= $_SESSION['total_loop']) {
            for ($count = 0; $count < 3200; $count += 200) {
                $tweets = $connection->get('statuses/user_timeline', ['count' => 200, 'max_id' => $_SESSION['max_id'], 'screen_name' => 'tamannaahspeaks']);
                $totalTweets[] = $tweets;
                $page += 1;
            }
            foreach ($totalTweets as $page) {
                foreach ($page as $key) {
                    $data['tweet_text'] = $key->text;
                    $_SESSION['max_id'] = $key->id_str;
                    $totalData[] = $data;
                }
            }
            $_SESSION['current_loop'] = $_SESSION['current_loop'] + 1;
            echo json_encode($totalData);
            die;
        } else {
            $_SESSION['current_loop'] = 0;
            $response_array['status'] = 'success';
            json_encode($response_array);
            die;
        }
    }
}
?>
<script src="dist/js/jspdf.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        downloadAllTweets();

    });


    function downloadAllTweets() {
        $.ajax({
            url: 'all_tweets.php',
            data: {'all_tweets': 'all_tweets'},
            type: 'post',
            success: function (data) {
                var totalData = JSON.parse(data);
                var current_loop = '<?php echo $_SESSION['current_loop']; ?>';
                var timeInt = setInterval(function () {
                    $.ajax({
                        url: 'all_tweets.php',
                        data: {'all_tweets': 'all_tweets'},
                        type: 'post',
                        success: function (d) {
                            d = JSON.parse(d);
                            totalData = totalData.concat(d);
                            function generatePDF() {
                                if (current_loop === 0) {
                                    var doc = new jsPDF("p", "mm", "a4");
                                    doc.setFontSize(12);
                                    doc.setFont("times");
                                    var lHeight = 20;
                                    for (i = 0; i < totalData.length; ++i) {
                                        lines = doc.splitTextToSize(totalData[i], 7.5);
                                        doc.text(20, lHeight, totalData[i]);
                                        lHeight = lHeight + 7;
                                    }
                                    doc.save('all_tweets.pdf');
                                    clearInterval(timeInt);
                                }
                            }
                            generatePDF();
                        }
                    });
                }, 960000);
            }
        });
    }
</script>
