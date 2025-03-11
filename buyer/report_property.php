<?php
session_start();

include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

// Check if property_id is provided in the URL
if (!isset($_GET['property_id'])) {
    header("location: dashboard.php");
    exit;
}

$property_id = $_GET['property_id'];

// Fetch property details
$sql = "SELECT * FROM Properties WHERE property_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $property = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle report submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["id"];
    $report_reason = $_POST['report_reason'];

    $sql = "INSERT INTO ReportProperty (property_id, user_id, report_reason) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "iis", $property_id, $user_id, $report_reason);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success_message = "Property reported successfully!";
    } else {
        $error_message = "Error: Unable to report property.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Property</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5 mb-5">
    <h2>Report Property</h2>
    <?php if (isset($success_message)) : ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <div class="card">
        <img src="../seller/<?php echo htmlspecialchars($property['image']); ?>" class="card-img-top" alt="Property Image">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($property['description']); ?></p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Location: <?php echo htmlspecialchars($property['location']); ?></li>
                <li class="list-group-item">Price: <?php echo htmlspecialchars($property['price']); ?></li>
                <li class="list-group-item">Type: <?php echo htmlspecialchars($property['property_type']); ?></li>
                <li class="list-group-item">Amenities: <?php echo htmlspecialchars($property['amenities']); ?></li>
            </ul>
        </div>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?property_id=<?php echo $property_id; ?>" method="post" class="mt-3">
        <div class="form-group">
            <label for="report_reason">Reason for reporting:</label>
            <textarea class="form-control" id="report_reason" name="report_reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Submit Report</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
