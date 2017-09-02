# Twitter-App

![Twitter-App Logo](/dist/images/Login_to_twitter.png)

## Introduction

This web application is made in the interest of, 

1. getting logged in 
2. user's home tweets 
3. show their 10 random followers
4. search twitter users and 
5. displaying their tweets 
6. as well as downloading their tweets up to 3200 in 3 formats like CSV, JSON, PDF.


## Documentation

* [Authentication](https://github.com/shwetadanej/Twitter-App/blob/master/callback.php)
* [Home Tweets](https://github.com/shwetadanej/Twitter-App/blob/master/homeTweets.php)
* [User Tweets](https://github.com/shwetadanej/Twitter-App/blob/master/userTweets.php)
* [Followers](https://github.com/shwetadanej/Twitter-App/blob/master/home.php)
* [Search Public Users](https://github.com/shwetadanej/Twitter-App/blob/master/publicSearch.php)
* [Download Tweets](https://github.com/shwetadanej/Twitter-App/blob/master/downloadTweets.php)


## Examples

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


      
Added PDF download for user tweets : [Check Here](http://www.fpdf.org/)

        I have used FPDF library , check out above given link for more info.


## Created By

**Shweta Danej**

[Stackoverflow Profile](https://stackoverflow.com/users/6375123/shweta-danej?tab=profile)

[Twitter Profile](https://twitter.com/shweta_danej)

[Facebook Profile](https://www.facebook.com/shweta.danej)

