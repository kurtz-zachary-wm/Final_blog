<?php
require 'sheets/databases.php';
require 'sheets/tags.php';

$database = new database;

function getToken() {
    if (isset($_COOKIE['token'])) {
        return $_COOKIE['token'];
    }
}

$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

if (@$_POST['delete']){
    $delete_id = $_POST['delete_id'];
    $database->query('DELETE FROM blog_post WHERE id = :id');
    $database->bind(':id', $delete_id);
    $database->execute();
}

if (@$post['update']){
    $title = $post['title'];
    $body = $post['post'];

    $database->query('UPDATE blog_post SET title = :title, post = :post WHERE id = :id');
    $database->bind(':title', $title);
    $database->bind(':post',$post);
    $database->execute();
}

if(@$post['submit']) {
    $token = getToken();
    $database->query('SELECT id FROM people WHERE token = :token');
    $database->bind(':token', $token);
    $id = $database->fetch()['id'];
    $title = $post['title'];
    $body = $post['post'];

    $database->query('INSERT INTO blog_post (title, post, author_id) VALUES (:title, :post, :author_id)');
    $database->bind(':title', $title);
    $database->bind(':post', $body);
    $database->bind(':author_id', $id);
    $database->execute();
    $users_id = $database->lastInsertId();
    if(isset($_POST['tag'])) {
        foreach ($_POST['tag'] as $value) {
            $database->query('INSERT INTO blog_post_tags (blog_post_id, tag_id) VALUES (:blog_post_id, :tag_id)');
            $database->bind(':blog_post_id', $users_id);
            $database->bind(':tag_id', $value);
            $database->execute();
        }
    }
}


$token = getToken();
$database->query('SELECT * FROM people WHERE token = :token');
$database->bind(':token', $token);
$rows = $database->resultset();
if(count($rows) > 0){

}
else {
    echo header('location: sheets/account.php');
}

$database->query('SELECT * FROM blog_post');
$rows = $database->resultset();
?>

<head>
    <meta charset="utf-8">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <span class="glyphicon glyphicon-pencil navbar-brand">Blog</span>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="index.php">Home page <span class="sr-only"></span></a></li>
                    <li class="dropdown"><a href="sheets/account.php">Account</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<div class="row row-content">
    <h1 style="text-align: center;" class="container">
    <span class="label label-default">
        Add Post
    </span>
    </h1>
    <form class="form-group-lg" style="text-align: center" method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
        <p style="padding: 5px;"></p>
        <input type="text" name="title" placeholder="Add a title..." /><br /><br />
        <textarea name="post" placeholder="Post body..." style="height: 100px; width: 500px;"></textarea><br /><br />
        <label>Post Tags</label><br>
        <?php
        $database->query('SELECT * FROM tags');
        $tags = $database->resultset();
        echo '<div class="btn-group" data-toggle="buttons">';
        foreach($tags as $row) {
            echo '<label class="btn btn-info"><input type="checkbox" name="tag[]" value="' . $row['id'] .'"> '. $row['name'] . '</label>&nbsp;';
        }
        echo '</div>';
        echo '<p style="padding: 5px"></p>';
        echo '<br />';
        ?>
        <button type="submit" name="submit" value="Submit" class="btn-sm btn-default">
            Submit
        </button>
    </form>
</div>
<h1 class="container" style="text-align: center; padding: 5px;">
    <span class="label label-default">
        Posts
    </span>
</h1>

<div class="container" style="text-align: center">
    <?php
    foreach($rows as $row) :
        ?>
        <div>
            <h3>
                <?php
                echo $row ['title'];
                ?>
            </h3>

            <h5>
                <?php
                $database->query('SELECT id, first_name, last_name, email, username FROM people WHERE id = :id LIMIT 1');
                $database->bind(':id',$row['author_id']);
                $author = $database->fetch();
                echo 'By ' .$author['username'];
                echo '<p style="padding: 1px;"></p> <br />';

                $database->query('SELECT t.id, t.name FROM blog_post p LEFT JOIN blog_post_tags pt ON p.id = pt.blog_post_id LEFT JOIN tags t ON pt.tag_id = t.id WHERE p.id = :pid');
                $database->bind(':pid', $row['id']);
                $displaytags = $database->resultset();
                foreach($displaytags as $displaytag) {
                    echo '<span class="label label-info">'. $displaytag['name'] .'</span>';
                }
                ?>
            </h5>
            <p>
                <?php
                echo $row['post'];
                ?>
            </p>
            <br />


            <form method="post" action="<?php $_SERVER['PHP_SELF']?>">
                <input type='hidden' name="delete_id" value="<?php echo $row ['id']; ?>">
                <input type="submit" name="delete" value="Delete">
            </form>
            <p style="padding: 20px;">
            </p>
        </div>
        <?php
    endforeach;
    ?>
</div>
<div class="navbar navbar-default navbar-fixed-bottom">
    <div class="container">
        <p class="navbar-text">
            Blog site company name.
        </p>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>