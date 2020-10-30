<!--
Starting Date: 16 Dec 2018
Name: index.php (Home)
Features:
  1) Jumbotron disappears when screen too small (i.e. when navbar is collapsed)
  2) Cancel navbar freezing (sticking to the top of the screen) when collapsed
  3) Decreases size of "Mingdao International Department" when text-wrapped

-->
<?php
    session_start();
    include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>MDID ConNect</title>
	<meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="MDB-Free_4/css/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="script.js"></script>

    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <meta name="google-signin-client_id" content="358731458931-kkonojvlpmpmlg6pmhd769lffaa484dv.apps.googleusercontent.com">
</head>
<script>
    function signOut() {
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut().then(function () {
            console.log('User signed out.');
        });
    }
</script>

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
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="rooms.php">Available Classrooms</a></li>
                    <li><a href="reserve.php">Reserve</a></li>

                    <?php
                        if (isset($_SESSION['status']) && $_SESSION['status'] == 1) {
                            try {
                                $stmt = $db->prepare("SELECT * FROM `reservations` WHERE `status` = 0");
                                $stmt->execute();
                                $row_count = $stmt->rowCount();
                            } catch (PDOException $ex) {
                                echo "Error!";
                            }

                            echo '<li><a href="approve.php">View Requests ';
                            if ($row_count > 0) {
                                echo '<span class="label label-danger">'.$row_count.'</span>';
                            }
                            echo '</a></li>
                            <li> <a href="editInfo.php">Edit Classroom Info</a></li>
                            <li> <a href="editAccount.php">Edit Student Accounts</a></li>';
                        } else {
                            echo '<li><a href="history.php">My Reservations</a></li>';
                        }
                    ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php
                        if (isset($_SESSION['stuID'])) {
                            echo "<li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Sign out (".$_SESSION['name'].")</a></li>";
                        } else {
                            /*echo "<li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='#'><span class='glyphicon glyphicon-log-in'></span> Login</a>
                                <ul class='dropdown-menu'>
                                    <li>
                                        <div id='signin_btn' class='g-signin2' data-onsuccess='onSignIn'></div>
                                        <a href='#' onclick='signOut();' id='signout_btn' style='display:none;'>Sign out</a>
                                    </li>
                            </li>";*/
                            echo "<li><a href='login.php'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-1 white-text text-center main-cont" >
        <div class="col-sm-1"></div>
    	<div class="col-sm-10">
            <h1><b>Top 3 most reserved classrooms</b></h1><br>

            <div class="card-deck">
                <?php
                    try {
                        $stmt = $db->prepare("SELECT * FROM `classrooms` ORDER BY `timesReserved` DESC LIMIT 3");
                        $stmt->execute();

                        foreach($stmt as $row) {
                            echo '<div class="card" style="width:300px">
                                    <div data-toggle="modal" data-target="#room_'.$row['id'].'" class="fader" style="
                                        background-image: url(\'img/img_'.$row['id'].'.jpg?'.Date('U').'\'");"></div>
                                    <div class="card-body">
                                        <h4 class="card-title">'.$row['name'].'</h4>
                                        <p class="card-text">Location: '.$row['description'].' <br>
                                            Reserved '.$row['timesReserved'].' Times
                                        </p>
                                        <a data-toggle="modal" data-target="#room_'.$row['id'].'" class="btn btn-primary">Check Availability</a>
                                        <a href="reserve.php?room='.$row['name'].'" class="btn btn-success">Reserve Now</a>
                                    </div>
                                </div>';
                        }
                    } catch (PDOException $ex) {
                        echo "Error!";
                    }
                ?>
            </div>
	    </div>
    </div>

    <div class="container-fluid bg-2 white-text text-center main-cont">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
            <h1>About MDID ConNect</h1><br>
            <h4 align="left" style="line-height: 30px">
                This is an online system created and hosted by Jay from 1001 for his MYP Personal Project. The primary purpose of the website is to bridge the distance between students and the school, creating a more convenient process for classroom reservation, and shortening the time needed for both students and teachers to reserve classrooms. Here are some of the services it offers: <br>
                <u>For students and teachers:</u> 
                <ol>
                    <li>Check availability of each classroom</li>
                    <li>Reserve classrooms</li>
                    <li>Check admin's reply on whether reservation is approved (sent via email simultaneously)</li>
                </ol>
                <u>For admin:</u>
                <ol>
                    <li>View reservation requests (sent via email simultaneously)</li>
                    <li>Approve/Disapprove reservations</li>
                </ol>
            </h4>
        </div>
    </div>

    <div class="container-fluid bg-3 text-center main-cont">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
            <h1>Comments and Feedback</h1><br>
            <h4 align="left" style="line-height: 30px">
                Please leave your comments or suggestions down below to help improve the website. Any feedback is appreciated!
            </h4>
            <div class="md-form green-textarea active-green-textarea">
                <textarea type="text" id="comments" class="md-textarea form-control" rows="3"></textarea>
                <label for="comments">Comments & Suggestions:</label>
                <button class="btn btn-default submit-btn" style="font-size:13px" onclick="sendSuggestion()">Submit</button>

                <div id="submitMessage" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Message</h4>
                            </div>
                            <div class="modal-body">
                                Your suggestion has been collected. Thank you for your feedback!
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="loadingMessage" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Message</h4>
                            </div>
                            <div class="modal-body">
                                Your suggestion is being sent. Please remain on this page. You will be notified when the suggestion has been sent!
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Generating a modal box for each room -->
    <?php
        try {
            $stmt = $db->prepare("SELECT `name`,`id` FROM `classrooms`");
            $stmt->execute();
        } catch (PDOException $ex) {
            echo "Error!";
        }
        foreach ($stmt as $row) {
            echo '<div id="room_'.$row['id'].'" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Classroom Availability</h4>
                            </div>
                            <div class="modal-body">
                                <h1>'.$row['name'].'</h1>
                                <h2>Weekly Schedule</h2>
                                <image src="img/sch_'.$row['id'].'.jpg?'.Date('U').'" width="100%" />

                                <h2>Reserved Periods</h2>
                                <table class="table">
                                    <thead>
                                        <th>Date</th>
                                        <th>Periods</th>
                                    </thead>
                                    <tbody>';
                                        $stmt2 = $db->prepare("SELECT * FROM `reservations` WHERE `room` = ? AND `date` >= ? ORDER BY `date`");
                                        $stmt2->execute(Array($row['name'], date("Y-m-d")));
                                        foreach($stmt2 as $row2) {
                                            if($row2['status'] >= 0) {
                                                if($row2['status'] == 0) echo '<tr class="bg-warning">';
                                                else echo '<tr class="bg-success white-text">';
                                                echo '<td>'.$row2['date'].'</td>';
                                                echo '<td>'.$row2['periods'].'</td>';
                                                echo '</tr>';
                                            }
                                        }
                                        echo '
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <a href="reserve.php?room='.$row['name'].'" class="btn btn-success">Reserve Now</a>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
        }
    ?>

    <footer class="container-fluid bg-4 text-center" style="padding-top:30px; padding-bottom:30px">
        <p>Created by Jay Chou.</p>
        <p>Mingdao International Department, Taichung, Taiwan.</p>
    </footer>

    <script>
    function sendSuggestion() {
        if ($('#comments').val() != "") {
            $('#loadingMessage').modal('show');
            $.ajax({
                url: "servers.php",
                type: "post",
                data: {
                    'request': 'index',
                    'message': $('#comments').val()
                },
                success: function(msg) {
                    alert(msg);
                    $('#loadingMessage').modal('hide');
                    $('#submitMessage').modal('show');
                }
            }).fail(function() {
                alert("Oops... We've encountered an error!");
                window.location.href="index.php";
            });
            $('#comments').val("");
        }
    }
    </script>
</body>
</html>