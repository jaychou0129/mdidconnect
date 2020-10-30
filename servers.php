<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';


$mail = new PHPMailer(true);                              // Passing `true` enables exceptions

//Server settings
$mail->SMTPDebug = 1;                                 // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'mdid.connect@gmail.com';                 // SMTP username
$mail->Password = 'asdfghjkl;\'';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

include 'db.php';

if (isset($_POST['request']) && $_POST['request'] == 'index') {
	try {
	    //Recipients
	    $mail->setFrom('mdid.connect@gmail.com', 'MDID ConNect System');
	    $mail->addAddress('jay920129@gmail.com');     // Add a recipient
	    $mail->addReplyTo('jay920129@gmail.com', 'MDID ConNect System');

	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = 'Feedback for MDID ConNect System';
	    $mail->Body    = '
		    <html>
		    <head>
				<meta charset="utf-8">
		    </head>
		    <body>
		    	<h3>You have received 1 suggestion from an anonymous user of the MDID ConNect System.</h3>
			    <p>Message:<br>
			    	'.nl2br($_POST['message']).'
			    </p>
			    <a href="localhost/ConNect/index.php">View Website</a>
		    </body>
		    </html>
	    ';
	    $mail->AltBody = nl2br($_POST['message']);
	    $mail->send();
	} catch (Exception $e) {}
}

if (isset($_POST['request']) && $_POST['request'] == 'reserve') {
	$sentBy = strip_tags($_POST['stuName']);
	$room = strip_tags($_POST['room']);
	$date = strip_tags($_POST['date']);
	$periods = strip_tags($_POST['periods']);
	$purpose = strip_tags($_POST['purpose']);
	$personnel = isset($_POST['personnel'])? strip_tags($_POST['personnel']) : "";

	try {
		$stmt = $db->prepare("INSERT INTO `reservations`(`sentBy`, `room`, `date`, `periods`, `purpose`, `personnel`) VALUES (?,?,?,?,?,?)");
		$stmt->execute(array($sentBy, $room, $date, $periods, $purpose, $personnel));

		//update timesReserved of the room.
		$stmt = $db->prepare("SELECT COUNT( * ) as 'total' FROM `reservations` WHERE `room` = ?");
		$stmt->execute(array($room));
		foreach($stmt as $row) { $totalValue = $row['total'];}
		$stmt = $db->prepare("UPDATE `classrooms` SET `timesReserved` = ? WHERE `name` = ?");
		$stmt->execute(array($totalValue, $room));


		// send email to admin
		try {
		    //Recipients
		    $mail->setFrom('mdid.connect@gmail.com', 'MDID ConNect System');
		    $mail->addAddress('jay920129@gmail.com');     // Add a recipient

		    //Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = 'New Reservation on MDID ConNect System';
		    $mail->Body    = '
			    <html>
			    <head>
					<meta charset="utf-8">
			    </head>
			    <body>
				    <h3>New Reservation!</h3>
				    <p>'.$_POST['fullName'].' made a new reservation for <b>'.$room.'</b>. <br>
				    	Date: <b>'.$date.'</b><br>
				    	Periods: <b>'.$periods.'</b>
				    </p>
				    <a href="localhost/ConNect/approve.php">View Request</a>
			    </body>
			    </html>
		    ';
		    $mail->AltBody = 'There is a new reservation for '.$room.' on '.$date.' '.$periods.' made by '.$_POST['fullName'].'!';
		    $mail->send();
		} catch (Exception $e) { }
		header("location: history.php");
	} catch (PDOException $ex) {
		echo "Error!";
	}
}

if (isset($_POST['request']) && $_POST['request'] == 'deleteRequest') {
	if(isset($_POST['id'])) {
		try {
		   	$stmt = $db->prepare("SELECT * FROM `reservations` WHERE `r_id` = ?");
		    $stmt->execute(array($_POST['id']));
		    foreach($stmt as $row) {
		    	$room = $row['room'];
			    $stmt = $db->prepare("DELETE FROM `reservations` WHERE `r_id` = ?");
			    $stmt->execute(array($_POST['id']));
			}

			// update timesReserved of the room.
			$stmt = $db->prepare("SELECT COUNT( * ) as 'total' FROM `reservations` WHERE `room` = ?");
			$stmt->execute(array($room));
			foreach($stmt as $row) { $totalValue = $row['total'];}
			$stmt = $db->prepare("UPDATE `classrooms` SET `timesReserved` = ? WHERE `name` = ?");
			$stmt->execute(array($totalValue, $room));
	  	} catch (PDOException $ex) {
	    	echo $ex->getMessage();
	  	}
	}
}

if (isset($_POST['request']) && $_POST['request'] == 'approve') {
	try {
		// update status
		$mode = strip_tags($_POST['mode']);
		$message = strip_tags($_POST['message']);
		$id = strip_tags($_POST['id']);
   		$stmt = $db->prepare("UPDATE `reservations` SET `status`= ?, `message`= ? WHERE `r_id` = ?");
    	$stmt->execute(array($mode, $message, $id));

    	// get reserved room and email address of user
    	$stmt = $db->prepare("SELECT * FROM `reservations` WHERE `r_id` = ?");
    	$stmt->execute(array($id));
    	foreach($stmt as $row) { $userEmail = $row['sentBy'].'@ms.mingdao.edu.tw'; $reservedRoom = $row['room']; }

		// send email to user
		try {
		    //Recipients
		    $mail->setFrom('mdid.connect@gmail.com', 'MDID ConNect System');
		    $mail->addAddress($userEmail);
		    $mail->isHTML(true);

		    if ($_POST['mode'] == '1') {
		    	$mail->Subject = 'Reservation approved on MDID ConNect System';
		    	$mail->Body = '
				    <html>
				    <head>
						<meta charset="utf-8">
				    </head>
				    <body>
				    	<h3>Reservation approved!</h3>
				    	<p>Your reservation for <b>'.$reservedRoom.'</b> has been approved by the admin!</p>
				    	<a href="localhost/ConNect/history.php">View Request</a>
				    </body>
				    </html>';
				$mail->AltBody = 'Your reservation for '.$reservedRoom.' has been approved by the admin!';
		    }
		    else {
		    	$mail->Subject = 'Reservation declined on MDID ConNect System';
		    	$mail->Body = '
				    <html>
				    <head>
						<meta charset="utf-8">
				    </head>
				    <body>
				    	<h3>Reservation declined!</h3>
				    	<p>Your reservation for <b>'.$reservedRoom.'</b> has been declined by the admin!</p>
				    	<p>Message:<br>'.nl2br($message).'</p>
				    	<a href="localhost/ConNect/history.php">View Request</a>
				    </body>
				    </html>
				';
				$mail->AltBody = 'Your reservation for '.$reservedRoom.' has been declined by the admin!';
		    }

		    $mail->send();
		} catch (Exception $e) {
		    echo 'Automated Email was not sent properly. Mailer Error: ', $mail->ErrorInfo;
		}
  	} catch (PDOException $ex) {
    	echo "Error!";
  	}
}

if(isset($_POST['request']) && $_POST['request'] == 'updateInfo') {
  	if ($_FILES['disFile']['tmp_name'] != "") {
	  	$target = "img/img_".$_POST['id'].".jpg";
	  	move_uploaded_file($_FILES['disFile']['tmp_name'], $target);
	}
  	if ($_FILES['schFile']['tmp_name'] != "") {
	  	$target = "img/sch_".$_POST['id'].".jpg";
	  	move_uploaded_file($_FILES['schFile']['tmp_name'], $target);
	}
	try {
		$room_name = $_POST['room_name'];
		$room_floor = $_POST['room_floor'];
		$id = $_POST['id'];
		$stmt = $db->prepare("UPDATE `classrooms` SET `name` = ?, `description` = ? WHERE `id` = ?");
		$stmt->execute(array($room_name, $room_floor, $id));
	} catch (PDOException $ex) {
		echo "Error!";
	}	
}

if(isset($_POST['request']) && $_POST['request'] == 'deleteRoom') {
	if(isset($_POST['id'])) {
		try {
		    $stmt = $db->prepare("DELETE FROM `classrooms` WHERE `id` = ?");
		    $stmt->execute(array($_POST['id']));
	  	} catch (PDOException $ex) {
	    	echo "Error!";
	  	}
	}
}

if(isset($_POST['request']) && $_POST['request'] == 'addRoom') {
	if(isset($_POST['room_name']) && isset($_POST['room_floor'])) {
		try {
			$room_name = $_POST['room_name'];
			$room_floor = $_POST['room_floor'];
		    $stmt = $db->prepare("INSERT INTO `classrooms`(`name`, `description`) VALUES (?, ?)");
		    $stmt->execute(array($room_name, $room_floor));

		    $stmt = $db->prepare("SELECT `id` FROM `classrooms` ORDER BY `id` DESC LIMIT 1");
		    $stmt->execute();
		    foreach($stmt as $row) { $id = $row['id']; }
	  	} catch (PDOException $ex) {
	    	echo "Error!";
	  	}
	} else {
		echo "Error!";
	}

	if ($_FILES['disFile']['tmp_name'] != "") {
	  	$target = "img/img_".$id.".jpg";
	  	move_uploaded_file($_FILES['disFile']['tmp_name'], $target);
	}
	if ($_FILES['schFile']['tmp_name'] != "") {
	  	$target = "img/sch_".$id.".jpg";
	  	move_uploaded_file($_FILES['schFile']['tmp_name'], $target);
	}
}

if(isset($_POST['request']) && $_POST['request'] == 'account') {
	$id = strip_tags($_POST['id']);

	if ($_POST['mode'] == 'edit') {
		$class = strip_tags($_POST['class']);
		$name = strip_tags($_POST['name']);
		$pwd = strip_tags($_POST['pwd']);
		if (!is_numeric($class) && $class != "Tr" && $class != "Admin") echo "Please enter class in the correct format. Examples of acceptable formats:\n\t1001\n\tTr\n\tAdmin";
		else if (strlen($name) > 20) echo "Name must be within 20 characters!";
		else if (strlen($pwd) > 20) echo "Password must be within 20 characters!";
		else {
			try {
				$stmt = $db->prepare("UPDATE `users` SET `class` = ?, `name` = ?, `pwd` = ? WHERE `ID` = ?");
				$stmt->execute(array($class, $name, $pwd, $id));
			} catch (PDOException $ex) {
				echo "Oops... We've encountered an error!";
			}		
		}

	}

	if ($_POST['mode'] == 'delete') {
		try {
		    $stmt = $db->prepare("DELETE FROM `users` WHERE `ID` = ?");
		    $stmt->execute(array($id));
	  	} catch (PDOException $ex) {
	    	echo "Oops... We've encountered an error!";
	  	}
	}

	if ($_POST['mode'] == 'add') {
		$class = strip_tags($_POST['class']);
		$name = strip_tags($_POST['name']);
		$pwd = strip_tags($_POST['pwd']);
		try {
			include 'db.php';
			$stmt = $db->prepare("SELECT `ID` FROM `users` WHERE `ID` = ?");
			$stmt->execute(array($_POST['id']));
			$row_count = $stmt->rowCount();
			if ($row_count != 0) echo "Username already taken!";
			else if (!is_numeric($class) && $class != "Tr" && $class != "Admin") echo "Please enter class in the correct format. Examples of acceptable formats:\n\t1001\n\tTr\n\tAdmin";
			else if (strlen($name) > 20) echo "Name must be within 20 characters!";
			else if (strlen($pwd) > 20) echo "Password must be within 20 characters!";
			else {
				$stmt = $db->prepare("INSERT INTO `users`(`class`, `name`, `ID`, `pwd`) VALUES (?,?,?,?)");
				$stmt->execute(array($class, $name, $id, $pwd));
			}
		} catch (PDOException $ex) {
			echo "OOps... We've encountered an error!";
		}
	}
}

?>