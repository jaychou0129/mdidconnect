<!--
Starting Date: 28 Jan 2018
Name: editAccount.php (Edit Student Accounts)
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
                    <li><a href="editInfo.php">Edit Classroom Info</a></li>
                    <li class="active"><a href="editAccount.php">Edit Student Accounts</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Sign out (admin)</a></li>
                </ul>
            </div>
        </div>
    </nav>

	<div class="container-fluid bg-2 white-text text-center main-cont" >
  		<h1><b>Edit Student Accounts</b></h1><br>

  		<div class="row">
	  		<div class="col-sm-1">&nbsp;</div>
	  		<div class="col-sm-10 jumbotron" style="padding:0px">
	  			<button class="btn btn-default" style="width:100%; height:60px; font-size:30px; margin:0px" onclick="showNew()"> &plus; </button>
	  		</div>
	  		<div class="col-sm-1">&nbsp;</div>
  		</div>

  		<div class="jumbotron black-text" style="padding: 30px 40px 60px 40px">
  			<div class="col-md-5 col-sm-12">
	      		<select class="form-control grey-tooltip" id="acntType" style="margin-bottom:10px" data-toggle="tooltip" data-placement="top" title="Account type">
			        <option value="All">Show All</option>
			        <option value="Tr">Teachers</option>
			        <option value="Stu">Students</option>
			        <option value="Admins">Admins</option>
	      		</select>
  			</div>
		  	<div class="col-md-6 col-sm-10">
		  		<input type="text" class="form-control" id="acntSearchBar" placeholder="Search.." style="margin-bottom: 10px"/>
		  	</div>
		  	<div class="col-md-1 col-sm-2">
		  		<button class="btn btn-default grey-tooltip" id="acntClearSearch" data-toggle="tooltip" data-placement="top" title="Clear filters">&times;</button>
		  	</div>
  		</div>

  		<div class="jumbotron black-text" id="display_row">
			<div class="table-responsive">
				<table class="table table-hover table-bordered" id="displayTable">
					<thead>
						<tr>
							<th width="23%">Class / AC Type</th>
							<th width="23%">Name</th>
							<th width="20%">Username</th>
							<th>Password</th>
							<th width="10%"></th>
						</tr>
					</thead>
					<tbody>
						<tr id="new_row" style="display:none">
							<th><input type="text" id="new_class" data-toggle="tooltip" data-placement="bottom" title="Enter 'Tr' for teachers and 'Admin' for admin" class="grey-tooltip" /></th>
							<th><input type="text" id="new_name" /></th>
							<th><input type="text" id="new_id" /></th>
							<th><input type="password" id="new_pwd" data-toggle="tooltip" data-placement="bottom" title="Length cannot exceed 20 characters" class="grey-tooltip" /></th>
							<th>
								<button onclick="hideNew()" id="new_delete_btn" class="btn-danger btn" style="padding: 5px 10px">&times;</button>
								<button onclick="addAccount()" style="padding: 5px 10px" id="new_save_btn" class="btn-default btn"><span class="glyphicon glyphicon-floppy-disk"></span></button>
							</th>
						</tr>
						<?php
							try {
								$stmt = $db->prepare("SELECT * FROM `users`");
								$stmt->execute();
								foreach($stmt as $row) {
									$pwdMask = "";
									for($i = 0; $i < strlen($row['pwd']); $i++) $pwdMask = $pwdMask.'*';
									if ($row['class'] == "Tr") echo '<tr class="success">';
									else if ($row['class'] == "Admin") echo '<tr class="info">';
									else echo '<tr class="warning">';
									echo '
										<th onclick="edit(\''.$row['ID'].'\')"><label id="classLabel_'.$row['ID'].'">'.$row['class'].'</label><input type="text" style="display:none;" id="class_'.$row['ID'].'" value="'.$row['class'].'" /></th>
										<th onclick="edit(\''.$row['ID'].'\')"><label id="nameLabel_'.$row['ID'].'">'.$row['name'].'</label><input type="text" style="display:none;" id="name_'.$row['ID'].'" value="'.$row['name'].'" /></th>
										<th onclick="edit(\''.$row['ID'].'\')">'.$row['ID'].'</th>
										<th onclick="edit(\''.$row['ID'].'\')"><label id="pwdLabel_'.$row['ID'].'">'.$pwdMask.'</label><input onfocus="showPassword(\''.$row['ID'].'\')" onblur="hidePassword(\''.$row['ID'].'\')" type="password" style="display:none;" id="pwd_'.$row['ID'].'" value="'.$row['pwd'].'" /></th>
										<th>
											<button onclick="deleteAccount(\''.$row['ID'].'\')" id="delete_btn_'.$row['ID'].'" class="btn-danger btn" style="padding: 5px 10px">&times;</button>
											<button onclick="saveAccount(\''.$row['ID'].'\')" style="opacity: 0; padding: 5px 10px" id="save_btn_'.$row['ID'].'" class="btn-default btn"><span class="glyphicon glyphicon-floppy-disk"></span></button>
										</th> 
									</tr>';
								}
						    } catch (PDOException $ex) {
								echo "Error!";
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
	function deleteAccount(id) {
	  if(confirm('Are you sure you want to delete this account?')) {
	    $.ajax({
	        url: "servers.php",
	        type: "post",
	        data: {
	        	'request': 'account',
	        	'mode': 'delete',
	        	'id': id
	        },
	        success: function(result) {
			    if(result != "") alert(result);
			    else {
	      			$('#class_'+id).parent().parent().remove();
			    }
			}
	    }).fail(function (){
	      	alert("Oops... We've encountered an error!");
	      	window.location.href="editAccount.php";
	    });

	  }
	}

	function showNew() {
		$('#new_row').show();
		$('#new_class').focus();
	}

	function hideNew() {
		$('#new_class').val('');
		$('#new_name').val('');
		$('#new_id').val('');
		$('#new_pwd').val('');
		$('#new_row').hide();
	}

	function addAccount() {
		var _class = $('#new_class').val();
		var _name = $('#new_name').val();
		var _id = $('#new_id').val();
		var _pwd = $('#new_pwd').val();
		if(_class != "" && _name != "" && _id != "" && _pwd != "") {
			var oData = new FormData();
			oData.append("request", "account");
			oData.append("mode", 'add');
			oData.append("class", _class);
			oData.append("name", _name);
			oData.append("id", _id);
			oData.append("pwd", _pwd);
		    $.ajax({
		        url: "servers.php",
		        dataType: 'text',
			    data: oData,
			    type: 'POST',
			    contentType: false,
			    processData: false,
			    success: function(result) {
			    	if(result != "") alert(result);
			    	else window.location.href="editAccount.php";
			    }
		    }).fail(function (){
		      	alert("Oops... We've encountered an error!");
		      	window.location.href="editAccount.php";
		    });
		} else {
			alert("Please enter all required information!");
		}
	}

	function edit(id) {
		$('#class_'+id).show();
		$('#name_'+id).show();
		$('#pwd_'+id).show();
		$('#save_btn_'+id).css("opacity", 1);
		$('#classLabel_'+id).hide();
		$('#nameLabel_'+id).hide();
		$('#pwdLabel_'+id).hide();
	}

	function saveAccount(id) {
		var oData = new FormData();
		oData.append("request", "account");
		oData.append("mode", "edit");
		oData.append("class", $('#class_'+id).val());
		oData.append("name", $('#name_'+id).val());
		oData.append("pwd", $('#pwd_'+id).val());
		oData.append("id", id);
	    $.ajax({
	        url: "servers.php",
	        dataType: 'text',
		    data: oData,
		    type: 'POST',
		    contentType: false,
		    processData: false,
		    success: function(result) {
		    	if (result != "") alert(result);
		    	else {
					$('#classLabel_'+id).html( $('#class_'+id).val() );
					$('#nameLabel_'+id).html($('#name_'+id).val());
					var pwdMask = "";
					for(var i = 0; i < $('#pwd_'+id).val().length; i++) pwdMask = pwdMask + '*';
					$('#pwdLabel_'+id).html(pwdMask);
					$('#class_'+id).hide();
					$('#name_'+id).hide();
					$('#pwd_'+id).hide();
					$('#save_btn_'+id).css("opacity", 0);
					$('#classLabel_'+id).show();
					$('#nameLabel_'+id).show();
					$('#pwdLabel_'+id).show();
		    	}
		    }
	    }).fail(function (){
	      	alert("Oops... We've encountered an error!");
	      	window.location.href="editAccount.php";
	    });
	}
	function showPassword(id) {
		$('#pwd_'+id).attr("type", "text");
	}
	function hidePassword(id) {
		$('#pwd_'+id).attr("type", "password"); 
	}
	</script>

	<style>
		label {
			font-weight:normal;
		}
	</style>
</body>
</html>