<?php
	require("functions.php");
  $notice = "";
  $email = "";
  $emailError = "";
  $passwordError = "";
  
  if(isset($_POST["login"])){
		if (isset($_POST["email"]) and !empty($_POST["email"])){
			$email = test_input($_POST["email"]);
			} else {
			$emailError = "Please insert E-Mail!";
			}
		
			if (!isset($_POST["password"]) or strlen($_POST["password"]) < 8){
			$passwordError = "Please enter password(at least 8 characters)!";
			}
		
		if(empty($emailError) and empty($passwordError)){
		$notice = signin($email, $_POST["password"]);
		} else {
			$notice = "Cannot log in!";
		}
	}
?>
<!DOCTYPE html>
<html>
  <head>
		<meta charset="utf-8">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> 
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="icon" type="image/png" href="favicon.png" />
	<title>Messenger</title>
  </head>
  <body>
		<div class="container">
			<h1>Welcome</h1>
			<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<label>E-mail:</label><br>
				<input type="email" name="email" value="<?php echo $email; ?>">&nbsp;<span></span>
				<br>
				<label>Password:</label>
				<br>
				<input name="password" type="password">&nbsp;<span></span>
				<br>
				<input id="big-btn" name="login" type="submit" value="Log in">&nbsp;<span>
				<br>
			</form>
			<?php echo $emailError; ?><br>
			<?php echo $passwordError; ?><br>
			<?php echo $notice; ?><br>
			<div id="change-page">
				<a href="newuser.php">Create Account!</a>
			</div>
	</div>
  </body>
</html>