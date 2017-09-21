<?php
session_start();
if ($_SESSION['loggedIn_user']) {
    header('Location: http://beta.technonic.in/TwitterApp/home.php');
}
?>
<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset = "utf-8">
        <meta name = "viewport" content = "width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name = "description" content = "">
        <meta name = "author" content = "">
        <link rel = "icon" href = "dist/images/favicon.ico">

        <title>Twitter Signin</title>
        <link href = "dist/css/bootstrap.min.css" rel = "stylesheet">
        <link href = "dist/css/style.css" rel = "stylesheet" type = "text/css">
    </head>
    <body>
    <center>
        <div class = "container" style = "margin-top:5%;">
            <h2 class = "form-signin-heading">Sign in With</h2>
            <a href = "home.php"><img src = "dist/images/Login_to_twitter.png" alt = "Login into Twitter" title = "Login into Twitter"/></a>
        </div>
    </center>
    <script src = "dist/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

