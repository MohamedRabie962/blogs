<?php
global $pass ;
global $conn;
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];

    // Define the regex pattern for the password
    $password_regex = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*(),.?\":{}|<>]{8,}$/";
    $pass = "Password must be at least 8 characters long and include at least one letter and one number." ;
    // Validate the password using regex before proceeding
    if (!preg_match($password_regex, $password)) {
    } else {
        // Hash the password and proceed with the database insert
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
    <a href="posts.php" class="btn btn-info">All Posts</a> <!-- Add this line to create the button -->

    <h1>Register</h1>

    <form method="POST" action="register.php">
        <input type="text" name="name" placeholder="Username" class="form-control" required><br>
        <input type="email" name="email" placeholder="Email" class="form-control" required><br>
        <input type="text" name="phone" placeholder="Phone" class="form-control" required><br>
        <input type="password" name="password" placeholder="Password" class="form-control" required > <?php echo $pass  ?> <br>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <a href="login.php">Already have an account? Login</a>
</div>

</body>
</html>
