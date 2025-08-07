<?php 
require_once '../controllerUserData.php';
require_once 'admin_functions.php';

$email = $_SESSION['email'];
if(!isset($_SESSION['email'])){
    header('Location: ../login-user.php');
    exit();
}
$sql = "SELECT * FROM usertable WHERE email = '$email'";
$run_Sql = mysqli_query($con, $sql);
if($run_Sql){
    $fetch_info = mysqli_fetch_assoc($run_Sql);
}else{
    header('Location: ../login-user.php');
}

// Check if user is admin
$isAdmin = isAdmin($email);

// Check for success/error messages
if(isset($_GET['success'])) {
    if($_GET['success'] == 1) {
        $msg = '<div class="alert alert-success"><span class="fa fa-check"> Complaint Registered Successfully!</span></div>';
    } elseif($_GET['success'] == 'delete') {
        $msg = '<div class="alert alert-success"><span class="fa fa-check"> Complaint Deleted Successfully!</span></div>';
    } elseif($_GET['success'] == 'update') {
        $msg = '<div class="alert alert-success"><span class="fa fa-check"> Complaint Updated Successfully!</span></div>';
    }
}

if(isset($_GET['error'])) {
    if($_GET['error'] == 'unauthorized') {
        $msg = '<div class="alert alert-danger"><span class="fa fa-times"> Unauthorized access!</span></div>';
    } elseif($_GET['error'] == 'delete_failed') {
        $msg = '<div class="alert alert-danger"><span class="fa fa-times"> Failed to delete complaint!</span></div>';
    } elseif($_GET['error'] == 'update_failed') {
        $msg = '<div class="alert alert-danger"><span class="fa fa-times"> Failed to update complaint!</span></div>';
    }
}
?>
<?php

 require_once "../controllerUserData.php";
 
error_reporting(0);
require_once('../connection.php');
$msg ="";


if(isset($_POST['submit'])){

    $name = mysqli_real_escape_string($con,$_POST['name']);
    $mobile = mysqli_real_escape_string($con,$_POST['mobile']);
    $checkbox1=$_POST['wastetype'];  
    $chk="";  
      foreach($checkbox1 as $chk1)  
             {  
                 $chk .= $chk1.",";  
             } 

    $email = mysqli_real_escape_string($con,$_POST['email']);
	$status = mysqli_real_escape_string($con,$_POST['status']);
    $location = mysqli_real_escape_string($con,$_POST['location']);    
    $locationdescription = mysqli_real_escape_string($con,$_POST['locationdescription']);
	$date = $_POST['date'];
	
	$file = $_FILES['file']['name'];
	$target_dir = "upload/";
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
  
	// Select file type
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  
	// Valid file extensions
	$extensions_arr = array("jpg","jpeg","png","gif","tif", "tiff");
  
	//validate file size 
  //   $filesize = $_FILES["file"]["size"] < 5 * 1024 ;
  
	// Check extension
	if( in_array($imageFileType,$extensions_arr) ){
   
	
	   // Upload file
	   move_uploaded_file($image = $_FILES['file']['tmp_name'],$target_dir.$file);
  
	}

		$sql = "insert into garbageinfo(name,mobile,email,wastetype,location,locationdescription,file,date,status)values('$name','$mobile','$email','$chk','$location','$locationdescription','$file','$date','$status')";
		
    	if(mysqli_query($con,$sql)){
			$msg = '<div class = "alert alert-success"><span class="fa fa-check"> Complaint Registered Successfully!</span></div>';
			header("Location: trash.php?success=1");
			exit();
		}else {
			$msg = '<div class = "alert alert-warning"><span class="fa fa-times"> Failed to Register!</span></div>';
		}
	


     // Database entry successful, no email notification needed

 }
?>

<!DOCTYPE html>
<html>
<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Complain</title>
    <style>
        .bg-dark {
            background-color: #37517e !important;
        }
        .bg-dark a {
            text-decoration: none;
            padding: 10px;
        }
        .bg-dark a:hover {
            color: #47b2e4 !important;
        }
    </style>
</head>
<body>
	    <div class="container-fluid bg-dark text-light py-2">
        <div class="row">
            <div class="col">
                <a href="../index.html" class="text-light"><i class="fa fa-home"></i> Home</a>
            </div>
            <div class="col text-center">
                <?php if($isAdmin): ?>
                    <span class="badge badge-warning"><i class="fa fa-shield"></i> Admin</span>
                <?php endif; ?>
            </div>
            <div class="col text-right">
                Welcome, <?php echo $fetch_info['name']; ?>
                <a href="../logout-user.php" class="text-light ml-3"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </div>
   <?php 
   $error ='';   
   ?>
   <form method="post" action="trash.php" enctype="multipart/form-data">
   <div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="contact-info">
				<img src="images.jfif" alt="image"/>
				<h2>Register Your Complain</h2>
				<h4>We would love to hear from you !</h4>
			</div>
		</div>
		<div class="col-md-9">
			<div class="contact-form">
				<div class="form-group">
				<div id="error"></div>
              <span style="color:red"><?php echo "<b>$msg</b>"?></span>
				  <label class="control-label col-sm-2" for="fname"> Name:</label>
				  <div class="col-sm-10">          
					<input type="text" class="form-control" id="name" placeholder="Enter Your Name" name="name" required>
				  </div>
				</div>
				<div class="form-group">
				  <label class="control-label col-sm-2" for="lname">Mobile:</label>
				  <div class="col-sm-10">          
					<input type="number" class="form-control" id="mobile" placeholder="Enter Your Mobile Number" name="mobile"required min ="80000000" max="100000000000">
				  </div>
				</div>
				<div class="form-group">
				  <!-- <label class="control-label col-sm-2" for="email">Email:</label>
				  <div class="col-sm-10"> -->
					<input type="hidden" class="form-control" id="email" placeholder="Enter Your email" name="email" value="<?php echo   $_SESSION['email'];?>"> 
				  <!-- </div> -->
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="option">Category:</label>
					<div class="col-sm-10">          
					    <input type="checkbox" name="wastetype[]" value="organic"> Organic
                        <input type="checkbox" name="wastetype[]" value="inorganic"> Inorganic
                        <input type="checkbox" name="wastetype[]" value="Household"> Household
                        <input type="checkbox" name="wastetype[]" value="mixed"id="mycheck" checked> All
					</div>
				  </div>
				  <div class="form-group">
					<label class="control-label col-sm-2" for="lname">Location:</label>
					<div class="col-sm-10">          
					   <select class="form-control" id="location" name="location"required>
						   <option class="form-control" >Pattangere</option>
						   <option class="form-control" >Kengeri-Bus-stand</option>
						   <option class="form-control" >Jnanabharathi</option>
						   <option class="form-control" >Rajarajeshwari Nagar</option>
						   
					   </select>
					</div>
				  </div>
				<div class="form-group">
				  
				  <div class="col-sm-10">
					<textarea class="form-control" rows="5" id="locationdescription" placeholder="Enter Location details..." name="locationdescription" required></textarea>
				  </div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="lname">Pictures:</label>
					<div class="col-sm-10">          
					  <input type="file" class="form-control" id="file" name="file"required accept="image/*" capture="camera">
					</div>
				  </div>
				<div class="form-group">        
				  <div class="col-sm-offset-2 col-sm-10">
				   <input type='hidden' class="form-control" id="date" name="status" value="Pending">
				    <input type="hidden" class="form-control" id="date" name="date" value="<?php $timezone = date_default_timezone_set("Asia/Kathmandu");
                                                                                             echo  date("g:ia ,\n l jS F Y");?>">
					<button type="submit" class="btn btn-default" name="submit" >Register</button>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
   </form>
</div>

<!-- Display Complaints Section -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Complaints List</h2>
            <?php if($isAdmin): ?>
                <p class="text-info"><i class="fa fa-info-circle"></i> You have admin privileges. You can edit and delete complaints.</p>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Waste Type</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Status</th>
                            <?php if($isAdmin): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all complaints
                        $query = "SELECT * FROM garbageinfo ORDER BY Id DESC";
                        $result = mysqli_query($con, $query);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['Id'] . "</td>";
                                echo "<td><img src='upload/" . $row['file'] . "' width='100' height='100' class='img-thumbnail'></td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['wastetype']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['locationdescription']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td><span class='badge badge-" . ($row['status'] == 'Completed' ? 'success' : 'warning') . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                                
                                if($isAdmin) {
                                    echo "<td>";
                                    echo "<button class='btn btn-primary btn-sm' onclick='editComplaint(" . $row['Id'] . ")'><i class='fa fa-edit'></i> Edit</button> ";
                                    echo "<button class='btn btn-danger btn-sm' onclick='deleteComplaint(" . $row['Id'] . ")'><i class='fa fa-trash'></i> Delete</button>";
                                    echo "</td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='" . ($isAdmin ? 11 : 10) . "' class='text-center'>No complaints found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Complaint</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editForm" method="post" action="admin_functions.php">
                <div class="modal-body">
                    <input type="hidden" name="complaint_id" id="edit_complaint_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile:</label>
                                <input type="text" class="form-control" name="mobile" id="edit_mobile" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" class="form-control" name="email" id="edit_email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Waste Type:</label>
                                <input type="text" class="form-control" name="wastetype" id="edit_wastetype" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location:</label>
                                <select class="form-control" name="location" id="edit_location" required>
                                    <option value="Pattangere">Pattangere</option>
                                    <option value="Kengeri-Bus-stand">Kengeri-Bus-stand</option>
                                    <option value="Jnanabharathi">Jnanabharathi</option>
                                    <option value="Rajarajeshwari Nagar">Rajarajeshwari Nagar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status:</label>
                                <select class="form-control" name="status" id="edit_status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Location Description:</label>
                        <textarea class="form-control" name="locationdescription" id="edit_locationdescription" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_complaint" class="btn btn-primary">Update Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="formValidation.js"></script>
<script>
function deleteComplaint(id) {
    if(confirm('Are you sure you want to delete this complaint?')) {
        // Create a form and submit it
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'trash.php';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_complaint';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function editComplaint(id) {
    // Fetch complaint data via AJAX
    fetch('get_complaint.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_complaint_id').value = data.Id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_mobile').value = data.mobile;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_wastetype').value = data.wastetype;
            document.getElementById('edit_location').value = data.location;
            document.getElementById('edit_locationdescription').value = data.locationdescription;
            document.getElementById('edit_status').value = data.status;
            $('#editModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading complaint data');
        });
}
</script>
</body>

</html>
