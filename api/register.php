<?php
include("connect.php");
error_reporting(E_ALL);
ini_set('display_errors', '1');

$name = $_POST['name'];
$mobile = $_POST['mobile'];
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];
$address = $_POST['address'];
$role = $_POST['role'];

$image = $_FILES['image']['name'];  
$tmp_name = $_FILES['image']['tmp_name'];  

if ($password == $cpassword) {
    $upload_path = __DIR__.'\\uploads\\' . basename($image);
    echo ($upload_path);
    echo ($_FILES);
    foreach($_FILES as $result) {
        echo $result, '<br>';
    }

    
    if (move_uploaded_file($tmp_name, $upload_path)) {
        
        $stmt = $connect->prepare("INSERT INTO user (name, mobile, password, address, image, role, status, votes) VALUES ('$name', '$mobile', '$password', '$addres','$role','$status', 0, 0)");
        $stmt->bind_param("ssssss", $name, $mobile, $password, $address, $image, $role);
        
        if ($stmt->execute()) {
            echo '<script>
                    alert("Registration Successful!");
                    window.location="../";
                  </script>';
        } else {
            echo '<script>
                    alert("Some error occurred!");
                    window.location="../";
                  </script>';
        }
        $stmt->close();
    } else {
        // echo '<script>
        //         alert("File upload failed!");
        //         window.location="../";
        //       </script>';
        echo $_FILES['image']['error'];
    }
} else {
    echo '<script>
            alert("Invalid password!!");
            window.location="../routes/register.html";
          </script>';
}
?>
