# Twitter-App

![Twitter-App Logo](/dist/images/if_twitter_313634.png)

Documentation

------------

* [An Introduction](https://github.com/shwetadanej/Twitter-App)
* [Authentication](https://github.com/shwetadanej/Twitter-App/blob/master/callback.php)
* [Home Tweets](https://github.com/shwetadanej/Twitter-App/blob/master/homeTweets.php)
* [User Tweets](https://github.com/shwetadanej/Twitter-App/blob/master/userTweets.php)
* [Followers](https://github.com/shwetadanej/Twitter-App/blob/master/home.php)
* [Search Public Users](https://github.com/shwetadanej/Twitter-App/blob/master/publicSearch.php)
* [Download Tweets](https://github.com/shwetadanej/Twitter-App/blob/master/downloadTweets.php)

Examples :

------------

1. [First Create a New App for twitter](https://apps.twitter.com/)  
2. Import Twitter Api [https://github.com/abraham/twitteroauth](https://github.com/abraham/twitteroauth)
3. [Authentication Example](https://github.com/sohaibilyas/twitter-api-php)

Authenticate with your application credentials:
	
	api = TwitterAPI(consumer_key, consumer_secret, access_token_key, access_token_secret)

Home Tweets: [Check Here](https://dev.twitter.com/rest/reference/get/statuses/home_timeline)

	$tweets = $connection->get('statuses/home_timeline', ['count' => 50, 'exclude_replies' => true, 'screen_name' => 'any name', 'include_rts' => false]);
	print_r($tweets)

User Tweets: [Check Here](https://dev.twitter.com/rest/reference/get/statuses/user_timeline)

	$tweets = $connection->get('statuses/user_timeline', ['count' => 50, 'exclude_replies' => true, 'screen_name' => 'any name', 'include_rts' => false]);    
	print_r($tweets)

Followers : [Check Here](https://dev.twitter.com/rest/reference/get/followers/list)

	$followers_list = $connection->get('followers/list', ['screen_name' => 'any name']);
  	print_r($followers_list)

Public Users : [Check Here](https://dev.twitter.com/rest/reference/get/users/search)

	$users = $connection->get('users/search', array('q' => 'any username or pagename'));
  	print_r($users)

Download Tweets in diff formats:

	Json :

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

  
	CSV :

        header('Content-Disposition: attachment; filename="tweets.csv";');
        header("Pragma: no-cache");
        header("Expires: 0");
        $fp = fopen('tweets.csv', 'w');
        foreach ($tweets_data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        readfile('tweets.csv');
        ignore_user_abort(true);
        unlink('tweets.csv');
        exit();
