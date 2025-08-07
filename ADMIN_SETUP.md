# Admin Setup and Usage Guide

## Overview
This waste management system now includes admin privileges that allow administrators to edit and delete complaints. Regular users can only view and submit complaints.

## Setup Instructions

### 1. Database Setup
First, run the updated database schema:
```sql
-- Run the database.sql file to create/update the database structure
-- This will add the 'role' column to the usertable
```

### 2. Create Admin User
Run the `create_admin.php` script in your browser to create the default admin account:
- Email: admin@wms.com
- Password: admin123

**Important**: Change the password after first login!

### 3. Alternative: Manual Admin Creation
You can also manually create an admin user by inserting into the database:
```sql
INSERT INTO usertable (name, email, password, role) 
VALUES ('Admin Name', 'admin@example.com', '$2y$10$...', 'admin');
```

## Admin Features

### Login as Admin
1. Go to the login page
2. Use admin credentials (admin@wms.com / admin123)
3. You'll see an "Admin" badge in the navigation

### Admin Privileges
- **View all complaints** in a table format
- **Edit complaints**: Click the "Edit" button to modify complaint details
- **Delete complaints**: Click the "Delete" button to remove complaints
- **Update status**: Change complaint status (Pending, In Progress, Completed)

### Regular User Limitations
- Can only view complaints in read-only format
- Cannot edit or delete complaints
- Can only submit new complaints

## Security Features

### Admin Verification
- All admin functions check user role before allowing access
- Unauthorized access attempts are logged and blocked
- Session-based authentication ensures security

### Data Protection
- SQL injection protection through prepared statements
- XSS protection through htmlspecialchars()
- File deletion when complaints are removed

## File Structure

### New Files Added:
- `phpGmailSMTP/admin_functions.php` - Admin-specific functions
- `phpGmailSMTP/get_complaint.php` - AJAX handler for complaint data
- `create_admin.php` - Admin user creation script
- `ADMIN_SETUP.md` - This documentation

### Modified Files:
- `phpGmailSMTP/trash.php` - Added admin interface and complaint display
- `controllerUserData.php` - Added role-based session management
- `database.sql` - Added role column to usertable

## Usage Examples

### Editing a Complaint
1. Login as admin
2. Find the complaint in the table
3. Click "Edit" button
4. Modify the details in the modal
5. Click "Update Complaint"

### Deleting a Complaint
1. Login as admin
2. Find the complaint in the table
3. Click "Delete" button
4. Confirm deletion
5. Complaint and associated image will be removed

### Changing Complaint Status
1. Login as admin
2. Edit the complaint
3. Change the status dropdown
4. Save changes

## Troubleshooting

### Admin Not Working
1. Check if the role column exists in usertable
2. Verify admin user has role = 'admin'
3. Clear browser cache and cookies
4. Check session variables

### Edit/Delete Buttons Not Showing
1. Ensure you're logged in as admin
2. Check if isAdmin() function returns true
3. Verify database connection

### AJAX Errors
1. Check browser console for JavaScript errors
2. Verify get_complaint.php is accessible
3. Check file permissions

## Security Notes

1. **Change Default Password**: Always change the default admin password
2. **Regular Updates**: Keep the system updated
3. **Backup**: Regularly backup the database
4. **Access Control**: Limit admin account access
5. **Logging**: Monitor admin actions for security

## Support

For issues or questions:
1. Check the error logs
2. Verify database connectivity
3. Test with different browsers
4. Check file permissions
