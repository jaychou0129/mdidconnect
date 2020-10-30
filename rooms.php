<!--
Starting Date: 16 Dec 2018
Name: rooms.php (Available Rooms)
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
                    <li class="active"><a href="rooms.php">Available Classrooms</a></li>
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
                            echo "<li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Sign out (".$_SESSION['stuID'].")</a></li>";
                        } else {
                            echo "<li><a href='login.php'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-1 white-text text-center main-cont" >
        <h1><b>Available Classrooms</b></h1>
        <h4>Find your matching classroom with the filters and the search bar.</h4><br>
        <div class="col-sm-3 jumbotron" style="color:black; padding-left:10px; padding-right:10px; text-align:left">
            <h3 style="text-align:center">FILTERS</h3>
            <input class="form-control" id="roomsSearchBar" type="text" placeholder="Search.." style="margin-bottom:20px">
            <div class="form-group">
                <label for="floor">Location:</label>
                <select class="form-control" id="roomsFloor" name="floor">
                    <option value="all">All</option>
                    <option value="7">7F</option>
                    <option value="8">8F</option>
                    <option value="9">9F</option>
                    <option value="10">10F</option>
                </select>
            </div>
        </div>

        <div class="col-sm-9">
            <div class="row" id="rooms-wrapper">
                <?php
                    try {
                        $stmt = $db->prepare("SELECT * FROM `classrooms`");
                        $stmt->execute();
                        foreach($stmt as $row) {
                            echo '<div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="card" style="width:500px">
                                        <div class="fader" data-toggle="modal" data-target="#room_'.$row['id'].'" style="
                                        background-image: url(\'img/img_'.$row['id'].'.jpg?'.Date('U').'\'");"></div>
                                        <div class="card-body">
                                            <h4 class="card-title">'.$row['name'].'</h4>
                                            <p class="card-text">Location: '.$row['description'].' <br>
                                                Reserved '.$row['timesReserved'].' Times
                                            </p>
                                            <a data-toggle="modal" data-target="#room_'.$row['id'].'" class="btn btn-primary">Check Availability</a>
                                            <a href="reserve.php?room='.$row['name'].'" class="btn btn-success">Reserve Now</a>
                                        </div>
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
</body>
</html>