<?php
	require_once("functions.php");
	// data.php
	
	// kui kasutaja ei ole sisseloginud,
	// siis suunan tagasi
	if(!isset($_SESSION["logged_in_user_id"])){
		header("Location: login.php");
		
	}
	
	// kasutaja tahab välja logima
	
	if(isset($_GET["logout"])){
		// aadressireal on olemas muutuja logout
		
		//kustutame kõik sessoni muutujad ja peatame sessiooni
		session_destroy();
		
		header("Location: login.php");
	}
	
	$teamname = $summa = "";
	$teamname_error = $summa_error = "";
	
	//keegi vajutas nuppu numbrimärgi lisamiseks
	if(isset($_POST["add_bet"])){
		// echo $_SESSION["logged_in_user_id"];
		
		// valideerite väljad
		// mõlemad on kohustuslikud
		// salvestatakse AB'i fn kaudu addCarPlate
		if ( empty($_POST["teamname"]) ) {
				$teamname_error = "See väli on kohustuslik";
			}else{
			// puhastame muutuja võimalikest üleliigsetest sümbolitest
				$teamname = cleanInput($_POST["teamname"]);
			}
		if ( empty($_POST["summa"]) ) {
				$summa_error = "See väli on kohustuslik";
			}else{
			// puhastame muutuja võimalikest üleliigsetest sümbolitest
				$summa = cleanInput($_POST["summa"]);
			}
		if(	$teamname_error == "" && $summa_error == ""){
					
					
					
					// kasutaja loomise funktsioon, failist functions.php
					// saadame kaasa muutujad
					$message = addBet($teamname, $summa);
					
					if($message != ""){
						// õnnestus, teeme inputi väljad tühjaks
						$number_plate = "";
						$color = "";
						
						echo $message;
						
					}
				}
	}
	
	$array_of_teams = getTeamData();
	
	$keyword = "";
	
	if(isset($_GET["keyword"])){
		
		//otsin
		$keyword = $_GET["keyword"];
		$array_of_teams = getTeamData($keyword);
		
	}else{
		
		//küsin kõik andmed
		$array_of_teams = getTeamData();
	}
	
	// kas kustutame
	// ?delete=vastav id mida kustutame on aadressireal
	if(isset($_GET["delete"])){
		
		if($email_from_db == 'toomas@toomas.ee'){
			echo "kustutame id ".$_GET["delete"];
			//käivitan funktsiooni, saadan kaasa id
			deleteTeam($_GET["delete"]);
		}
	}
	
	// salvestan muudatuse andmebaasi
	if(isset($_POST["save"])){
		if($email_from_db == 'toomas@toomas.ee'){
			updateTeam($_POST["id"], $_POST["teamname"], $_POST["player1"], $_POST["player2"], $_POST["player3"], $_POST["player4"], $_POST["player5"]);
		}
	}
	
?>
<p>
	Tere, <?=$_SESSION["logged_in_user_email"];?>
	<a href="?logout=1">logi välja<a>	
</p>

<form action="data.php" method="get">
	<input type="search" name="keyword" value="<?=$keyword?>">
	<input type="submit">
</form>

<p>
<h1>Tiimid</h1>
<table border=1>
	<tr>
		<th>id</th>
		<th>teamname</th>
		<th>player1</th>
		<th>player2</th>
		<th>player3</th>
		<th>player4</th>
		<th>player5</th>
		<th>kustuta</th>
		<th>edit</th>
	</tr>
</p>

	<?php
		for($i = 0;$i < count($array_of_teams);$i++){
			// kasutaja tahab muuta seda rida
			if(isset($_GET["edit"]) && $array_of_teams[$i]->id == $_GET["edit"]){
				// admin saab tabelit muuta
				
				echo "<tr>";
				echo "<form action='editdata.php' method='post'>";
				echo "<input type='hidden' name='id' value='".$array_of_teams[$i]->id."'>";
				echo "<td>".$array_of_teams[$i]->id."</td>";
				echo "<td><input name='teamname' value='".$array_of_teams[$i]->teamname."'></td>";
				echo "<td><input name='player1' value='".$array_of_teams[$i]->player1."'></td>";
				echo "<td><input name='player2' value='".$array_of_teams[$i]->player2."'></td>";
				echo "<td><input name='player3' value='".$array_of_teams[$i]->player3."'></td>";
				echo "<td><input name='player4' value='".$array_of_teams[$i]->player4."'></td>";
				echo "<td><input name='player5' value='".$array_of_teams[$i]->player5."'></td>";
				echo "<td><a href='editdata.php'>cancel</a></td>";
				echo "<td><input type='submit' name='save'></td>";
				echo "</form>";
				echo "</tr>";
				
				
			}else{
				echo "<tr>";
				echo "<td>".$array_of_teams[$i]->id."</td>";
				echo "<td>".$array_of_teams[$i]->teamname."</td>";
				echo "<td>".$array_of_teams[$i]->player1."</td>";
				echo "<td>".$array_of_teams[$i]->player2."</td>";
				echo "<td>".$array_of_teams[$i]->player3."</td>";
				echo "<td>".$array_of_teams[$i]->player4."</td>";
				echo "<td>".$array_of_teams[$i]->player5."</td>";
				echo "<td><a href='?delete=".$array_of_teams[$i]->id."'>X</a></td>";
				echo "<td><a href='?edit=".$array_of_teams[$i]->id."'>edit</a></td>";
				echo "</tr>";
				
				
			}
		}
	
	?>
</table>
	
  <h2>Place your bets</h2>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" >
  	<label for="teamname">Tiiminimi</label><br>
	<input id="teamname" name="teamname" type="text" value="<?php echo $teamname; ?>"> <?php echo $teamname_error; ?><br><br>
	<label for="summa">Summa</label><br>
  	<input id="summa" name="summa" type="float" value="<?php echo $summa; ?>"> <?php echo $summa_error; ?><br><br>
  	<input type="submit" name="add_bet" value="Salvesta">
  </form>