<!--
Starting Date: 16 Dec 2018
Name: login.php (Login)
Features:
  1) Jumbotron disappears when screen too small (i.e. when navbar is collapsed)
  2) Cancel navbar freezing (sticking to the top of the screen) when collapsed
  3) Decreases size of "Mingdao International Department" when text-wrapped

-->
<?php session_start(); 
?>

<!DOCTYPE html>
<?php
    if(isset($_SESSION['stuID'])) {
        header("Location: index.php");
    } else {
        echo "<script>signOut()</script>";
    }
?>

<html lang="en">
<head>
    <title>MDID ConNect</title>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="MDB-Free_4/css/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="script.js"></script>

    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <meta name="google-signin-client_id" content="358731458931-kkonojvlpmpmlg6pmhd769lffaa484dv.apps.googleusercontent.com">
</head>
<body>
    <script type="text/javascript" src="MDB-Free_4/js/mdb.min.js"></script>
    <div class="jumbotron disappear-when-too-small title">
        <h1 class="smaller-when-necessary">MDID ConNect System</h1>
    </div>

    <nav class="navbar navbar-inverse sticky-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span> 
                </button>
                <a class="navbar-brand" href="index.php">ConNect</a>
            </div>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="rooms.php">Available Classrooms</a></li>
                    <li><a href="reserve.php">Reserve</a></li>
                    <li><a href="history.php">My Reservations</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href='login.php'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container-fluid main-cont bg-2" >
        <div class="col-sm-2"></div> <!--BLANK-->
        <div class="jumbotron col-sm-8" >
            <h1>Login</h1>
            <h4>Please sign in with your Mingdao account.</h4>
            <h4><br /></h4>
            <center>
                <div id="my-signin2"></div>
            </center>
        </div>
        <div class="col-sm-2"></div><!--BLANK-->
    </div>

    <?php
        if (isset($_POST['name']) && isset($_POST['email'])) {
            $name = strip_tags($_POST['name']);
            $email = strip_tags($_POST['email']);
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['stuID'] = strtok($email, '@');
            header("Location:")
            /*try {
                include 'db.php';
                $stmt = $db->prepare("SELECT * FROM `users` WHERE `ID` = ? AND `pwd` = ?");
                $stmt->execute(array($stuID, $pwd));
                $row_count = $stmt->rowCount();
                if($row_count == 1) {
                    foreach($stmt as $row) {
                        $_SESSION['stuID'] = $row['ID'];
                        $_SESSION['class'] = $row['class'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['status'] = ($row['class'] == "Admin") ? 1 : 0;
                    }

                    if($_SESSION['status'] == 1) header("location: approve.php");
                    else {
                        if ($_GET['redirect'] == 'history') header("Location: history.php");
                        else header("location: rooms.php");
                    }
                } else {
                    echo "<script type='text/javascript'>$('#info-msg').show();</script>";
                }
            } catch (PDOException $ex) {
                echo "Error!";
            }*/
        }
    ?>

    <footer class="container-fluid bg-4 text-center" style="padding-top:30px; padding-bottom:30px">
        <p>Created by Jay Chou.</p>
        <p>Mingdao International Department, Taichung, Taiwan.</p>
    </footer>
    <script type="text/javascript">
        function onSuccess(googleUser) {
            var profile = googleUser.getBasicProfile();
            if (!profile.getEmail().endsWith("@ms.mingdao.edu.tw")) {
                alert("Please sign in with a Mingdao Account!");
                signOut();
                return;
            }

            $.ajax({
                url: "login.php",
                type: "post",
                data: {
                    'name': profile.getName(),
                    'email': profile.getEmail()
                }
            }).done(function (){
                //window.location.href="index.php";
            }).fail(function (){
                alert("Oops... We've encountered an error!");
                window.location.href="index.php";
            });
        }
        function onFailure(error) {
          console.log(error);
        }
        function renderButton() {
          gapi.signin2.render('my-signin2', {
            'scope': 'profile email',
            'width': 240,
            'height': 50,
            'longtitle': true,
            'theme': 'light',
            'onsuccess': onSuccess,
            'onfailure': onFailure
          });
        }

    function signOut() {
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut().then(function () {
            console.log('User signed out.');
        });
    }
    </script>
   <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
</body>
</html>