<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user details from the database
$sql = "SELECT id, username, email, age, bio, expertise FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $username, $email, $age, $bio, $expertise);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST["username"];
    $newEmail = $_POST["email"];
    $newAge = $_POST["age"];
    $newBio = $_POST["bio"];
    $newExpertise = $_POST["expertise"];

    // Update user details in the database
    $update_query = "UPDATE users 
                     SET username = ?, email = ?, age = ?, bio = ?, expertise = ? 
                     WHERE id = ?";
    
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssisi", $newUsername, $newEmail, $newAge, $newBio, $newExpertise, $user_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo "Profile updated successfully!";
        // Update session data if needed
        $_SESSION["username"] = $newUsername;
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
<div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

    <h2>Update Profile</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" class="form-control" name="age" value="<?php echo $age; ?>" required>
        </div>
        <div class="form-group">
            <label for="bio">Bio:</label>
            <textarea class="form-control" name="bio" rows="3"><?php echo $bio; ?></textarea>
        </div>
        <div class="form-group">
            <label for="expertise">Expertise:</label>
            <input type="text" class="form-control" name="expertise" value="<?php echo $expertise; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
