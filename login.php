
<?php include 'component/navbar_links.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <style>
      .login-form {
         max-width: 400px;
         margin: 50px auto;
         padding: 20px;
         background: #fff;
         border-radius: 5px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      .login-form h1 {
         margin-bottom: 20px;
         font-size: 24px;
         text-align: center;
         color: #333;
      }

      .login-form label {
         display: block;
         margin-bottom: 10px;
         font-weight: bold;
         color: #555;
      }

      .login-form input[type="email"],
      .login-form input[type="password"] {
         width: 100%;
         padding: 10px;
         margin-bottom: 20px;
         border: 1px solid #ddd;
         border-radius: 5px;
         font-size: 16px;
      }

      .login-form button {
         width: 100%;
         padding: 10px;
         background: #333;
         border: none;
         border-radius: 5px;
         color: #fff;
         font-size: 16px;
         cursor: pointer;
         transition: background 0.3s;
      }

      .login-form button:hover {
         background: #555;
      }

      .login-form .link {
         text-align: center;
         margin-top: 20px;
      }

      .login-form .link a {
         color: #333;
         text-decoration: none;
         font-weight: bold;
      }

      .login-form .link .signup-btn {
         background: #333;
         border: none;
         color: #fff;
         padding: 5px 10px;
         margin-top: 8px;
         font-size: 10px;
         cursor: pointer;
         transition: background 0.3s;
      }

      .login-form .link .signup-btn:hover {
         background: #555;
      }

      /* Error message styling */
      .error-message {
         color: red;
         margin-bottom: 20px;
         text-align: center;
         font-size: 14px;
      }
      
      /* Autofill styles */
      .login-form input:-webkit-autofill {
         background-color: #f0f0f0 !important;
         -webkit-box-shadow: 0 0 0px 1000px #f0f0f0 inset;
         box-shadow: 0 0 0px 1000px #f0f0f0 inset;
         border: 1px solid #ddd;
         border-radius: 5px;
      }

      .login-form input:-webkit-autofill:focus {
         background-color: #fff !important;
         -webkit-box-shadow: 0 0 0px 1000px #fff inset;
         box-shadow: 0 0 0px 1000px #fff inset;
      }
   </style>
</head>
<body>



<div class="login-form">
    <h1>Login</h1>

    

    <form action="login_process.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <?php
 
 if (isset($_SESSION['error_message'])) {
     echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
     unset($_SESSION['error_message']); // Clear the error message after displaying it
 }
 ?>

        <button type="submit">Login</button>
        <div class="link">
            Don't have an account? <a href="signup.php" class="signup-btn">Sign up here</a>
        </div>
    </form>
</div>

<?php include 'component/footer.php'; ?>

</body>
</html>
