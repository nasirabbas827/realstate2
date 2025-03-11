<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

include('config.php');

$property_id = isset($_GET['property_id']) ? $_GET['property_id'] : null;
$seller_id = null;

// Fetch seller ID from the property ID
if ($property_id) {
    $sql = "SELECT user_id FROM Properties WHERE property_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $property_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $seller_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // Fetch seller details
    if ($seller_id) {
        $sql = "SELECT email, phone, bio, expertise FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $seller_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $seller_email, $seller_phone, $seller_bio, $seller_expertise);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['message_text'])) { // Send new message
        $message_text = $_POST['message_text'];
        $sender_id = $_SESSION["id"];
        $receiver_id = $seller_id;

        $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iis", $sender_id, $receiver_id, $message_text);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } elseif (isset($_POST['reply_text'])) { // Reply to existing message
        $reply_text = $_POST['reply_text'];
        $message_id = $_POST['message_id'];

        $sql = "UPDATE messages SET reply_text = ?, reply_datetime = NOW() WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $reply_text, $message_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("location: contact_seller.php?property_id=$property_id");
    exit;
}

// Fetch messages between buyer and seller
$messages = [];
if ($seller_id) {
    $sql = "SELECT m.*, u.email AS sender_email FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $user_id = $_SESSION["id"];
        mysqli_stmt_bind_param($stmt, "iiii", $user_id, $seller_id, $seller_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Seller</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
    <div class="row">
                <div class="col-md-6">
                    <h4>Seller Information</h4>
                    <ul>
                        <li><strong>Email:</strong> <?php echo htmlspecialchars($seller_email); ?></li>
                        <li><strong>Phone:</strong> <?php echo htmlspecialchars($seller_phone); ?></li>
                        <li><strong>Expertise:</strong> <?php echo htmlspecialchars($seller_expertise); ?></li>
                        <li><strong>Bio:</strong> <?php echo htmlspecialchars($seller_bio); ?></li>
                    </ul>
                </div>
                <div class="col-md-6">
        <h2>Contact Seller</h2>
        <?php if ($seller_id) : ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?property_id=' . $property_id); ?>" method="post">
                <div class="form-group">
                    <label for="message_text">Message</label>
                    <textarea class="form-control" id="message_text" name="message_text" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>

            <hr>

            <h3>Message History</h3>
            <?php foreach ($messages as $message) : ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><strong>From:</strong> <?php echo htmlspecialchars($message['sender_email']); ?></p>
                        <p><strong>Sent at:</strong> <?php echo htmlspecialchars($message['sent_datetime']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($message['message_text'])); ?></p>
                        <?php if (!empty($message['reply_text'])) : ?>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <p><strong>Reply:</strong></p>
                                    <p><strong>Sent at:</strong> <?php echo htmlspecialchars($message['reply_datetime']); ?></p>
                                    <p><?php echo nl2br(htmlspecialchars($message['reply_text'])); ?></p>
                                </div>
                            </div>
                        <?php elseif ($_SESSION["id"] == $seller_id) : ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?property_id=' . $property_id); ?>" method="post">
                                <div class="form-group">
                                    <label for="reply_text">Reply</label>
                                    <textarea class="form-control" id="reply_text" name="reply_text" rows="3" required></textarea>
                                </div>
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" class="btn btn-success">Send Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-danger">Invalid property ID.</p>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
