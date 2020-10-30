<!--
Starting Date: 16 Dec 2018
Name: history.php (My Reservations)
Features:
  1) Jumbotron disappears when screen too small (i.e. when navbar is collapsed)
  2) Cancel navbar freezing (sticking to the top of the screen) when collapsed
  3) Decreases size of "Mingdao International Department" when text-wrapped

-->
<?php
    session_start();
    if (!isset($_SESSION['stuID'])) {
        echo "<script>alert('You have to sign in first!'); window.location='login.php?redirect=history';</script>";
    } else if (isset($_SESSION['status']) && $_SESSION['status'] == 1) {
        header("Location: approve.php");
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="tablesorter-master/dist/js/jquery.tablesorter.min.js"></script>
    <script src="tablesorter-master/dist/js/jquery.tablesorter.widgets.min.js"></script>
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
                    <li><a href="reserve.php">Reserve</a></li>
                    <li class="active"><a href="history.php">My Reservations</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Sign out (<?php echo $_SESSION['stuID']; ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-2 white-text text-center main-cont" >
	    <h1><b>My Reservations</b></h1>

        <!--Top Table UI-->
        <div class="jumbotron" style="padding: 30px 30px 20px 30px">
            <div class="row">
                <div class="col-sm-4 col-xs-6">
                    <select class="form-control grey-tooltip" id="historyShow" name="show" data-toggle="tooltip" data-placement="top" title="Past/future reservations" style="margin-bottom: 10px">
                        <option value="all">Show all reservations</option>
                        <option value="future">Show future reservations only</option>
                        <option value="past">Show past reservations only</option>
                    </select>
                </div>

                <div class="col-sm-4 col-xs-6">
                    <select class="form-control grey-tooltip" id="historyStatus" name="status" data-toggle="tooltip" data-placement="top" title="Status" style="margin-bottom: 10px">
                        <option value="all">Show all status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="disapproved">Disapproved</option>
                    </select>
                </div>

                <div class="col-sm-3 col-xs-10">
                    <input class="form-control" id="historySearchBar" type="text" placeholder="Search.." style="margin-bottom: 10px">
                </div>

                <div class="col-sm-1 col-xs-2">
                    <button class="btn btn-default grey-tooltip" id="historyClearSearch" data-toggle="tooltip" data-placement="top" title="Clear filters">&times;</button>
                </div>
            </div>
        </div>

        <div class="jumbotron black-text" style="padding:20px;">
            <div class="table-responsive">
                <table class="table table-hover table-bordered tablesorter" id="displayTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Periods</th>
                            <th>Room</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            try {
                                include 'db.php';
                                $stmt = $db->prepare("SELECT * FROM `reservations` WHERE `sentBy` = ? ORDER BY `r_id` DESC");
                                $stmt->execute(Array($_SESSION['stuID']));
                            } catch (PDOException $ex) {
                                echo "Error!";
                            }

                            foreach($stmt as $row) {
                                switch($row['status']) {
                                case '-1':
                                    echo '
                                        <tr class="danger" data-toggle="modal" data-target="#modal'.$row['r_id'].'" style="cursor: pointer">'.
                                            '<th name="date">'.$row['date'].'</th>
                                            <th>'.$row['periods'].'</th>
                                            <th>'.$row['room'].'</th>
                                            <th>'.$row['purpose'].'</th>
                                            <th><div class="grey-tooltip" data-toggle="tooltip" data-placement="bottom" title="Click to see why it was disapproved.">Disapproved</div></th>

                                            <div id="modal'.$row['r_id'].'" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Message</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>'.$row['message'].'</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>';
                                    break;
                                case '0':
                                case '1':
                                    if ($row['status'] == '0') echo '<tr class="warning" data-toggle="modal" data-target="#modal'.$row['r_id'].'" style="cursor: pointer">';
                                    else echo '<tr class="success" data-toggle="modal" data-target="#modal'.$row['r_id'].'" style="cursor: pointer">';
                                    echo '<th name="date">'.$row['date'].'</th>
                                            <th>'.$row['periods'].'</th>
                                            <th>'.$row['room'].'</th>
                                            <th>'.$row['purpose'].'</th>';
                                    if ($row['status'] == '0') echo '<th>Pending</th>';
                                    else echo '<th>Approved</th>';

                                    echo '
                                        <div id="modal'.$row['r_id'].'" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">View Request</h4>
                                                    </div>
                                                    <div class="modal-body text-left">
                                                        <p><b>Sent By:</b>
                                                        '.$row['sentBy'].'</p>
                                                        <p><b>Room:</b>
                                                        '.$row['room'].'</p>
                                                        <p><b>Date:</b>
                                                        '.$row['date'].'</p>
                                                        <p><b>Periods:</b>
                                                        '.$row['periods'].'</p>
                                                        <p><b>Purpose:</b>
                                                        '.nl2br($row['purpose']).'</p>
                                                        <p><b>People involved:</b>
                                                        '.nl2br($row['personnel']).'</p>
                                                    </div>
                                                    <div class="modal-footer">';
                                                        if ($row['status'] == 0) echo '<button type="button" class="btn btn-danger" onclick="deleteReq('.$row['r_id'].')">Cancel Request</button>';
                                                        echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>';
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="container-fluid bg-4 text-center" style="padding-top:30px; padding-bottom:30px">
        <p>Created by Jay Chou.</p>
        <p>Mingdao International Department, Taichung, Taiwan.</p>
    </footer>

    <script>
    function deleteReq(id) {
        if(confirm('Are you sure you want to delete this reservation request?')) {
            $.ajax({
                url: "servers.php",
                type: "post",
                data: {
                    'request': 'deleteRequest',
                    'id': id
                }
            }).done(function (){
                alert("Request deleted successfully!");
                window.location.href="history.php";
            }).fail(function (){
                alert("Oops... We've encountered an error!");
                window.location.href="history.php";
            });

        }
    }
    </script>
</body>
</html>