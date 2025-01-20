<<<<<<< Updated upstream
=======
<?php
   include("config.php");
   session_start();

   $error = '';

   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Use prepared statements to prevent SQL injection
      $myusername = $_POST['username'];
      $mypassword = $_POST['password'];

      // Prepare the query
      $stmt = $db->prepare("SELECT password FROM user WHERE username = ?");
      $stmt->bind_param("s", $myusername);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows == 1) {
         $stmt->bind_result($hashed_password);
         $stmt->fetch();

         // Verify the password
         if (password_verify($mypassword, $hashed_password)) {
            $_SESSION['login_user'] = $myusername;
            header("Location: http://localhost/appProject/index.html");
            exit;
         } else {
            $error = "Invalid username or password.";
         }
      } else {
         $error = "Invalid username or password.";
      }

      $stmt->close();
   }
?>
>>>>>>> Stashed changes
