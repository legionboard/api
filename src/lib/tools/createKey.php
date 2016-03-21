<?php
/*
 * @author	Nico Alt
 * @date	16.03.2016
 *
 * See the file "LICENSE" for the full license governing this code.
 * 
 * DELETE AFTER USAGE!!!
 * Copy this script two folders above (where `api.php`, `index.html`, etc. is)
 */
// Set UTF-8
header("Content-Type: text/html; charset=utf-8");
$username = $_POST['username'];
$password = $_POST['password'];
$groups = $_POST['groups'];

try {
	// Include authentication
	require '../lib/authentication.php';
	$authentication = new Authentication();

	// Check if strings are "POSTed"
	if (empty($username) && empty($password) && empty($groups)) {
		throw new Exception();
	}

	// Check if strings are not empty
	if ($username == '' || $password == '' || $groups == '') {
		throw new Exception("Please check the fields.");
	}

	// Create key
	$key = hash('sha256', strtolower(trim($username)) . '//' . trim($password));
	if (!$authentication->create($key, $groups)) {
		throw new Exception("Could not add the key.");
	}

	// Print out message with details
	$msg = "Key added successfully.";
}
catch (Exception $e) {
	$msg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Create key</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
	</head>
	<body>
		<h1>DELETE AFTER USAGE!!!</h1>
		<p><b><?=$msg?></b></p>
		<form method="post" accept-charset="UTF-8">
			<p><input type="text" name="username" placeholder="Username" value="<?=$username?>" required/></p>
			<p><input type="text" name="password" placeholder="Password" value="<?=$password?>" required/></p>
			<p><input type="text" name="groups" placeholder="Groups (comma separated)" value="<?=$groups?>" required/></p>
			<p><input type="submit" /></p>
		</form>
		<p>Groups:</p>
		<p>0: See changes</p>
		<p>1: Add change</p>
		<p>2: Update change</p>
		<p>3: Delete change</p>
		<p>4: See teachers</p>
		<p>5: Add teacher</p>
		<p>6: Update teacher</p>
		<p>7: Delete teacher</p>
		<p>8: See reasons</p>
		<p>9: See private texts</p>
	</body>
</html>
