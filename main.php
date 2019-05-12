<?php
  require("functions.php");
  
  //kui pole sisse loginud
  if(!isset($_SESSION["userID"])){
	  header("Location: index.php");
	  exit();
  }
  
  //välja logimine
  if(isset($_GET["logout"])){
	  session_destroy();
	  header("Location: index.php");
	  exit();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	<script type="text/javascript">
		let userID='<?php echo $_SESSION["userID"];?>';
	</script>
	<script type="text/javascript" src="main.js" defer></script>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="icon" type="image/png" href="favicon.png" />
	<title>Messenger</title>
  </head>
  <body>
		<div class="container">
		<h1>Messenger</h1>
			<div class="content">
				<div id="friend-list"></div>
				<div id="chatbox"></div>
				<div id="user-list"></div>
				<form enctype="multipart/form-data" method="POST" id="msg-form">
				<div id="inputbox"></div>
				</form>
				<div id="change-page">
					<a href="?logout=1>">Log out</a>
				</div>
				<button class="a2hs">Add to Homescreen</button>
			</div>
		</div>
		<script type="text/javascript" src="a2hs.js" defer></script>
  </body>
</html>