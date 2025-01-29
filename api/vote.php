<?php
session_start();
include("connect.php");

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] != '1' || $_SESSION['user_status'] == 1) {
    header("Location: ../");
    exit();
}

if (!isset($_GET['group_id'])) {
    header("Location: ../routes/dashboard.php");
    exit();
}

$groupId = $_GET['group_id'];
$voterId = $_SESSION['user_id'];

// Update group votes
mysqli_query($connect, "UPDATE user SET votes = votes + 1 WHERE id='$groupId'");

// Update voter status
mysqli_query($connect, "UPDATE user SET status = 1 WHERE id='$voterId'");

$_SESSION['user_status'] = 1;
header("Location: ../routes/dashboard.php");
?>