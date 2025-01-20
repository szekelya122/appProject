
<?php
   include("config.php");
   session_start();
   $error='';
   if($_SERVER["REQUEST_METHOD"] == "POST") {
   
      // username and password sent from form 
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

      $sql = "SELECT * FROM user WHERE username = '$myusername' and passcode = '$mypassword'";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    

      $result = mysqli_query($db,$sql);      
      $row = mysqli_num_rows($result);      
      $count = mysqli_num_rows($result);    
    
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    

      if($count == 1) {
	  
         // session_register("myusername");
         $_SESSION['login_user'] = $myusername;
         header("location: welcome.php");
      } else {
         $error = "Your Login Name or Password is invalid";
      }
   }

    if ($errors) {
        
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        try {
            // Hash the password for secure storage
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => htmlspecialchars($username),
                ':password' => $hashedPassword,
                ':email' => $email,
            ]);

            echo "<p style='color: green;'>User registered successfully!</p>";
        } catch (PDOException $e) {
            // Handle unique constraint violations for email or username
            if ($e->getCode() == 23000) {
                echo "<p style='color: red;'>A user with this email or username already exists.</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
}
?>  