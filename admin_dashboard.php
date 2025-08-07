<?php
session_start();
require_once "connection.php";

// Check if user is logged in and is admin
if(!isset($_SESSION['email'])) {
    header('Location: admin_login.php');
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT role FROM usertable WHERE email = '$email'";
$result = mysqli_query($con, $sql);
if($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if($row['role'] !== 'admin') {
        header('Location: admin_login.php');
        exit();
    }
} else {
    header('Location: admin_login.php');
    exit();
}

// Handle delete request
if(isset($_POST['delete_complaint']) && isset($_SESSION['email'])) {
    $id = mysqli_real_escape_string($con, $_POST['delete_complaint']);
    
    // Get the file name to delete from upload folder
    $sql = "SELECT file FROM garbageinfo WHERE Id = '$id'";
    $result = mysqli_query($con, $sql);
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filename = $row['file'];
        
        // Delete file from upload folder
        $filepath = "phpGmailSMTP/upload/" . $filename;
        if(file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    // Delete from database
    $delete_sql = "DELETE FROM garbageinfo WHERE Id = '$id'";
    if(mysqli_query($con, $delete_sql)) {
        $success_msg = "Complaint deleted successfully!";
    } else {
        $error_msg = "Failed to delete complaint!";
    }
}

// Handle update request
if(isset($_POST['update_complaint']) && isset($_SESSION['email'])) {
    $id = mysqli_real_escape_string($con, $_POST['complaint_id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $wastetype = mysqli_real_escape_string($con, $_POST['wastetype']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $locationdescription = mysqli_real_escape_string($con, $_POST['locationdescription']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    $update_sql = "UPDATE garbageinfo SET 
                   name = '$name', 
                   mobile = '$mobile', 
                   email = '$email', 
                   wastetype = '$wastetype', 
                   location = '$location', 
                   locationdescription = '$locationdescription', 
                   status = '$status' 
                   WHERE Id = '$id'";
    
    if(mysqli_query($con, $update_sql)) {
        $success_msg = "Complaint updated successfully! Status changed to: " . $status;
    } else {
        $error_msg = "Failed to update complaint: " . mysqli_error($con);
    }
}

// Handle quick status change
if(isset($_POST['quick_status_change']) && isset($_SESSION['email'])) {
    $id = mysqli_real_escape_string($con, $_POST['complaint_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['new_status']);
    
    $status_sql = "UPDATE garbageinfo SET status = '$new_status' WHERE Id = '$id'";
    
    if(mysqli_query($con, $status_sql)) {
        $success_msg = "Status updated to: " . $new_status;
    } else {
        $error_msg = "Failed to update status: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Waste Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
        }
        .admin-badge {
            background-color: #ff6b35;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
        }
        .complaint-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status-pending { background-color: #fff3cd; }
        .status-progress { background-color: #d1ecf1; }
        .status-completed { background-color: #d4edda; }
        .action-buttons .btn {
            margin: 2px;
        }
        .status-cell {
            min-width: 150px;
        }
        .quick-status-select {
            font-size: 11px;
            padding: 2px 4px;
        }
        .admin-controls {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #343a40;
            color: white;
            border-color: #454d55;
        }
        .status-pending { background-color: #fff3cd; }
        .status-progress { background-color: #d1ecf1; }
        .status-completed { background-color: #d4edda; }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2><i class="fas fa-shield-alt"></i> Admin Dashboard</h2>
                    <p class="mb-0">Waste Management System - Complaints Preview</p>
                </div>
                <div class="col-md-6 text-right">
                    <span class="admin-badge"><i class="fas fa-user-shield"></i> Admin Access</span>
                    <p class="mb-0 mt-2">Welcome, <?php echo $_SESSION['name']; ?></p>
                    <a href="logout-user.php" class="btn btn-outline-light btn-sm mt-2">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Success/Error Messages -->
        <?php if(isset($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h4>
                            <?php
                            $total = mysqli_query($con, "SELECT COUNT(*) as count FROM garbageinfo");
                            $total_count = mysqli_fetch_assoc($total)['count'];
                            echo $total_count;
                            ?>
                        </h4>
                        <p class="mb-0">Total Complaints</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h4>
                            <?php
                            $pending = mysqli_query($con, "SELECT COUNT(*) as count FROM garbageinfo WHERE status = 'Pending'");
                            $pending_count = mysqli_fetch_assoc($pending)['count'];
                            echo $pending_count;
                            ?>
                        </h4>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h4>
                            <?php
                            $progress = mysqli_query($con, "SELECT COUNT(*) as count FROM garbageinfo WHERE status = 'In Progress'");
                            $progress_count = mysqli_fetch_assoc($progress)['count'];
                            echo $progress_count;
                            ?>
                        </h4>
                        <p class="mb-0">In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h4>
                            <?php
                            $completed = mysqli_query($con, "SELECT COUNT(*) as count FROM garbageinfo WHERE status = 'Completed'");
                            $completed_count = mysqli_fetch_assoc($completed)['count'];
                            echo $completed_count;
                            ?>
                        </h4>
                        <p class="mb-0">Completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Controls Info -->
        <div class="admin-controls">
            <h5><i class="fas fa-cogs"></i> Admin Controls</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit text-primary mr-2"></i>
                        <span><strong>Edit:</strong> Click the blue edit button to modify complaint details</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-trash text-danger mr-2"></i>
                        <span><strong>Delete:</strong> Click the red delete button to remove complaints</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exchange-alt text-success mr-2"></i>
                        <span><strong>Status:</strong> Use dropdown to quickly change complaint status</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaints List -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list"></i> All Complaints - WMS Database</h4>
                <p class="mb-0 text-muted">Manage complaints with full admin privileges</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM garbageinfo ORDER BY Id DESC";
                            $result = mysqli_query($con, $query);
                            
                            if(mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $status_class = '';
                                    switch($row['status']) {
                                        case 'Pending': $status_class = 'status-pending'; break;
                                        case 'In Progress': $status_class = 'status-progress'; break;
                                        case 'Completed': $status_class = 'status-completed'; break;
                                    }
                                    
                                    echo "<tr class='$status_class'>";
                                    echo "<td>" . $row['Id'] . "</td>";
                                    echo "<td><img src='phpGmailSMTP/upload/" . $row['file'] . "' width='80' height='80' class='img-thumbnail'></td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['wastetype']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                    echo "<td>" . htmlspecialchars(substr($row['locationdescription'], 0, 50)) . "...</td>";
                                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                    echo "<td>";
                                    echo "<span class='badge badge-" . ($row['status'] == 'Completed' ? 'success' : ($row['status'] == 'In Progress' ? 'info' : 'warning')) . "'>" . htmlspecialchars($row['status']) . "</span>";
                                    echo "<br><small class='text-muted'>Quick Change:</small>";
                                    echo "<select class='form-control form-control-sm mt-1' onchange='quickStatusChange(" . $row['Id'] . ", this.value)'>";
                                    echo "<option value=''>Select Status</option>";
                                    echo "<option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                                    echo "<option value='In Progress'" . ($row['status'] == 'In Progress' ? ' selected' : '') . ">In Progress</option>";
                                    echo "<option value='Completed'" . ($row['status'] == 'Completed' ? ' selected' : '') . ">Completed</option>";
                                    echo "</select>";
                                    echo "</td>";
                                    echo "<td class='action-buttons'>";
                                    echo "<button class='btn btn-primary btn-sm' onclick='editComplaint(" . $row['Id'] . ")' title='Edit Complaint'><i class='fas fa-edit'></i></button> ";
                                    echo "<button class='btn btn-danger btn-sm' onclick='deleteComplaint(" . $row['Id'] . ")' title='Delete Complaint'><i class='fas fa-trash'></i></button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11' class='text-center'>No complaints found in WMS database</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Complaint</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="post">
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
                                        <option value="Jnanabharathi">Jnanabhari</option>
                                        <option value="Rajarajeshwari Nagar">Rajarajeshwari Nagar</option>
                                        <option value="Ktm">Ktm</option>
                                        <option value="Bktpur">Bktpur</option>
                                        <option value="lalitpur">lalitpur</option>
                                        <option value="sanepa">sanepa</option>
                                        <option value="Kalanki">Kalanki</option>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    function deleteComplaint(id) {
        if(confirm('Are you sure you want to delete this complaint?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin_dashboard.php';
            
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
        fetch('phpGmailSMTP/get_complaint.php?id=' + id)
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

    function quickStatusChange(id, newStatus) {
        if(newStatus === '') return;
        
        if(confirm('Are you sure you want to change the status to "' + newStatus + '"?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin_dashboard.php';
            
            var input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'quick_status_change';
            input1.value = '1';
            
            var input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'complaint_id';
            input2.value = id;
            
            var input3 = document.createElement('input');
            input3.type = 'hidden';
            input3.name = 'new_status';
            input3.value = newStatus;
            
            form.appendChild(input1);
            form.appendChild(input2);
            form.appendChild(input3);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>
