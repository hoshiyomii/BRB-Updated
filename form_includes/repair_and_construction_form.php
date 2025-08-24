<div class="form-group">
    <label for="construction_address">Construction Address</label>
    <input type="text" class="form-control" id="construction_address" name="construction_address" required>
</div>
<div class="form-group">
    <label for="date_of_request">Date of Request</label>
    <input type="date" class="form-control" id="date_of_request" name="date_of_request" value="<?php echo date('Y-m-d'); ?>" readonly>
</div>
<div class="form-group">
    <label for="homeowner_name">Name of Homeowner</label>
    <input type="text" class="form-control" id="homeowner_name" name="homeowner_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
</div>
<div class="form-group">
    <label for="contractor_name">Name of Contractor</label>
    <input type="text" class="form-control" id="contractor_name" name="contractor_name" required>
</div>
<div class="form-group">
    <label for="contractor_contact">Contact Number of Contractor</label>
    <input type="text" class="form-control" id="contractor_contact" name="contractor_contact" required oninput="validateContractorContact()">
    <small id="contractorContactError" class="text-danger" style="display: none;">Contact number must be exactly 11 digits and contain only numbers.</small>
</div>
<div class="form-group">
    <label for="activity_nature">Nature of Activity</label>
    <select class="form-control" id="activity_nature" name="activity_nature" required>
        <option value="Repairs">Repairs</option>
        <option value="Minor Construction">Minor Construction</option>
        <option value="Construction">Construction</option>
        <option value="Demolition">Demolition</option>
    </select>
</div>

<script>
    function validateContractorContact() {
        const contactInput = document.getElementById('contractor_contact');
        const errorElement = document.getElementById('contractorContactError');
        const contactValue = contactInput.value;

        // Check if the input is exactly 11 digits and contains only numbers
        if (/^\d{11}$/.test(contactValue)) {
            errorElement.style.display = 'none';
            contactInput.setCustomValidity(''); // Clear any custom validation errors
        } else {
            errorElement.style.display = 'block';
            contactInput.setCustomValidity('Invalid contact number'); // Set a custom validation error
        }
    }
</script>
