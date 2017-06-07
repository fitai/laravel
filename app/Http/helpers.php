<?php
function site_url() {
	return "http://ec2-52-204-229-101.compute-1.amazonaws.com/";
}
function home_path($data) {
	echo "/var/www/html/".$data;
}

/* Client Calls */
function get_athlete_school() {
	global $myPDO;
	$query = $myPDO->prepare("
		SELECT cli.client_name 
		FROM client_info AS cli 
		INNER JOIN athlete_info AS ai 
		ON cli.client_id = ai.client_id 
		WHERE ai.user_id=:user_id");
	$query->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_ASSOC);
	$name = $fetch['client_name'];
	
	return $name;
}

/* Team Calls */
function get_athlete_team_id() {
	global $myPDO;
	$query = $myPDO->prepare("SELECT team_id FROM athlete_info WHERE user_id=:user_id");
	$query->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_OBJ);
	$teamID = $fetch->team_id;
	
	return $teamID;
}
function get_athlete_team_name() {

	$name = Auth::user()->athlete->team->team_name;

	return $name;
}

/* Coach Calls */
function get_coach_id() {
	global $myPDO;
	$query = $myPDO->prepare("SELECT coach_id FROM athlete_info WHERE user_id=:user_id");
	$query->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_OBJ);
	$coachID = $fetch->coach_id;
	
	return $coachID;
}
function get_coach_name() {
	global $myPDO;
	$query = $myPDO->prepare("
		SELECT ci.coach_first_name, ci.coach_last_name
		FROM coach_info AS ci 
		INNER JOIN athlete_info AS ai 
		ON ci.coach_id = ai.coach_id 
		WHERE ai.user_id=:user_id");
	$query->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_ASSOC);
	$firstName = $fetch['coach_first_name'];
	$lastName = $fetch['coach_last_name'];
	$fullName = $firstName." ".$lastName;
	
	return $fullName;
}
function get_coach_first_name() {
	global $myPDO;
	$query = $myPDO->prepare("
		SELECT ci.coach_first_name 
		FROM coach_info AS ci 
		INNER JOIN athlete_info AS ai 
		ON ci.coach_id = ai.coach_id 
		WHERE ai.user_id=:user_id");
	$query->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_ASSOC);
	$firstName = $fetch['coach_first_name'];
	
	return $firstName;
}

/* Athlete Calls */
function get_athlete_id($userID) {	
	global $myPDO;
	$query = $myPDO->prepare("SELECT athlete_id FROM athlete_info WHERE user_id=:user_id");
	$query->bindParam(':user_id', $userID, PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_ASSOC);
	$athleteID = $fetch['athlete_id'];

	return $athleteID;
}
function get_athlete_name() {
	if (Auth::user()) :
		$athlete = Auth::user()->athlete;
		$firstName = $athlete->athlete_first_name;
		$lastName = $athlete->athlete_last_name;
		$fullName = $firstName." ".$lastName;
	else :
		$fullName = 'Guest';
	endif;

	return $fullName;
}
function query_athlete_name($id) {
	$athlete = \App\User::find($id)->athlete;
	$firstName = $athlete->athlete_first_name;
	$lastName = $athlete->athlete_last_name;
	$fullName = $firstName." ".$lastName;
	
	return $fullName;
}
function get_athlete_age() {
	$athlete = \App\Athlete::where('user_id', Auth::id())->first();
	
	return $athlete->athlete_age;
}
function get_athlete_gender() {
	$athlete = \App\Athlete::where('user_id', Auth::id())->first();
	
	return $athlete->athlete_gender;
}
function get_next_lift() {
	global $myPDO;
	$query = $myPDO->prepare("SELECT * FROM lift_schedule WHERE user_id=:user_id AND complete <> 'y' ORDER BY id ASC LIMIT 1");
	$query->bindParam(':user_id', $_SESSION['userID'], PDO::PARAM_STR);
	$result = $query->execute();
	$fetch = $query->fetch(PDO::FETCH_ASSOC);
		
	return $fetch;
}

/* Collar Calls */
function get_team_collars() {
	$collars = Auth::user()->athlete->team->collars;
	
	return $collars;
}

/* Lift Type Calls */
function get_lift_types() {
	// global $myPDO;
	// $query = $myPDO->prepare("
	// 	SELECT * 
	// 	FROM lift_types
	// 	ORDER BY name_display ASC
	// ");
	// $result = $query->execute();
	// $fetch = $query->fetchAll(PDO::FETCH_ASSOC);

	$liftTypes = \App\LiftType::orderBy('name_display', 'ASC')->get();
	
	return $liftTypes;
}
?>