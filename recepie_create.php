<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Recipe</title>
</head>
<body>

<div class="container">
    <h2 class="my-4">Add Recipe</h2>

    <?php
    session_start();
    

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to the login page if not logged in
        exit();
    }

    // Assuming you have a database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "karizma";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get values from the form
        $recipeName = $_POST['recipeName'];
        $recipeDuration = $_POST['recipeDuration'];
        $recipeImage = $_FILES['recipeImage']['name'];
        $steps = $_POST['steps'];
        $ingredients = $_POST['ingredients'];

        // Insert into recipes table
        $sql = "INSERT INTO recipes (recipe_name, recipe_duration, recipe_image, steps) 
                VALUES ('$recipeName', $recipeDuration, '$recipeImage', '$steps')";

        if ($conn->query($sql) === TRUE) {
            $recipeId = $conn->insert_id;

            // Save image to project folder
            $targetDir = "./assets/img/recepies/"; // Adjust the path as needed
            $targetFile = $targetDir . basename($recipeImage);

            move_uploaded_file($_FILES["recipeImage"]["tmp_name"], $targetFile);

            // Insert into ingredients table
            foreach ($ingredients as $ingredient) {
                $ingredientName = $ingredient;
                $sql = "INSERT INTO ingredients (recipe_id, ingredient_name) 
                        VALUES ($recipeId, '$ingredientName')";
                $conn->query($sql);
            ?>

            <div class="alert alert-success" role="alert">
                Recipe added successfully!
            </div>

            <?php
            } // End foreach

            // Redirect to recipe list
            header("Location: recepies_list.php");
            exit;
        } else {
            ?>

            <div class="alert alert-danger" role="alert">
                Error: <?php echo $sql . "<br>" . $conn->error; ?>
            </div>

            <?php
        }
    }

    $conn->close();
    ?>


    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="recipeName" class="form-label">Recipe Name</label>
            <input type="text" class="form-control" id="recipeName" name="recipeName" required>
        </div>
        <div class="mb-3">
            <label for="recipeDuration" class="form-label">Recipe Duration (in minutes)</label>
            <input type="number" class="form-control" id="recipeDuration" name="recipeDuration" required>
        </div>
        <div class="mb-3">
            <label for="recipeImage" class="form-label">Recipe Image</label>
            <input type="file" class="form-control" id="recipeImage" name="recipeImage" accept="image/*">
        </div>
        <div id="ingredientInputs">
            <div class="mb-3">
                <label for="ingredient1" class="form-label">Ingredient 1</label>
                <input type="text" class="form-control" id="ingredient1" name="ingredients[]" required>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addIngredientInput()">Add Ingredient</button>
        <div class="mb-3">
            <label for="steps" class="form-label">Steps to Prepare</label>
            <textarea class="form-control" id="steps" name="steps" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Recipe</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let ingredientCount = 1;

    function addIngredientInput() {
        ingredientCount++;
        const ingredientInputs = document.getElementById("ingredientInputs");
        const newIngredientInput = document.createElement("div");
        newIngredientInput.classList.add("mb-3");
        newIngredientInput.innerHTML = `
            <label for="ingredient${ingredientCount}" class="form-label">Ingredient ${ingredientCount}</label>
            <input type="text" class="form-control" id="ingredient${ingredientCount}" name="ingredients[]" required>
        `;
        ingredientInputs.appendChild(newIngredientInput);
    }
</script>

</body>
</html>
