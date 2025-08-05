<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the request details
$sql = "SELECT 
            wp.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            u.email, 
            CONCAT(u.house_number, ' ', u.street) AS address, 
            wp.utility_type, 
            wp.other_utility_type, 
            wp.service_provider, 
            wp.other_service_provider, 
            wp.date_of_work, 
            wp.nature_of_work, 
            wp.created_at,
            wp.status
        FROM work_permit_utilities wp
        JOIN users u ON wp.user_id = u.id
        WHERE wp.id = $request_id";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Error fetching request details or no record found.");
}

$request = $result->fetch_assoc();
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Edit Work Permit for Utilities Request</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="editWorkPermitForm">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($request['id']); ?>">
        <input type="hidden" name="document_type" value="work_permit_utilities">
        <div class="row g-3">
            <!-- Non-editable fields -->
            <div class="col-12 col-sm-6">
                <label><strong>Full Name:</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['full_name']); ?>" disabled>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Email:</strong></label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($request['email']); ?>" disabled>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Address:</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['address']); ?>" disabled>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Utility Type:</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['utility_type']); ?>" disabled>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Other Utility Type:</strong></label>
                <input type="text" class="form-control" name="other_utility_type" 
                       value="<?php echo htmlspecialchars($request['other_utility_type']); ?>" 
                       <?php echo $request['utility_type'] !== 'Others' ? 'disabled' : ''; ?>>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Service Provider:</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['service_provider']); ?>" disabled>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Other Service Provider:</strong></label>
                <input type="text" class="form-control" name="other_service_provider" 
                       value="<?php echo htmlspecialchars($request['other_service_provider']); ?>" 
                       <?php echo $request['service_provider'] !== 'Others' ? 'disabled' : ''; ?>>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Date of Request:</strong></label>
                <input type="text" class="form-control" value="<?php echo date("F j, Y, g:i a", strtotime($request['created_at'])); ?>" disabled>
            </div>
            <div class="col-12 col-sm-6">
                <label><strong>Nature of Work:</strong></label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['nature_of_work']); ?>" disabled>
            </div>

            <!-- Editable fields -->
            <div class="col-12">
                <label><strong>Date of Work:</strong></label>
                <input type="date" class="form-control" name="date_of_work" value="<?php echo htmlspecialchars($request['date_of_work']); ?>" required>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="submitEditWorkPermitForm()">Save Changes</button>
</div>

<script>
    function submitEditWorkPermitForm() {
        const form = document.getElementById('editWorkPermitForm');
        const formData = new FormData(form);

        // Debugging: Log the form data
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        $.ajax({
            url: 'document_modals/save_edit.php', // Ensure this path is correct
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // Ensure the response is treated as JSON
            success: function(response) {
                console.log(response); // Debugging: Log the response
                if (response.success) {
                    location.reload(); // Reload the page to reflect changes
                } else {
                    console.error('Error saving changes:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Debugging: Log AJAX errors
            }
        });
    }
</script>