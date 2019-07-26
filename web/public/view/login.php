<!DOCTYPE html>
<html>

<head>
    <title>LogIn</title>
    <style>
        .container {
            width: 100%;
            height: 100%;
        }

        body {
            margin: 0;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .articles-wrapper {
            width: 700px;
            margin: 0 auto;
            padding: 30px;
            background: #fafafa;
            border-radius: 5px;
        }

        .single-article {
            background: #ffffff;
            border-radius: 5px;
            padding: 30px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .add-article-wrapper {
            width: 700px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 30px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .add-comment-wrapper {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .comments-header {
            width: 100%;
        }

        .comment-wrapper {
            margin-bottom: 15px;
        }

        .show-comments-btn {
            left: 100px;
            bottom: 40px;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333;
        }

        li {
            float: left;
        }

        li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        li a:hover:not(.active) {
            background-color: #111;
        }

        .active {
            background-color: #4CAF50;
        }
        .menu-item {
            float: right;
        }
    </style>
</head>

<body>
<div class="navbar">
    <ul>
        <? if (!$userData['isLogged']) { ?>
            <li class="menu-item"><a href="?route=signup">Sign Up</a></li>
            <li class="menu-item"><a href="?route=login">Login</a></li>
        <? } else { ?>
            <li class="menu-item"><a href="?route=logout">Logout</a></li>
            <li class="menu-item"><?=$userData['userName']?></li>
        <? } ?>
    </ul>
</div>
<div class="container">
    <div class="add-article-wrapper">
        <hr>
        <footer>
            <h3>Log In</h3>
            <form action="/?route=login" id="article" method="post">
                Username: <input type="text" name="userName">
                Password: <input type="password" name="userPassword">
                <input type="submit">
            </form>
        </footer>
    </div>
</div>

</body>

</html>
