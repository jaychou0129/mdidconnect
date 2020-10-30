<!--
Starting Date: 16 Dec 2018
Name: reserve.php (Reserve)
Features:
  1) Jumbotron disappears when screen too small (i.e. when navbar is collapsed)
  2) Cancel navbar freezing (sticking to the top of the screen) when collapsed
  3) Decreases size of "Mingdao International Department" when text-wrapped

-->
<?php
    session_start();
    include 'db.php';
    if (!isset($_SESSION['stuID'])) {
        echo "<script>alert('You have to sign in first!'); window.location='login.php';</script>";
    }
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
    <link rel="stylesheet" href="bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script type="text/javascript" src="bower_components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
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
                    <li><a href="rooms.php">Available Classrooms</a></li>
                    <li class="active"><a href="reserve.php">Reserve</a></li>

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

    <div class="container-fluid main-cont bg-2" >
        <div class="col-sm-2"></div>
        <div class="jumbotron col-sm-8" >
            <h1>Reserve a room</h1>
            <h3>Here are some important information to fill out:</h3>
            <h5><span class="glyphicon glyphicon-asterisk" style="color:red;font-size:8px"></span> => required!</h5><br>

            <form method="POST" action="servers.php" id="reserve-form">
                <input name="request" type="hidden" value="reserve" />
                <div class="form-group">
                    <span class="glyphicon glyphicon-asterisk" style="color:red;font-size:8px"></span>
                    <label for="stuName">Sent by:</label>
                    <input type="text" class="form-control" name="fullName" value="<?php echo $_SESSION['class']." ".$_SESSION['name']." (".$_SESSION['stuID'].")"; ?>" readonly>
                    <input type="hidden" name="stuName" value="<?=$_SESSION['stuID'] ?>" />
                </div>
                <div class="form-group">
                    <span class="glyphicon glyphicon-asterisk" style="color:red;font-size:8px"></span>
                    <label for="room">Room:</label>
                    <select class="form-control" id="room" name="room">
                        <option value=""></option>
                        <?php
                            try {
                                $stmt = $db->prepare("SELECT * FROM `classrooms`");
                                $stmt->execute();
                            } catch (PDOException $ex) {
                                echo "Error!";
                            }
                            foreach($stmt as $row) {
                                echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <span class="glyphicon glyphicon-asterisk" style="color:red;font-size:8px"></span>
                    <label for="date">Date:</label>
                    <div class="input-group date" id="datepicker">
                        <input type='text' class="form-control" id="date" name="date">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group form-inline">
                    <span class="glyphicon glyphicon-asterisk" style="color:red;font-size:8px"></span>
                    <label>Periods:</label><br>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="Morning HR Hour"> Morning HR Hour</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="1"> Block 1</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="2"> Block 2</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="3"> Block 3</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="4"> Block 4</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="Lunch Break"> Lunch Break</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="5"> Block 5</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="6"> Block 6</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="7"> Block 7</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="8"> Block 8</label>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" class="custom-control-input" name="period" value="9"> Block 9</label>
                    </div>
                    <input type="hidden" name="periods" id="periods" />
                </div>
                <div class="form-group">
                    <span class="glyphicon glyphicon-asterisk" style="color:red;font-size:8px"></span>
                    <label for="purpose">Purpose:</label>
                    <textarea class="form-control" id="purpose" name="purpose" style="resize:vertical;"></textarea>
                </div>
                <div class="form-group">
                    <label for="personnel">Who else is involved:</label>
                    <textarea class="form-control" id="personnel" name="personnel" style="resize:vertical;"></textarea>
                </div>
                <h5 id="info-msg">Please fill in all required fields!</h5>
                <button type="submit" class="btn btn-primary bg-1">Submit request</button> 
            </form>
        </div>
    </div>

    <footer class="container-fluid bg-4 text-center" style="padding-top:30px; padding-bottom:30px">
        <p>Created by Jay Chou.</p>
        <p>Mingdao International Department, Taichung, Taiwan.</p>
    </footer>


    <script type="text/javascript">
        if (getUrlParameter('room') != undefined) {
            $('#room').val(getUrlParameter('room'));
        }
    </script>
</body>
</html>