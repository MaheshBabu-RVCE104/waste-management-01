<?php
require_once('../connection.php');

$id = $_GET['i'];
$query = "DELETE FROM garbageinfo WHERE Id = '$id'";

$data = mysqli_query($con, $query);

if($data) {
    echo "<span></span>";
    ?>
    <META HTTP-EQUIV="Refresh" CONTENT="0; URL=http://localhost/waste-management-system-main/adminlogin/welcome.php">
    <?php
} else {
    echo "<font color='red'>Failed to delete!</font>";
}

?>