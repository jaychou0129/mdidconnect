<!--
Starting Date: 19 Jan 2019
Name: approve.php (View Requests)

-->
<?php
    session_start();
    if (!isset($_SESSION['stuID'])) {
        echo "<script>alert('You have to sign in first!'); window.location='login.php';</script>";
    } else if (isset($_SESSION['status']) && $_SESSION['status'] == 0) {
        echo "<script>alert('You do not have access to this page!'); window.location='index.php';</script>";
    }
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
                    <li class="active"><a href="approve.php">View Requests 
                        <?php
                            try {
                                $stmt = $db->prepare("SELECT * FROM `reservations` WHERE `status` = 0");
                                $stmt->execute();
                                $row_count = $stmt->rowCount();
                            } catch (PDOException $ex) {

                            }
                            if ($row_count > 0) {
                                echo '<span class="label label-danger">'.$row_count.'</span>';
                            }
                        ?></a></li>
                    <li> <a href="editInfo.php">Edit Classroom Info</a></li>
                    <li> <a href="editAccount.php">Edit Student Accounts</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Sign out (admin)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid bg-2 white-text text-center main-cont" >
	   <h1><b>View Requests</b></h1>

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
                            <th>Reserved By</th>
                            <th>Date</th>
                            <th>Periods</th>
                            <th>Room</th>
                            <th width="265px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            try {
                                $stmt = $db->prepare("SELECT * FROM `reservations` ORDER BY `r_id` DESC");
                                $stmt->execute();
                            } catch (PDOException $ex) {
                                echo "Error!";
                            }
                            foreach($stmt as $row) {
                                $stmt2 = $db->prepare("SELECT * FROM `users` WHERE `ID` = ?");
                                $stmt2->execute(Array($row['sentBy']));
                                foreach($stmt2 as $usr) { $reservedBy = $usr['class'].' '.$usr['name']; }
                                $dayOfWeek = date('l', strtotime($row['date']));

                                switch($row['status']) {
                                case '0':
                                    echo '
                                        <tr class="warning" id="row_'.$row['r_id'].'">
                                            <th data-toggle="modal" data-target="#'.$row['r_id'].'" style="cursor: pointer">'.$reservedBy.'</th>
                                            <th name="date" data-toggle="modal" data-target="#'.$row['r_id'].'" style="cursor: pointer">'.$row['date'].' ('.$dayOfWeek.')</th>
                                            <th data-toggle="modal" data-target="#'.$row['r_id'].'" style="cursor: pointer">'.$row['periods'].'</th>
                                            <th data-toggle="modal" data-target="#room_'.$row['room'].'" style="cursor: pointer">'.$row['room'].'</th>
                                            <th id="status_'.$row['r_id'].'">
                                            	<button class="btn btn-success" onclick="action('.$row['r_id'].', 1)">Approve</button>
                                            	<button class="btn btn-danger" onclick="action('.$row['r_id'].', -1)">Disapprove</button>
                                            </th>

                                            <div id="'.$row['r_id'].'" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">View Request</h4>
                                                        </div>
                                                        <div class="modal-body text-left">
                                                            <p><b>Reserved By:</b>
                                                            '.$reservedBy.'</p>
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
                                                        <div class="modal-footer">
                                                            <button class="btn btn-success" onclick="action('.$row['r_id'].', 1)">Approve</button>
                                                            <button class="btn btn-danger" onclick="action('.$row['r_id'].', -1)">Disapprove</button>
                                                            <button class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>';
                                    break;
                                case '-1':
                                case '1':
                                    if ($row['status'] == '-1') echo '<tr class="danger" id="row_'.$row['r_id'].'">';
                                    else echo '<tr class="success" id="row_'.$row['r_id'].'">';
                                    echo '
                                        <th data-toggle="modal" data-target="#'.$row['r_id'].'" style="cursor: pointer">'.$reservedBy.'</th>
                                        <th name="date" data-toggle="modal" data-target="#'.$row['r_id'].'" style="cursor: pointer">'.$row['date'].' ('.$dayOfWeek.')</th>
                                        <th data-toggle="modal" data-target="#'.$row['r_id'].'" style="cursor: pointer">'.$row['periods'].'</th>
                                        <th data-toggle="modal" data-target="#room_'.$row['room'].'" style="cursor: pointer">'.$row['room'].'</th>';
				                        if ($row['status'] == '-1') echo '<th>Disapproved</th>';
				                        else echo '<th>Approved</th>';

                                        echo '
                                        <div id="'.$row['r_id'].'" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">View Request</h4>
                                                    </div>
                                                    <div class="modal-body text-left">
                                                        <p><b>Reserved By:</b>
                                                        '.$reservedBy.'</p>
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
                                                        <p><b>Message:</b></p>
                                                        '.nl2br($row['message']).'</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" onclick="deleteReq('.$row['r_id'].')">Delete Request</button>
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

	<?php
        try {
    		$stmt = $db->prepare("SELECT `name`,`id` FROM `classrooms`");
    		$stmt->execute();
    	} catch (PDOException $ex) {
    	    echo "Error!";
    	}
        foreach ($stmt as $row) {
            echo '
                <div id="room_'.$row['name'].'" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Classroom Availability</h4>
                            </div>
                            <div class="modal-body">
                                <h1>'.$row['name'].'</h1>
                                <h2>Weekly Schedule</h2>
                                <image src="img/sch_'.$row['id'].'.jpg" width="100%" />';
            $stmt2 = $db->prepare("SELECT * FROM `reservations` WHERE `room` = ? AND `date` >= ? ORDER BY `date`");
            $stmt2->execute(Array($row['name'], date("Y-m-d")));
            echo '
                                <h2>Reserved Periods</h2>
                                <table class="table">
                                    <thead>
                                        <th>Date</th>
                                        <th>Periods</th>
				                    </thead>
				                    <tbody>';
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

function action(id, mode) {
	var msg = "";
	if (mode == -1) {
        msg = prompt('Why is this request disapproved? Leave a message to the user.');
	}
	if (msg != null) {
        if(mode == 1) $('#row_'+id).attr('class', 'success');
        if(mode == -1) $('#row_'+id).attr('class', 'danger');
        $('#status_'+id).html('LOADING...');
		$.ajax({
	        url: "servers.php",
	        type: "post",
	        data: {
                'request': 'approve',
                'id': id,
	    		'mode': mode,
	    		'message': msg
            }
	    }).done(function (){
            if(mode == 1) $('#status_'+id).html('Approved');
            if(mode == -1) $('#status_'+id).html('Disapproved');
	    }).fail(function (){
            alert("Oops... We've encountered an error!");
            window.location.href="approve.php";
	    });      
	}
}

</script>
</body>
</html>