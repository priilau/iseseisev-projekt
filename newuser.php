<?php
  //lisan teise php faili
  require("functions.php");
  $notice = "";
  $email = "";
  
  $emailError = "";
  $passwordError = "";
  $checkpasswordError = "";
  
  //püüan POST andmed kinni
  //var_dump($_POST);
  if(isset($_POST["submitUserData"])){ //kas on üldse nuppu vajutatud
		if(isset($_POST["email"]) and !empty($_POST["email"])){
			$email = test_input($_POST["email"]);
		} else {
			$emailError = "Please insert E-Mail!";
		}
	//parooli pikkuse kontroll
	//strlen($_POST["password"]) >= 8
	

		if(isset($_POST["password"]) and !empty($_POST["password"]) and strlen($_POST["password"]) >= 8 and strlen($_POST["password"]) <= 60){
			$password = test_input($_POST["password"]);
		} elseif(strlen($_POST["password"]) > 0 and strlen($_POST["password"]) < 8) {
			$passwordError = "Password must be over 8 characters!";  
		} elseif(strlen($_POST["password"]) > 60) {
			$passwordError = "Password cannot be over 60 characters!";
		} elseif(empty($_POST["password"])){
			$passwordError = "Please insert password!";
		}
		
		if(isset($_POST["checkpassword"]) and !empty($_POST["checkpassword"]) and ($_POST["checkpassword"]) == ($_POST["password"])){
			$checkpasswordError = "";
		} elseif(empty($_POST["checkpassword"])){
			$checkpasswordError = "Password box is empty!";
		} else {
			$checkpasswordError = "Entered passwords do not match!";
		}  
  //kõik kontrollid tehtud
		if(empty($emailError) and empty($passwordError) and empty($checkpasswordError)){
			$notice = signup($_POST["email"], $_POST["password"]);
			$notice = "Your account has been created, you can now log in!";
		}
  }
  //kas on üldse nuppu vajutatud lõppeb
  
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> 
	<link rel="icon" type="image/png" href="favicon.png" />
	<link rel="stylesheet" type="text/css" href="style.css">
  <title>
  Messenger
  </title>
</head>
<body>
	<div class="container">
		<h1>
		Create Account
		</h1>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label>E-mail: </label>
		<br>
		<input type="email" name="email"><span></span>
		<br>
		<label>Password (min 8 chars): </label>
		<br>
		<input type="password" name="password"><span></span>
		<br>
		<label>Repeat password: </label>
		<br>
		<input type="password" name="checkpassword"><span></span>
		<br>
		<input id="big-btn" type="submit" name="submitUserData" value="Create Account">
		<br>
		</form>
		<?php echo $emailError; ?><br>
		<?php echo $passwordError; ?><br>
		<?php echo $checkpasswordError; ?><br>
		<?php echo $notice; ?><br>
		<div id="change-page">
			<a href="index.php">Back to front page</a>
		</div>
	</div>
</body>
</html>


