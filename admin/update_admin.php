<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$admin_username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST["new_username"];
    $new_password = $_POST["new_password"];

    $sql_update = "UPDATE admins SET username = ?, password = ? WHERE username = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sss", $new_username, $new_password, $admin_username);
    $stmt_update->execute();
    $stmt_update->close();
    $success_message = "Profile updated successfully!";
    $_SESSION["username"] = $new_username; // Update the session username
}

$sql_fetch_admin = "SELECT username FROM admins WHERE username = ?";
$stmt_fetch_admin = $conn->prepare($sql_fetch_admin);
$stmt_fetch_admin->bind_param("s", $admin_username);
$stmt_fetch_admin->execute();
$result_fetch_admin = $stmt_fetch_admin->get_result();

if ($result_fetch_admin->num_rows == 1) {
    $row_admin = $result_fetch_admin->fetch_assoc();
    $current_username = $row_admin["username"];
}

$stmt_fetch_admin->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
<div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

    <h2>Admin Profile</h2>
    <?php if (isset($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>
    <form method="post">
        <div class="form-group">
            <label for="new_username">New Username:</label>
            <input type="text" class="form-control" id="new_username" name="new_username" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
