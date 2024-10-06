<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentContent'])) {
    //$ticketId = $_POST['ticketId']; // Ensure this is securely validated
    $content = $_POST['commentContent'];
    $userId = $_SESSION['userId']; // Assuming user session exists
    $commentDate = date('Y-m-d H:i:s');

    // Database insertion logic here
    // Assuming $mysqli is your database connection
    $stmt = $mysqli->prepare("INSERT INTO comments (comment_user_id, comment_ticket_id, commentDate, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $userId, $ticketId, $commentDate, $content);
    $stmt->execute();

    // Redirect back to avoid form resubmission issues
    header('Location: ViewRequisition.php?ticketId=' . $ticketId);
    exit;
}