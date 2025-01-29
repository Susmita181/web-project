<?php
include("connect.php");
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Create uploads directory if it doesn't exist
$upload_dir = __DIR__.'/uploads';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        echo '<script>
                alert("Failed to create uploads directory. Please contact administrator.");
                window.location="../routes/register.html";
              </script>';
        exit();
    }
    chmod($upload_dir, 0777); // For development only
}

// Validate form data
if (!isset($_POST['name']) || !isset($_POST['mobile']) || !isset($_POST['password']) || 
    !isset($_POST['cpassword']) || !isset($_POST['address']) || !isset($_POST['role'])) {
    echo '<script>
            alert("All fields are required!");
            window.location="../routes/register.html";
          </script>';
    exit();
}

$name = mysqli_real_escape_string($connect, $_POST['name']);
$mobile = mysqli_real_escape_string($connect, $_POST['mobile']);
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];
$address = mysqli_real_escape_string($connect, $_POST['address']);
$role = mysqli_real_escape_string($connect, $_POST['role']);

// Validate mobile number
if (!preg_match("/^[0-9]{10,11}$/", $mobile)) {
    echo '<script>
            alert("Invalid mobile number format!");
            window.location="../routes/register.html";
          </script>';
    exit();
}

// Check if mobile number already exists
$check_mobile = mysqli_query($connect, "SELECT mobile FROM user WHERE mobile='$mobile'");
if (mysqli_num_rows($check_mobile) > 0) {
    echo '<script>
            alert("Mobile number already registered!");
            window.location="../routes/register.html";
          </script>';
    exit();
}

// Validate passwords
if ($password != $cpassword) {
    echo '<script>
            alert("Passwords do not match!");
            window.location="../routes/register.html";
          </script>';
    exit();
}

// Handle image upload
if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    echo '<script>
            alert("Please select an image!");
            window.location="../routes/register.html";
          </script>';
    exit();
}

$image = $_FILES['image']['name'];
$tmp_name = $_FILES['image']['tmp_name'];

// Validate image file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $tmp_name);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo '<script>
            alert("Only JPG, PNG & GIF images are allowed!");
            window.location="../routes/register.html";
          </script>';
    exit();
}

// Generate unique filename
$file_extension = pathinfo($image, PATHINFO_EXTENSION);
$unique_image_name = uniqid() . '.' . $file_extension;
$upload_path = $upload_dir . '/' . $unique_image_name;

if (move_uploaded_file($tmp_name, $upload_path)) {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user data
    $stmt = $connect->prepare("INSERT INTO user (name, mobile, password, address, image, role, status, votes) VALUES (?, ?, ?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("ssssss", $name, $mobile, $hashed_password, $address, $unique_image_name, $role);
    
    if ($stmt->execute()) {
        echo '<script>
                alert("Registration Successful!");
                window.location="../";
              </script>';
    } else {
        unlink($upload_path); // Delete uploaded image if registration fails
        echo '<script>
                alert("Registration failed: ' . $stmt->error . '");
                window.location="../routes/register.html";
              </script>';
    }
    $stmt->close();
} else {
    echo '<script>
            alert("Failed to upload image!");
            window.location="../routes/register.html";
          </script>';
}
?>