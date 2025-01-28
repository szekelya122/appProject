<?php
session_start();

// MySQL kapcsolódás
$servername = "localhost";
$username = "root";  // Az adatbázis felhasználó neve
$password = "root";  // Az adatbázis jelszava
$dbname = "webshop"; // Az adatbázis neve

$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolódás ellenőrzése
if ($conn->connect_error)    {
    die("Connection failed: " . $conn->connect_error);
}

// POST kérések kezelése
if ($stmt->num_rows > 0) {
	$stmt->bind_result($id, $password);
	$stmt->fetch();
	// Account exists, now we verify the password.
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if (password_verify($_POST['password'], $password)) {
		// Verification success! User has logged-in!
		// Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;
		echo 'Welcome back, ' . htmlspecialchars($_SESSION['name'], ENT_QUOTES) . '!';
	} else {
		// Incorrect password
		echo 'Incorrect username and/or password!';
	}
} else {
	// Incorrect username
	echo 'Incorrect username and/or password!';
}



?>
