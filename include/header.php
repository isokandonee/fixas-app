<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fixas-Bank</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" id="bootstrap-css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700" rel="stylesheet">

    <link rel="stylesheet" href="../css/main.css">
    <script src="../script/index.js"></script>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../script/jquery.min.js"></script>
    <script src="../script/bootstrap.min.js"></script>

    
    <link rel="stylesheet" href="css/main.css">
    <script src="script/index.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="script/jquery.min.js"></script>
    <script src="script/bootstrap.min.js"></script>

    <meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, Fixas-bank App' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!--     Font     -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>

</head>
<body>
    <header>
        <nav class="navbar navbar-expand-sm bg-light">
            <div class="container col-sm-11">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link <?php if ($CURRENT_PAGE == "Index") {?>active<?php }?>" href="
                        <?php if (isset($_SESSION['email'])) { ?>
                            ../dashboard/index.php
                        <?php }
                            else{?>
                                #
                        <?php    }
                        ?>
                        ">
                            <?php 
                                if (isset($_SESSION['email'])) {
                                    echo "<b style='font-size:25px;'>". $_SESSION['first_name'] ." ". $_SESSION['last_name']."</b>";
                                }
                                else{
                                    echo "<b style='font-size:25px;'>Welcome to Fixas-Bank</b>";
                                }
                            ?>
                        </a>
                    </li>
                </ul>
                <?php 
                    if (isset($_SESSION['email'])) {
                        echo '
                        <form class="form-inline nav-right" action="../controller/logout.php" method="POST">
                            <button class="btn btn-primary" type="submit">Logout</button>
                        </form>';
                    }
                    else {
                        echo '
                        <form class="form-inline nav-right" action="controller/login.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="sdkajsdaksjdklasjdaklsdjalkjs938092qpwoalsdalsdasdasd">
                            <input class="form-control mr-sm-2" type="text" placeholder="Email" name="email">
                            <input class="form-control mr-sm-2" type="password" placeholder="Password" name="password">
                            <button class="btn btn-primary" type="submit">Login</button>
                        </form>';
                    }
                ?>
            </div>
        </nav>
    </header>