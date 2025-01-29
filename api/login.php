<?php
session_start();
include("connect.php");

if (!isset($_POST['mobile']) || !isset($_POST['password']) || !isset($_POST['role'])) {
    echo '<script>
            alert("All fields are required!");
            window.location="../";
          </script>';
    exit();
}

$mobile = mysqli_real_escape_string($connect, $_POST['mobile']);
$password = $_POST['password'];
$role = mysqli_real_escape_string($connect, $_POST['role']);

// Get user from database
$sql = "SELECT * FROM user WHERE mobile = ? AND role = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("ss", $mobile, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_mobile'] = $user['mobile'];
        $_SESSION['user_address'] = $user['address'];
        $_SESSION['user_image'] = $user['image'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_status'] = $user['status'];
        $_SESSION['logged_in'] = true;
        
        header("Location: ../routes/dashboard.php");
    } else {
        echo '<script>
                alert("Invalid credentials!");
                window.location="../";
              </script>';
    }
} else {
    echo '<script>
            alert("Invalid credentials!");
            window.location="../";
          </script>';
}

$stmt->close();
?>