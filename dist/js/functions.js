var $slider = $('.bxslider').bxSlider();
$(document).ready(function () {
    getHomeTweets();

    $("#searchTxt").keyup(function () {
        var str = $("#searchTxt").val();
        $.ajax({
            type: "POST",
            url: 'search.php',
            data: {'q': str},
            success: function (response) {
                var data = $.parseJSON(response);
                if (str != "") {
                    $("#followerList").css('display', 'block');
                }
                var li = "";
                $.each(data, function (index, element) {
                    var nm = '"' + element.screen_name + '"';
                    li = li + "<a onclick='getUserTweets(" + nm + ")'><li class='f_li'>" + element.name + "</li></a>";
                });
                $("#f_ul").html(li);
                $("#f_ul").show();

            }
        });
    });
});

function getHomeTweets() {
    $.ajax({
        type: "POST",
        url: 'homeTweets.php',
        success: function (data) {
            var home_tweets = $.parseJSON(data);           
            $("#tweets_title").html("Recent Tweets");
            $.each(home_tweets, function (index, element) {
                var li = "<li>";
                li += "<table><tr><td>";
                li += "<a href='https://twitter.com/'" + element.user_screen_name + "id='userProfile'>";
                li += "<img src=" + element.user_profile_image + " id='tweet_profile_image'>";
                li += "<b id='tweet_name'>" + element.user_name + "</b>";
                if (element.verified == true) {
                    li += "<img src='dist/images/verified.ico' id='tweet_verified'>";
                }
                li += "<p id='tweet_screen_name'>@" + element.user_screen_name + "</p>";
                li += "</a></td></tr><tr><td>";
                li += "<p id='tweet_text'>" + element.tweet_text + "</p>";
                li += "</td></tr>";
                var tweet_media = element.media_image;
                if (tweet_media != null) {
                    li += "<tr><td>";
                    $.each(tweet_media, function (i, e) {
                        li += "<img id='tweet_media' src=" + e.media_url + ">";
                    });
                    li += "</td></tr>";
                }
                li += "<tr><td>" + element.date_time + "</td></tr>";

                li += "<tr><td>";
                li += "<a href='https://twitter.com/intent/tweet/?in_reply_to=" + element.tweet_id + "' target='_blank'>";
                li += "<img src='dist/images/reply.png' alt='Reply' title='Reply' style='float: left;'/> ";
                li += "<p class='tweet_options' > </p></a>";

                li += "<a href='https://twitter.com/intent/retweet?tweet_id=" + element.tweet_id + "' target='_blank'>";
                li += "<img src='dist/images/retweet.png' alt='Retweet' title='Retweet' style='float: left;'/> ";
                li += "<p class='tweet_options'>" + element.retweet_count + "</p></a>";

                li += "<a href='https://twitter.com/intent/favorite?tweet_id=" + element.tweet_id + "' target='_blank'>";
                li += "<img src='dist/images/like.png' alt='Like' title='Like' style='float: left;'/> ";
                li += "<p class='tweet_options' > " + element.likes_count + "</p></a>";
                li += "</td></tr></table></li>";
                $(".bxslider").append($(li));
            });
            $slider.reloadSlider();
        }
    });
}

function getUserTweets(userScreenName) {
    $('#searchTxt').val(userScreenName);
    $("#f_ul").hide();
    console.log(userScreenName);
    $.ajax({
        type: "POST",
        url: 'userTweets.php',
        data: {'username': userScreenName},
        success: function (data) {
            var user_tweets = $.parseJSON(data);
            $("#tweets_title").html("@" + userScreenName + "'s Tweets");
            $(".bxslider").empty();
            $.each(user_tweets, function (index, element) {
                var li = "<li>";
                li += "<table><tr><td>";
                li += "<a href='https://twitter.com/'" + element.user_screen_name + "id='userProfile'>";
                li += "<img src=" + element.user_profile_image + " id='tweet_profile_image'>";
                li += "<b id='tweet_name'>" + element.user_name + "</b>";
                if (element.verified == true) {
                    li += "<img src='dist/images/verified.ico' id='tweet_verified'>";
                }
                li += "<p id='tweet_screen_name'>@" + element.user_screen_name + "</p>";
                li += "</a></td></tr><tr><td>";
                li += "<p id='tweet_text'>" + element.tweet_text + "</p>";
                li += "</td></tr>";
                var tweet_media = element.media_image;
                if (tweet_media != null) {
                    li += "<tr><td>";
                    $.each(tweet_media, function (i, e) {
                        li += "<img id='tweet_media' src=" + e.media_url + ">";
                    });
                    li += "</td></tr>";
                }
                li += "<tr><td>" + element.date_time + "</td></tr>";

                li += "<tr><td>";
                li += "<a href='https://twitter.com/intent/tweet/?in_reply_to=" + element.tweet_id + "' target='_blank'>";
                li += "<img src='dist/images/reply.png' alt='Reply' title='Reply' style='float: left;'/> ";
                li += "<p class='tweet_options' > </p></a>";

                li += "<a href='https://twitter.com/intent/retweet?tweet_id=" + element.tweet_id + "' target='_blank'>";
                li += "<img src='dist/images/retweet.png' alt='Retweet' title='Retweet' style='float: left;'/> ";
                li += "<p class='tweet_options'>" + element.retweet_count + "</p></a>";

                li += "<a href='https://twitter.com/intent/favorite?tweet_id=" + element.tweet_id + "' target='_blank'>";
                li += "<img src='dist/images/like.png' alt='Like' title='Like' style='float: left;'/> ";
                li += "<p class='tweet_options' > " + element.likes_count + "</p></a>";
                li += "</td></tr></table></li>";
                $(".bxslider").append($(li));
            });
            $slider.reloadSlider();
        }

    });
}
