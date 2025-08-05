<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$request_id) {
    die("Invalid request ID.");
}

// Fetch the request details
$sql = "SELECT 
            cr.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            u.email, 
            CONCAT(u.house_number, ' ', u.street) AS address, 
            cr.resident_since, 
            cr.created_at, 
            cr.status
        FROM certificate_of_residency cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.id = $request_id";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Error fetching request details or no record found.");
}

$request = $result->fetch_assoc();
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Edit Certificate of Residency Request</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="editCertificateOfResidencyForm">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($request['id']); ?>">
        <input type="hidden" name="document_type" value="certificate_of_residency">
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
                <label><strong>Date of Request:</strong></label>
                <input type="text" class="form-control" value="<?php echo date("F j, Y, g:i a", strtotime($request['created_at'])); ?>" disabled>
            </div>

            <!-- Editable field -->
            <div class="col-12">
                <label><strong>Resident Since:</strong></label>
                <input type="date" class="form-control" name="resident_since" value="<?php echo htmlspecialchars($request['resident_since']); ?>" required>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" onclick="submitEditCertificateOfResidencyForm()">Save Changes</button>
</div>

<script>
    function submitEditCertificateOfResidencyForm() {
        const form = document.getElementById('editCertificateOfResidencyForm');
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