var $slider = $('.bxslider').bxSlider();
$(document).ready(function () {
    getHomeTweets();

    $("#searchPublic").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: 'publicSearch.php',
                type: 'post',
                data: {searchText: request.term},
                dataType: "json",
                success: function (result) {
                    response($.map(result, function (item)
                    {
                        return{
                            label: item.screen_name,
                            value: item.screen_name,
                            avatar: item.profile_image_url
                        }
                    }));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            $("#searchPublic").val(ui.item.value);
            var user_name = ui.item.value;
            getUserTweets(user_name);
            return false;
        },
        focus: function (event, ui) {
            $(".ui-helper-hidden-accessible").hide();
            event.preventDefault();
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        var inner_html = '<img style="height:25px;width:25px;border-radius:50%;margin-right:10px" src="' + item.avatar + '">' + item.label;
        return $("<li></li>")
                .data("item.autocomplete", item)
                .append(inner_html)
                .appendTo(ul);
    };


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
    if ($("#download_link").length > 0) {
        $("#download_link").remove();
    }
    var dlink = '<a href="downloadTweets.php?uname=' + userScreenName + '"  id="download_link" target="_blank"><img src="dist/images/download_tweets.png" alt="Download Tweets of ' + userScreenName + '" title="Download Tweets of ' + userScreenName + '" style="float: right"/></a>';
    $("#download_tweets").append(dlink);
    $.ajax({
        type: "POST",
        url: 'userTweets.php',
        data: {'username': userScreenName},
        success: function (data) {
            var user_tweets = $.parseJSON(data);
            $("#tweets_title").html("@" + userScreenName + "'s Tweets");
            $("#download_tweets").css('display', 'block');
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
