<?php
session_start();
global $pass ;
global $conn;
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Define the regex pattern for the password
    $password_regex = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*(),.?\":{}|<>]{8,}$/";
    $pass = "Password must be at least 8 characters long and include at least one letter and one number." ;

    // Validate the password using regex before proceeding
    if (!preg_match($password_regex, $password)) {
    } else {
        // Proceed with querying the database if password format is correct
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email']; // Store email in the session
                header("Location: index.php");
                exit;
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "No user found with this email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: .25rem;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
        }
        form {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            margin-top: 10px;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Login</h1>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" class="form-control" required><br>
        <input type="password" name="password" placeholder="Password" class="form-control"  required >  <?php echo $pass ?> <br>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <a href="register.php">Don't have an account? Register</a>
</div>

</body>
</html>
