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
    ?>

    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">
            <link rel="icon" href="dist/images/favicon.ico" >
            <link href="dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
            <link href="dist/css/tweets.css" rel="stylesheet" type="text/css"/>
            <link href="dist/css/jquery.bxslider.css" rel="stylesheet" type="text/css"/>
            <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
            <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
            <link href="dist/css/social_counters.css" rel="stylesheet" type="text/css"/>
            <title>Home</title>
        </head>

        <body>
            <div class="navbar navbar-dark bg-dark">
                <div class="container d-flex justify-content-between">
                    <img src="<?php echo $user->profile_image_url; ?>" id="loggedinProfile" style="border-radius: 50%;"/>
                    <a href="#" class="navbar-brand" style="margin-right: 55%;"><?php echo "@" . $loggedIn_user; ?> </a>

                    <form class="form-inline mt-2 mt-md-0" name="serachFrm"> 
                        <input class="form-control mr-sm-2 ui-autocomplete-input" type="text" name="searchTxt" id="searchTxt" placeholder="Search Followers" >
                        <div id="followerList" style="display: none;">
                            <ul id="f_ul"> </ul>
                        </div>  
                    </form>
                    <a href='logout.php' class="navbar-brand" style="text-align: right;">
                        <img src="dist/images/logout.png" />
                    </a>
                    
                </div>
            </div>
            <div class="tweets text-muted">
                <div class="container">

                    <div class="row"> 
                        <div class="col-12">
                            <center><h3 class="task_title" id="tweets_title"></h3></center>
                        </div>
                        <ul class="bxslider">

                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <center>
                                <h3 class="task_title">Followers</h3>
                            </center>
                        </div>
                        <div id="wrapper" class="square">

                            <?php
                            $followers_list = $connection->get('followers/list', ['screen_name' => $loggedIn_user,]);
                            $followers = $followers_list->users;
                            $c = count($followers);
                            $c = $c <= 9 ? $c = $c - 1 : $c = 9;
                            for ($count = 0; $count <= $c; $count ++) {
                                ?>
                                <a class="item twitter">
                                    <img id="follower_img" src="<?php echo $followers[$count]->profile_image_url ?>">
                                    <span class="count">
                                        <?php echo $followers[$count]->name; ?>  
                                    </span>
                                    <?php echo "@" . $followers[$count]->screen_name; ?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <br><br>
                    </div>


                    <div class="row"> 
                        <div class="col-12">
                            <h3 class="task_title">Download Tweets As</h3>
                        </div>
                        <div class="col-12">
                            <center>
                                <a class="btn btn-primary" href="downloadTweets.php?dType=json" role="button" target="_blank">JSON</a>
                                <a class="btn btn-primary" href="downloadTweets.php?dType=csv" role="button" target="_blank">CSV</a>
                            </center> 
                        </div>
                        <br>
                        <br>
                    </div>
                </div>
            </div>

            <footer>
                <div class="container">
                    <p class="float-right">
                        
                        <a href="#">Back to top</a>
                    </p>
                </div>
            </footer>

            <script src="http://code.jquery.com/jquery-1.10.2.js"type="text/javascript"></script>
            <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js" type="text/javascript"></script>
            <script src="dist/js/jquery.bxslider.min.js" type="text/javascript"></script>
            <script src="dist/js/popper.min.js" type="text/javascript"></script>
            <script src="dist/js/holder.min.js" type="text/javascript"></script>
            <script src="dist/js/bootstrap.min.js" type="text/javascript"></script>
            <script src="dist/js/ie10-viewport-bug-workaround.js" type="text/javascript"></script>
            <script>

                $(function () {
                    Holder.addTheme("thumb", {background: "#55595c", foreground: "#eceeef", text: "Thumbnail"});
                });
            </script>
            <script src="dist/js/functions.js" type="text/javascript"></script>
        </body>

    </html>
    <?php
}