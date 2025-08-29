<?php
function addNotification($conn, $user_id, $message) {
    $msg = mysqli_real_escape_string($conn, $message);
    mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ($user_id, '$msg')");
}
?>
