<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete report if requested
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_report'])) {
    $report_id = $_POST['delete_report'];

    // Delete report from the database
    $sql = "DELETE FROM ReportProperty WHERE report_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $report_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch all reports with user details and property titles
$sql = "SELECT rp.report_id, u.username, p.title AS property_title, rp.report_reason, rp.report_datetime
        FROM ReportProperty rp
        JOIN users u ON rp.user_id = u.id
        JOIN Properties p ON rp.property_id = p.property_id";
$result = mysqli_query($conn, $sql);

$reports = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reports[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Property Reports</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Property</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report) : ?>
                <tr>
                    <td><?php echo $report['report_id']; ?></td>
                    <td><?php echo htmlspecialchars($report['username']); ?></td>
                    <td><?php echo htmlspecialchars($report['property_title']); ?></td>
                    <td><?php echo htmlspecialchars($report['report_reason']); ?></td>
                    <td><?php echo $report['report_datetime']; ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this report?')">
                            <input type="hidden" name="delete_report" value="<?php echo $report['report_id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
