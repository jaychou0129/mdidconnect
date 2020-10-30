<!--
Starting Date: 19 Jan 2018
Name: editInfo.php (Edit Classroom Info)
Features:
  1) Jumbotron disappears when screen too small (i.e. when navbar is collapsed)
  2) Cancel navbar freezing (sticking to the top of the screen) when collapsed
  3) Decreases size of "Mingdao International Department" when text-wrapped

-->
<?php
  	session_start();
  	if (!isset($_SESSION['stuID'])) {
    	echo "<script>alert('You have to sign in first!'); window.location='login.php';</script>";
  	} else if (isset($_SESSION['status']) && $_SESSION['status'] == 0) {
    	echo "<script>alert('You do not have access to this page!'); window.location='home.php';</script>";
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
  	<script src="script.js"></script>
</head>

<body>
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
                    <li><a href="approve.php">View Requests 
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
                    <li class="active"><a href="editInfo.php">Edit Classroom Info</a></li>
                    <li><a href="editAccount.php">Edit Student Accounts</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Sign out (admin)</a></li>
                </ul>
            </div>
        </div>
    </nav>

	<div class="container-fluid bg-2 white-text text-center main-cont" >
		<h1><b>Edit Classroom Info</b></h1><br>

		<div class="row">
			<div class="col-sm-1">&nbsp;</div>
	  		<div class="col-sm-10 jumbotron" style="padding:0px">
	  			<button class="btn btn-default" style="width:100%; height:60px; font-size:30px; margin:0px" onclick="showNew()"> &plus; </button>
	  		</div>
  			<div class="col-sm-1">&nbsp;</div>
  		</div>

		<div class="row" id="new_row" style="display:none">
			<div class="col-sm-1">&nbsp;</div>
			<div class="col-sm-10 jumbotron black-text">
				<div class="col-md-5 col-sm-12">
					<input class="form-control" id="room_name_new" type="text" placeholder="Name of the new classroom" style="width:100%; margin: 0px 10px" oninput="checkComplete()">
					<h3>Display image</h3>
					<label for="disFile_new" class="btn btn-default"><span class="glyphicon glyphicon-camera"></span> Select Display Image</label>
					<form name="disForm_new">
						<input type="file" style="opacity:0" id="disFile_new" name="disFile" onchange="readURL(this, 'dis', 'new');checkComplete();" accept="image/*" />
					</form>
					<image src="img/img_new.jpg" height="100px" id="dis_new"/>
				</div>
				<div class="col-md-5 col-sm-12">
		  			<select class="form-control" id="room_floor_new" onchange="checkComplete()">
					    <option value="7th Floor">7F</option>
					    <option value="8th Floor">8F</option>
					    <option value="9th Floor">9F</option>
					    <option value="10th Floor">10F</option>
		  			</select>
		  			<h3>Schedule image</h3>
		  			<label for="schFile_new" class="btn btn-default"><span class="glyphicon glyphicon-camera"></span> Select Schedule Image</label>
		  			<form name="schForm_new">
		  				<input type="file" style="opacity:0" id="schFile_new" name="schFile" onchange="readURL(this, 'sch', 'new');checkComplete();" accept="image/*" />
		  			</form>
		  			<image src="img/sch_new.jpg" height="100px" id="sch_new"/>
				</div>
				<div class="col-md-2 col-sm-12">
				    <button class="btn btn-default" style="width:100%; height:40%; font-size: 1.5em" onclick="addRoom()">&plus;</button><br>
				    <button class="btn btn-danger" style="width:100%; height:40%; font-size: 1.5em" onclick="hideNew()"><span class="glyphicon glyphicon-trash"></span></button>
		    		<h5 style="color: red;" id="new_status"></h5>
				</div>
			</div>
			<div class="col-sm-1">&nbsp;</div>
		</div>

  		<?php
    		try {
      			$stmt = $db->prepare("SELECT * FROM `classrooms`");
      			$stmt->execute();
      			foreach($stmt as $row) {
      				echo '
		  	<div class="row">
			  	<div class="col-sm-1">&nbsp;</div>
			  	<div class="col-sm-10 jumbotron black-text">
				  	<div class="col-md-5 col-sm-12">
			  			<input class="form-control" id="room_name_'.$row['id'].'" type="text" placeholder="Enter the name of the classroom" value="'.$row['name'].'" style="width:100%; margin: 0px 10px" oninput="unsaved('.$row['id'].')">
				  		<h3>Display image</h3>
				  		<label for="disFile_'.$row['id'].'" class="btn btn-default"><span class="glyphicon glyphicon-camera"></span> Select Display Image</label>
				  		<form name="disForm_'.$row['id'].'">
				  			<input type="file" style="opacity:0" id="disFile_'.$row['id'].'" name="disFile" onchange="readURL(this, \'dis\', '.$row['id'].');" accept="image/*" />
			  			</form>
			  			<image src="img/img_'.$row['id'].'.jpg?'.Date('U').'" height="100px" id="dis_'.$row['id'].'"/>
					</div>
					<div class="col-md-5 col-sm-12">
			      		<select class="form-control" id="room_floor_'.$row['id'].'" onchange="unsaved('.$row['id'].')">
					        <option value="7th Floor">7F</option>
					        <option value="8th Floor">8F</option>
					        <option value="9th Floor">9F</option>
					        <option value="10th Floor">10F</option>
			      		</select>
			      		<script>$("#room_floor_'.$row['id'].'").val("'.$row['description'].'");</script>
			      		<h3>Schedule image</h3>
				  		<label for="schFile_'.$row['id'].'" class="btn btn-default"><span class="glyphicon glyphicon-camera"></span> Select Schedule Image</label>
			      		<form name="schForm_'.$row['id'].'">
			      			<input type="file" style="opacity:0" id="schFile_'.$row['id'].'" name="schFile" onchange="readURL(this, \'sch\', '.$row['id'].');" accept="image/*" />
			      		</form>
			      		<image src="img/sch_'.$row['id'].'.jpg?'.Date('U').'" height="100px" id="sch_'.$row['id'].'"/>
			    	</div>
			    	<div class="col-md-2 col-sm-12">
					    <button class="btn btn-default" style="width:100%; height:40%; font-size: 1.5em" onclick="saveRoom('.$row['id'].')"><span class="glyphicon glyphicon-floppy-disk"></span></button><br>
					    <button class="btn btn-danger" style="width:100%; height:40%; font-size: 1.5em" onclick="deleteRoom('.$row['id'].')"><span class="glyphicon glyphicon-trash"></span></button>
					    <h5 style="color: red; display:none;" id="unsaved_'.$row['id'].'">Unsaved Changes!</h5>
					</div>
			  	</div>
				<div class="col-sm-1">&nbsp;</div>
		  	</div>';
	      		}
		    } catch (PDOException $ex) {
		      	echo "Error!";
		    }
	  	?>
	</div>

	<footer class="container-fluid bg-4 text-center" style="padding-top:30px; padding-bottom:30px">
	  	<p>Created by Jay Chou.</p>
	  	<p>Mingdao International Department, Taichung, Taiwan.</p>
	</footer>

	<script>
	function readURL(input, mode, id) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $('#'+mode+'_'+id)
	                .attr('src', e.target.result);
	        };

	        reader.readAsDataURL(input.files[0]);
	       	unsaved(id);
	    }
	}

	function unsaved(id) {
		$('#unsaved_'+id).show();
	}

	function saveRoom(id) {
		if ($('#unsaved_'+id).is(":visible")) {
			var form = document.forms.namedItem('disForm_'+id);
			var form2 = document.forms.namedItem('schForm_'+id);

			var oData = new FormData(form);
			var oData2 = new FormData(form2);

			oData.append("request", "updateInfo");
			oData.append("id", id);
			oData.append("schFile", oData2.get("schFile"));
			oData.append("room_name", $('#room_name_'+id).val());
			oData.append("room_floor", $('#room_floor_'+id).val());

			var request = $.ajax({
			    url: 'servers.php',
        		dataType: 'text',
			    data: oData,
			    type: 'POST',
			    contentType: false,
			    processData: false,
			}).done(function(msg){
				alert(msg+"Saved Successfully!");
				$('#unsaved_'+id).hide();
			});
		}
	}

	function deleteRoom(id) {
	  if(confirm('Are you sure you want to delete this classroom?')) {
	    $.ajax({
	        url: "servers.php",
	        type: "post",
	        data: {
	        	'request': 'deleteRoom',
	        	'id': id
	        }
	    }).done(function (){
	      	alert("Classroom deleted successfully!");
	      	window.location.href="editInfo.php";
	    }).fail(function (){
	      	alert("Oops... We've encountered an error!");
	      	window.location.href="editInfo.php";
	    });

	  }
	}

	function showNew() {
		$('#new_row').show();
		$('#room_name_new').focus();
	}

	function hideNew() {
		$('#room_name_new').val('');
		$('#disFile_new').val('');
		$('#schFile_new').val('');
		$('#dis_new').attr('src','img/img_new.jpg');
		$('#sch_new').attr('src','img/sch_new.jpg');
		$('#new_status').html("");
		$('#new_row').hide();
	}

	function checkComplete() {
		var str = "";
		if ($('#room_name_new').val() == '') str += "Enter Name of Room! <br>";
		if ($('#disFile_new').val() == '' || $('#schFile_new').val() == '') str += "Select Images!";

		if (str == "") str = "Click Add to save room!";
		$('#new_status').html(str);
	}

	function addRoom() {
		if ($('#new_status').html() == "Click Add to save room!") {
			var form = document.forms.namedItem('disForm_new');
			var form2 = document.forms.namedItem('schForm_new');

			var oData = new FormData(form);
			var oData2 = new FormData(form2);

			oData.append("request", "addRoom");
			oData.append("schFile", oData2.get("schFile"));
			oData.append("room_name", $('#room_name_new').val());
			oData.append("room_floor", $('#room_floor_new').val());
		    $.ajax({
		        url: "servers.php",
		        dataType: 'text',
			    data: oData,
			    type: 'POST',
			    contentType: false,
			    processData: false
		    }).done(function (){
		      	alert("Classroom added successfully!");
		      	window.location.href="editInfo.php";
		    }).fail(function (){
		      	alert("Oops... We've encountered an error!");
		      	window.location.href="editInfo.php";
		    });
		}
	}
	</script>
</body>
</html>