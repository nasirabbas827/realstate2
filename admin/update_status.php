<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $userId = intval($_POST['id']);
    $query = "UPDATE users SET status = 'Approved' WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "error";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "error";
    }
    mysqli_close($conn);
}
?>
