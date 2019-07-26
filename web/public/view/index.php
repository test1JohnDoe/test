<!DOCTYPE html>
<html>

<head>
    <title>Guest Book</title>
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
    <script type="text/javascript">
        function showComments(button, id) {
            var x = document.getElementsByClassName('post_' + id + '_comment');
            Array.prototype.forEach.call(x, function(el) {
                if (el.style.display === "none") {
                    el.style.display = "block";
                    button.innerHTML = 'Hide comments';
                } else {
                    el.style.display = "none";
                    button.innerHTML = 'Show comments';
                }
            });
        }
    </script>
</head>

<body>
<div class="navbar">
    <ul>
        <? if (!$userData['isLogged']) { ?>
            <li class="menu-item"><a href="?route=signup">Sign Up</a></li>
            <li class="menu-item"><a href="?route=login">Login</a></li>
        <? } else { ?>
            <li class="menu-item"><a href="?route=logout">Logout</a></li>
            <li class="menu-item"><a><?=$userData['userName']?></a></li>
        <? } ?>
    </ul>
</div>
<div class="container">
    <div class="articles-wrapper">
        <section>
            <? foreach ($viewData as $post) { ?>
                <div class="single-article">
                    <article>
                        <header>
                            <h1><?=$post->getTitle()?></h1> by <?=$post->getUser()->getId() === $userData['userId'] ? 'me' : $post->getUser()->getUserName()?>
                            (<time><?=$post->getCreateDate()?></time>)
                            <?if ($post->getUser()->getId() === $userData['userId']) { ?>
                                <a href="/?route=edit_post&post_id=<?=$post->getId()?>">Edit</a>
                                <form action="/?route=delete_post" id="article-comment" method="post">
                                    <input type="hidden" name="postId" value="<?=$post->getId()?>">
                                    <button type="submit" class="show-comments-btn">Delete</button>
                                </form>
                            <? } ?>
                        </header>
                        <p>
                            <?=$post->getText()?>
                        </p>
                        <hr>
                        <section>
                            <? if (count($post->getComments()) > 0) { ?>
                                <div class="comments-header">
                                    <h3>Comments</h3>
                                    <button class="show-comments-btn" onclick="showComments(this, <?=$post->getId()?>)">Hide comments</button>
                                </div>
                            <?}?>
                            <? foreach ($post->getComments() as $comment) { ?>
                                <div class="comment-wrapper post_<?=$post->getId()?>_comment">
                                    <article id="unique-id-1">
                                        <p><?=$comment->getTitle()?></p>
                                        <p><?=$comment->getText()?></p>
                                        <footer>Posted by <?=$comment->getUser()->getId() === $userData['userId'] ? 'me' : $comment->getUser()->getUserName()?> (<time><?=$comment->getCreateDate()?></time>)
                                        </footer>
                                    </article>
                                    <hr>
                                </div>
                            <?}?>
                        </section>
                    </article>
                    <? if ($userData['isLogged']) { ?>
                        <div class="add-comment-wrapper">
                            <footer>
                                <h3>Add new comment</h3>
                                <form action="/?route=add_post" id="article-comment" method="post">
                                    Title: <input type="text" name="title">
                                    <input type="submit">
                                    <input type="hidden" name="refId" value="<?=$post->getId()?>">
                                    <br>
                                    <textarea rows="4" cols="50" name="text">Enter text here...</textarea>
                                </form>
                            </footer>
                        </div>
                    <? } ?>
                </div>
            <? } ?>
        </section>
    </div>
    <? if ($userData['isLogged']) { ?>
        <div class="add-article-wrapper">
            <hr>
            <footer>
                <h3>Add new article</h3>
                <form action="/?route=add_post" id="article" method="post">
                    Title: <input type="text" name="title">
                    <input type="submit">
                </form>
                <br>
                <textarea rows="4" cols="50" name="text" form="article">Enter text here...</textarea>
            </footer>
        </div>
    <?}?>
</div>

</body>

</html>
