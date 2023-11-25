<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}
$mysqli = new mysqli("localhost", "root", "", "karizma");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>User List</title>
    <style>
        body {
            padding: 20px;
        }
        .user-table {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4">User List</h2>

    <div class="user-table">
        <h4>User List</h4>
        <button type="button" class="btn btn-success" onclick="redirectToCreateUser()">Create User</button>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Function to get user data from the database
            function getUsers($conn) {
                $sql = "SELECT id_utilisateur, nom_utilisateur FROM utilisateur";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id_utilisateur"] . "</td>";
                        echo "<td>" . $row["nom_utilisateur"] . "</td>";
                        echo "<td>";
                        echo "<button onclick='redirectToUpdateUser(" . $row["id_utilisateur"] . ")' class='btn btn-info btn-sm'>Edit</button>";
                        echo "<button onclick='confirmDeleteUser(" . $row["id_utilisateur"] . ")' class='btn btn-danger btn-sm'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No users found</td></tr>";
                }
            }

            // Call the function and pass the database connection
            getUsers($mysqli);
            ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function redirectToCreateUser() {
        window.location.href = "user_create.php";
    }

    function redirectToUpdateUser(userId) {
        window.location.href = "user_update.php?id=" + userId;
    }

    function confirmDeleteUser(userId) {
        var result = confirm("Are you sure you want to delete this user?");
        if (result) {
            // Redirect to the code for deleting the user
            window.location.href = "user_delete.php?id=" + userId;
        }
    }
</script>
</body>
</html>
