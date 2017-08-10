|LOGO|

.. |LOGO| image:: https://github.com/shwetadanej/Twitter-App/blob/master/dist/images/if_twitter_313634.png 

Documentation
-------------
* `An Introduction <https://github.com/shwetadanej/Twitter-App>`_
* `Authentication <https://github.com/shwetadanej/Twitter-App/blob/master/callback.php>`_
* `Home Tweets <https://github.com/shwetadanej/Twitter-App/blob/master/homeTweets.php>`_
* `User Tweets <https://github.com/shwetadanej/Twitter-App/blob/master/userTweets.php>`_
* `Followers <https://github.com/shwetadanej/Twitter-App/blob/master/home.php>`_
* `Search Public Users <https://github.com/shwetadanej/Twitter-App/blob/master/publicSearch.php>`_
* `Download Tweets <https://github.com/shwetadanej/Twitter-App/blob/master/downloadTweets.php>`_

Some Example
------------

First, authenticate with your application credentials::

	from TwitterAPI import TwitterAPI
	api = TwitterAPI(consumer_key, consumer_secret, access_token_key, access_token_secret)

Home Tweets::

	$tweets = $connection->get('statuses/home_timeline', ['count' => 50, 'exclude_replies' => true, 'screen_name' => 'any name', 'include_rts' => false]);
	print_r($tweets)

User Tweets::

	$tweets = $connection->get('statuses/user_timeline', ['count' => 50, 'exclude_replies' => true, 'screen_name' => 'any name', 'include_rts' => false]);    
	print_r($tweets)

Followers List::

	$followers_list = $connection->get('followers/list', ['screen_name' => $loggedIn_user,]);
  print_r($followers_list)

Download Tweets in diff formats::

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
