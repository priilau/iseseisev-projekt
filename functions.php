<?php
	require("config.php");
	$database = "if18_priit_la_3";

	if (!isset($_GET['actionG'])){
		$_GET['actionG'] = null;
	}
		
	if (!isset($_POST['actionP'])){
		$_POST['actionP'] = null;
	}
  //var_dump($_GET);  
	//var_dump($_POST);
	//die;  
	session_start();
	
	switch ($_GET['actionG']) {
		case "GetFriends":
			echo json_encode(listfriends($_SESSION["userID"]));
      break;
		case "GetMessages":
			echo json_encode(readmsg($_SESSION["userID"], $_SESSION["userID"], $_GET["friendID"], $_GET["friendID"]));
			break;
		case "GetUsers":
			echo json_encode(listusers($_SESSION["userID"]));
      break;
	}

	switch($_POST['actionP']){
		case "SendMessage":
			echo json_encode(sendmsg($_POST["message"], $_SESSION["userID"], $_POST["friendID"]));
			break;
		case "AddFriend":
			echo json_encode(addfriend($_SESSION["userID"],$_POST["friendID"]));
			break;
		case "RemoveFriend":
			echo json_encode(removefriend($_SESSION["userID"],$_POST["friendID"]));
			break;
	} 

	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	function signup($email, $password){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id FROM users WHERE email=?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		if($stmt->fetch()){
			$notice = "Sellise E-mailiga (" .$email .") on juba kasutaja loodud!";
		} else {
			$stmt = $mysqli->prepare("INSERT INTO users (email, password) VALUES(?, ?)");
		    echo $mysqli->error;
		    //krüpteerime parooli ära
				$options = ["cost"=>12, "salt"=>substr(sha1(mt_rand()), 0, 22)];
				$pwdhash = password_hash($password, PASSWORD_BCRYPT, $options);
				$stmt->bind_param("ss", $email, $pwdhash);
			if ($stmt->execute()){
				$notice = 'Kasutaja loomine õnnestus!';
			} else {
				$notice = "Kasutaja loomisel tekkis viga: " .$stmt->error;
			}
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	//sisselogimine
	function signin($email, $password){
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id, password FROM users WHERE email=?");
		echo $mysqli->error;
		$stmt->bind_param("s", $email);
		$stmt->bind_result($idFromDB, $passwordFromDB);
		if($stmt->execute()){
			//andmebaasi päring õnnestus
			if($stmt->fetch()){
				//kasutaja on olemas
				if(password_verify($password, $passwordFromDB)){
					//parool õige
					$notice = "Olete edukalt sisseloginud!";
					//määrame sessiooni muutujad
					$_SESSION["userID"] = $idFromDB;
					$stmt->close();
					$mysqli->close();
					header("location: main.php");
					exit();
				} else {
					$notice = "Sisestatud parool ei ole õige!";
				}
			} else {
				$notice = "Kahjuks sellist kasutajatunnust" .$email ." ei leitud!";
			}
		} else {
			$notice = "Sisselogimisel tekkis tehniline viga" .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}

	function sendmsg($msg, $senderId, $recieverId){
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO messages (message, sender_id, reciever_id) VALUES(?, ?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("sii", $msg, $senderId, $recieverId);
		$stmt->execute();
		$stmt->close();
		$mysqli->close();
	}

	function readmsg($userID, $userID2, $friendID, $friendID2){
		$messages = [];
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT * FROM messages WHERE (reciever_id=? OR sender_id=?) AND (reciever_id=? OR sender_id=?)");
		echo ($mysqli->error);
		$stmt->bind_param("iiii", $userID, $userID2, $friendID, $friendID2);
		$stmt->bind_result($id, $msg, $senderId, $recieverId, $timestamp);
		$stmt->execute();
		while ($stmt->fetch()){
			$messages[] = [
				'id' => $id,
				'message' => $msg,
				'recieverId' => $recieverId,
				'senderId' => $senderId,
				'timestamp' => $timestamp
			]; 
		}
		$stmt->close();
		$mysqli->close();
		return $messages;
	}
	function listfriends($userID){
		$userID2 = $userID; 
		$friends = [];
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		//$stmt = $mysqli->prepare("SELECT * FROM friendlist WHERE user_id=?");
		$stmt = $mysqli->prepare("SELECT users.id, users.email FROM users JOIN friendlist ON users.id = friendlist.friend_id WHERE friendlist.user_id=?");
		echo $mysqli->error;
		$stmt->bind_param("i", $userID);
		//$stmt->bind_result($id, $user, $friend);
		$stmt->bind_result($id, $email);
		$stmt->execute();
		while ($stmt->fetch()){
			$friends[] = [
				'user_id' => $id,
				'email' => $email
			];
		}
		$stmt->close();
		$mysqli->close();
		return $friends;
	}
	function listusers($userID){
		$users = [];
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		//$stmt = $mysqli->prepare("SELECT users.id, users.email FROM users JOIN friendlist ON users.id=friendlist.user_id OR users.id=friendlist.friend_id WHERE users.id=? AND friendlist.user_id!=? AND friendlist.friend_id!=?");
		//$stmt = $mysqli->prepare("SELECT id, email FROM users WHERE NOT EXISTS (SELECT user_id FROM friendlist WHERE users.id=? AND (friendlist.user_id=users.id OR friendlist.friend_id=users.id)");
		//$stmt = $mysqli->prepare("SELECT users.id, users.email FROM users WHERE users.id!=?");
		$stmt = $mysqli->prepare("SELECT users.id, users.email FROM users WHERE users.id!=?");
		echo $mysqli->error;
		$stmt->bind_param("i", $userID);
		$stmt->bind_result($id, $email);
		$stmt->execute();
		while ($stmt->fetch()){
			$users[] = [
				'user_id' => $id,
				'email' => $email
			];
		}
		$stmt->close();
		$mysqli->close();
		return $users;
	}

	function addfriend($userID, $friendID){
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO friendlist (user_id, friend_id) VALUES(?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("ii", $userID, $friendID);
		$stmt->execute();
		$stmt->close();
		$mysqli->close();
	}

	function removefriend($userID, $friendID){
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("DELETE FROM friendlist WHERE user_id=? AND friend_id=?");
		echo $mysqli->error;
		$stmt->bind_param("ii", $userID, $friendID);
		$stmt->execute();
		$stmt->close();
		$mysqli->close();
	}
?>