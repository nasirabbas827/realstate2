<?php 
session_start();

// Check if the user is logged in and is a Seller
if (!isset($_SESSION["id"]) || $_SESSION["user_type"] != "Seller-Agent") {
    header("location: login.php");
    exit;
}

include('config.php');

// Fetch total properties count
$sql = "SELECT COUNT(*) AS total_properties FROM Properties WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $total_properties = $row['total_properties'];
    mysqli_stmt_close($stmt);
}

// Fetch total sold properties count
$sql = "SELECT COUNT(*) AS total_sold FROM Properties WHERE user_id = ? AND status = 'Sold'";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $total_sold = $row['total_sold'];
    mysqli_stmt_close($stmt);
} else {
    $total_sold = 0;
}

// Fetch payment details of sold properties
$sql = "SELECT p.property_id, p.title, py.amount, py.transaction_id, py.payment_status, py.payment_date 
        FROM Payments py 
        JOIN Properties p ON py.property_id = p.property_id 
        WHERE p.user_id = ? 
        ORDER BY py.payment_date DESC";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $payments = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Fetch latest sold property notification
$sql = "SELECT p.title, py.amount, py.payment_date 
        FROM Payments py 
        JOIN Properties p ON py.property_id = p.property_id 
        WHERE p.user_id = ? 
        ORDER BY py.payment_date DESC LIMIT 1";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $latest_sold = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Fetch messages for the seller
$sql = "SELECT m.*, u.email AS sender_email FROM messages m 
        JOIN users u ON m.sender_id = u.id 
        WHERE receiver_id = ? ORDER BY sent_datetime DESC";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

// Handle reply to messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_text'])) {
    $reply_text = $_POST['reply_text'];
    $message_id = $_POST['message_id'];

    $sql = "UPDATE messages SET reply_text = ?, reply_datetime = NOW() WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $reply_text, $message_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Redirect to avoid resubmission on refresh
    header("location: seller_dashboard.php");
    exit;
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5 mb-5">
        <h2>Welcome to the Seller Dashboard, <?php echo htmlspecialchars($_SESSION["email"]); ?>!</h2>

        <h3 class="mt-4">Your Total Added Properties: 
            <span class="badge badge-info"><?php echo $total_properties; ?></span>
        </h3>
        
        <h3 class="mt-4">Total Sold Properties: 
            <span class="badge badge-success"><?php echo $total_sold; ?></span>
        </h3>

        <?php if ($latest_sold) : ?>
            <div class="alert alert-warning mt-4">
                <strong>Notification:</strong> Your latest property "<b><?php echo htmlspecialchars($latest_sold['title']); ?></b>" 
                was sold for <b>$<?php echo number_format($latest_sold['amount'], 2); ?></b> on 
                <b><?php echo date("F j, Y, g:i A", strtotime($latest_sold['payment_date'])); ?></b>.
            </div>
        <?php endif; ?>

        <h3 class="mt-4">Payment Details</h3>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Property ID</th>
                    <th>Property Title</th>
                    <th>Amount</th>
                    <th>Transaction ID</th>
                    <th>Payment Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment) : ?>
                    <tr>
                        <td><?php echo $payment['property_id']; ?></td>
                        <td><?php echo htmlspecialchars($payment['title']); ?></td>
                        <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_status']); ?></td>
                        <td><?php echo date("F j, Y, g:i A", strtotime($payment['payment_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="mt-4">Messages</h3>
        <div class="list-group">
            <?php foreach ($messages as $message) : ?>
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">From: <?php echo htmlspecialchars($message['sender_email']); ?></h5>
                        <small><?php echo htmlspecialchars($message['sent_datetime']); ?></small>
                    </div>
                    <p class="mb-1"><?php echo htmlspecialchars($message['message_text']); ?></p>
                    <?php if (empty($message['reply_text'])) : ?>
                        <form action="seller_dashboard.php" method="post">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <div class="form-group">
                                <label for="reply_text">Reply:</label>
                                <textarea class="form-control" id="reply_text" name="reply_text" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    <?php else : ?>
                        <div class="alert alert-info mt-3" role="alert">
                            <strong>Reply:</strong><br>
                            <?php echo htmlspecialchars($message['reply_text']); ?>
                            <br>
                            <small><?php echo htmlspecialchars($message['reply_datetime']); ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
