<?php
include 'databases.php';

    $database = new database();
    function generateToken() {
        $date = date(DATE_RFC2822);
        $rand = rand();
        return sha1($date.$rand);
    }

    function getToken() {
        if (isset($_COOKIE['token'])) {
            return $_COOKIE['token'];
        }
    }

    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


    if(@$_POST['submit']){
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $token = generateToken();

        $database->query('INSERT INTO people (first_name, last_name, username, password, email, token)
VALUES (:first_name, :last_name, :username, :password, :email, :token)');

        $database->bind(':first_name',$first_name);
        $database->bind(':last_name', $last_name);
        $database->bind(':username', $username);
        $database->bind(':password', $password);
        $database->bind(':email', $email);
        $database->bind(':token',$token);
        $database->execute();
        setcookie('token', $token, 0, '/');
        if ($database->lastInsertId()){
            echo '<p>Register successful!</p>';
            $token = generateToken();

        }

    }

    if (isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $database->query('SELECT * FROM people WHERE username = :username and password = :password ');
        $database->bind(':username', $username);
        $database->bind(':password', $password);
        $rows = $database->resultset();

        if(count($rows) > 0){
            echo 'Login successful!';
            $token = generateToken();
            setcookie('token', $token, 0, '/');
            $database->query('UPDATE people SET token = :token WHERE username = :username');
            $database->bind(':token',$token);
            $database->bind(':username', $username);
            $database->execute();
        }
        else {
            echo 'Login failed!';
        }
    }
?>
<head>
    <meta charset="utf-8">
    <link href="../bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../index.php"><span class="glyphicon glyphicon-pencil">Blog</span></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="../index.php">Home page <span class="sr-only"></span></a></li>
                    <li class="active"><a href="account.php">Account</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>
<div style="text-align: center">
    <h3>
        <span class="label label-info">
            Please login to access home page
        </span>
    </h3>
</div>
<div style="float:left; padding-left:560px;">
    <h3>
<span class="label label-default">
    Register
</span>
    </h3>
<p style="padding: 5px;">

</p>
<form class="form-group-lg" style="text-align: center" method="post" action="<?php $_SERVER['PHP_SELF']?>">
    <input type="text" name="first_name" placeholder="First name...">
    <p></p>
    <br />
    <input type="text" name="last_name" placeholder="Last name...">
    <p></p>
    <br />
    <input type="text" name="username" placeholder="Username...">
    <p></p>
    <br />
    <input type="password" name="password" placeholder="Password...">
    <p></p>
    <br />
    <input type="email" name="email" placeholder="Email..."><br />
    <p style="padding: 5px;">
    </p>
    <button type="submit" name="submit" value="Submit" class="btn-sm btn-default">
        Submit
    </button>

</form>
</div>
<div style="float: right; padding-right: 555px;">
<h3 style="padding-left: 85px;">
<span class="label label-default">
    Login
</span>
</h3>
<p style="padding: 5px;">

</p>
<form class="form-group-lg" style="text-align: center" method="post" action="<?php $_SERVER['PHP_SELF'] ?>" >
    <input type="text" name="username" placeholder="Username...">
    <p></p>
    <br />
    <input type="password" name="password" placeholder="Password...">
    <br />
    <p style="padding: 5px;">

    </p>
    <button type="submit" name="login" value="Submit" class="btn-sm btn-default">
        Submit
    </button>
</form>
</div>
<div class="navbar navbar-default navbar-fixed-bottom">
    <div class="container">
        <p class="navbar-text">
            Blog site company name.
        </p>
    </div>
</div>
<div>
    <center>
    <div style="margin-top: 450px;" >
        <form method="post" action="">
            <input type="submit" class="btn-lg btn-default" value="Logout" name="logOut">
        </form>
    </div>
    </center>
</div>
<script src="script.js"></script>
</body>