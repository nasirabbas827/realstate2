<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalQueriesForReply = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM messages WHERE reply_text IS NULL"))['total'];
$totalProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM Properties"))['total'];
$totalReports = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ReportProperty"))['total'];

// Fetch pending users
$pendingUsersQuery = "SELECT id, email, user_type, status FROM users WHERE status = 'Pending'";
$pendingUsersResult = mysqli_query($conn, $pendingUsersQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <div class="row mt-4">
        <div class="col-md-3"><div class="card"><div class="card-body"><h5 class="card-title">Total Users</h5><p class="card-text"><?php echo $totalUsers; ?></p></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><h5 class="card-title">Total Queries</h5><p class="card-text"><?php echo $totalQueriesForReply; ?></p></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><h5 class="card-title">Total Properties</h5><p class="card-text"><?php echo $totalProperties; ?></p></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><h5 class="card-title">Total Reports</h5><p class="card-text"><?php echo $totalReports; ?></p></div></div></div>
    </div>

    <!-- Pending Users Section -->
    <div class="mt-5">
        <h3>Pending Users</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($pendingUsersResult)) { ?>
                    <tr id="user_<?php echo $row['id']; ?>">
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['user_type']; ?></td>
                        <td><span class="badge badge-warning"><?php echo $row['status']; ?></span></td>
                        <td>
                            <button class="btn btn-success btn-sm approve-btn" data-id="<?php echo $row['id']; ?>">Approve</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function(){
    $(".approve-btn").click(function(){
        var userId = $(this).data("id");
        $.ajax({
            url: "update_status.php",
            type: "POST",
            data: { id: userId },
            success: function(response){
                if (response == "success") {
                    $("#user_" + userId).fadeOut("slow");
                } else {
                    alert("Error updating status. Try again.");
                }
            }
        });
    });
});
</script>

</body>
</html>
