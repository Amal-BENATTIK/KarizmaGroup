<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "karizma";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Fetch hashed password from the database
    $sql = "SELECT mot_de_pass FROM utilisateur WHERE nom_utilisateur = '$username'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row["mot_de_pass"];

            // Verify the password using password_verify
            if (md5($password) === $hashedPassword)  {
                header("Location: recepies.php");
                $_SESSION['user_id']=$username;
                exit; // Ensure that the script stops after redirecting
            } else {
                echo "Invalid password";
            }
        } else {
            echo "User not found";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>



    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <title>Login Page</title>
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                background-color: #f8f9fa; 
            }

            .login-form {
                width: 30%;
                background-color: #ffffff; 
                padding: 50px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
                margin: 0 auto; 
                margin-top: 50px;
            }

            .login-form h2 {
                color: #343a40; 
                text-align: center;
                margin-bottom: 30px;
            }

            .login-form label {
                color: #495057; 
            }

            .login-form input {
                margin-bottom: 20px;
            }

            .login-form button {
                background-color: #007bff; 
                color: #ffffff; 
            }

            .login-form button:hover {
                background-color: #0056b3; 
            }
        </style>
    </head>
    <body>

    <div class="login-form">
        <h2>Login</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
