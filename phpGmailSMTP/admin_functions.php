<?php
// Session is already started in the calling file
require_once '../connection.php';

// Function to check if user is admin
function isAdmin($email) {
    global $con;
    $email = mysqli_real_escape_string($con, $email);
    $sql = "SELECT role FROM usertable WHERE email = '$email'";
    $result = mysqli_query($con, $sql);
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['role'] === 'admin';
    }
    return false;
}

// Function to delete complaint (admin only)
function deleteComplaint($id) {
    global $con;
    
    // Get the file name to delete from upload folder
    $sql = "SELECT file FROM garbageinfo WHERE Id = '$id'";
    $result = mysqli_query($con, $sql);
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filename = $row['file'];
        
        // Delete file from upload folder
        $filepath = "upload/" . $filename;
        if(file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    // Delete from database
    $delete_sql = "DELETE FROM garbageinfo WHERE Id = '$id'";
    return mysqli_query($con, $delete_sql);
}

// Function to update complaint (admin only)
function updateComplaint($id, $name, $mobile, $email, $wastetype, $location, $locationdescription, $status) {
    global $con;
    
    $update_sql = "UPDATE garbageinfo SET 
                   name = '$name', 
                   mobile = '$mobile', 
                   email = '$email', 
                   wastetype = '$wastetype', 
                   location = '$location', 
                   locationdescription = '$locationdescription', 
                   status = '$status' 
                   WHERE Id = '$id'";
    
    return mysqli_query($con, $update_sql);
}

// Handle delete request
if(isset($_GET['delete']) && isset($_SESSION['email'])) {
    if(isAdmin($_SESSION['email'])) {
        $id = mysqli_real_escape_string($con, $_GET['delete']);
        if(deleteComplaint($id)) {
            header('Location: trash.php?success=delete');
            exit();
        } else {
            header('Location: trash.php?error=delete_failed');
            exit();
        }
    } else {
        header('Location: trash.php?error=unauthorized');
        exit();
    }
}

// Handle update request
if(isset($_POST['update_complaint']) && isset($_SESSION['email'])) {
    if(isAdmin($_SESSION['email'])) {
        $id = mysqli_real_escape_string($con, $_POST['complaint_id']);
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $wastetype = mysqli_real_escape_string($con, $_POST['wastetype']);
        $location = mysqli_real_escape_string($con, $_POST['location']);
        $locationdescription = mysqli_real_escape_string($con, $_POST['locationdescription']);
        $status = mysqli_real_escape_string($con, $_POST['status']);
        
        if(updateComplaint($id, $name, $mobile, $email, $wastetype, $location, $locationdescription, $status)) {
            header('Location: trash.php?success=update');
            exit();
        } else {
            header('Location: trash.php?error=update_failed');
            exit();
        }
    } else {
        header('Location: trash.php?error=unauthorized');
        exit();
    }
}
?>
