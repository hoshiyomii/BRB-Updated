<div class="form-group">
    <label for="service_provider">Service Provider</label>
    <select class="form-control" id="service_provider" name="service_provider" required>
        <option value="Meralco">Meralco</option>
        <option value="Globe">Globe</option>
        <option value="PLDT">PLDT</option>
        <option value="Sky Cable">Sky Cable</option>
        <option value="CIGNAL">CIGNAL</option>
        <option value="Manila Water">Manila Water</option>
        <option value="Smart">Smart</option>
        <option value="Bayantel">Bayantel</option>
        <option value="Destiny">Destiny</option>
        <option value="Others">Others</option>
    </select>
</div>
<div class="form-group" id="other_service_provider_group" style="display: none;">
    <label for="other_service_provider">Specify Other Service Provider</label>
    <input type="text" class="form-control" id="other_service_provider" name="other_service_provider">
</div>
<script>
    document.getElementById('service_provider').addEventListener('change', function() {
        const otherGroup = document.getElementById('other_service_provider_group');
        otherGroup.style.display = this.value === 'Others' ? 'block' : 'none';
    });
</script>
<div class="form-group">
    <label for="date_of_work">Date of Work</label>
    <input type="date" class="form-control" id="date_of_work" name="date_of_work" required>
    <small id="dateOfWorkError" class="text-danger" style="display: none;">Date of work must be at least 1 week in the future.</small>
</div>
<div class="form-group">
    <label for="date_of_request">Date of Request</label>
    <input type="date" class="form-control" id="date_of_request" name="date_of_request" value="<?php echo date('Y-m-d'); ?>" readonly>
</div>
<div class="form-group">
    <label for="contact_no">Contact Number</label>
    <input type="text" class="form-control" id="contact_no" name="contact_no" required oninput="validateContactNumber()">
    <small id="contactNoError" class="text-danger" style="display: none;">Contact number must be exactly 11 digits and contain only numbers.</small>
</div>
<div class="form-group">
    <label for="address">Work Address</label>
    <input type="text" class="form-control" id="address" name="address" required>
</div>
<div class="form-group">
    <label for="utility_type">Utility Type</label>
    <select class="form-control" id="utility_type" name="utility_type" required>
        <option value="Water">Water</option>
        <option value="Electricity">Electricity</option>
        <option value="Internet">Internet</option>
        <option value="Others">Others</option>
    </select>
</div>
<div class="form-group" id="other_utility_type_group" style="display: none;">
    <label for="other_utility_type">Specify Other Utility Type</label>
    <input type="text" class="form-control" id="other_utility_type" name="other_utility_type">
</div>
<script>
    document.getElementById('utility_type').addEventListener('change', function() {
        const otherUtilityGroup = document.getElementById('other_utility_type_group');
        otherUtilityGroup.style.display = this.value === 'Others' ? 'block' : 'none';
    });

    // Validate Date of Work
    document.getElementById('date_of_work').addEventListener('input', function() {
        const dateOfWorkInput = document.getElementById('date_of_work');
        const errorElement = document.getElementById('dateOfWorkError');
        const selectedDate = new Date(dateOfWorkInput.value);
        const today = new Date();
        const oneWeekFromNow = new Date(today.setDate(today.getDate() + 7));

        if (selectedDate >= oneWeekFromNow) {
            errorElement.style.display = 'none';
            dateOfWorkInput.setCustomValidity('');
        } else {
            errorElement.style.display = 'block';
            dateOfWorkInput.setCustomValidity('Invalid date');
        }
    });

    // Validate Contact Number
    function validateContactNumber() {
        const contactInput = document.getElementById('contact_no');
        const errorElement = document.getElementById('contactNoError');
        const contactValue = contactInput.value;

        if (/^\d{11}$/.test(contactValue)) {
            errorElement.style.display = 'none';
            contactInput.setCustomValidity('');
        } else {
            errorElement.style.display = 'block';
            contactInput.setCustomValidity('Invalid contact number');
        }
    }
</script>
<div class="form-group">
    <label for="nature_of_work">Nature of Work</label>
    <select class="form-control" id="nature_of_work" name="nature_of_work" required>
        <option value="New installation">New installation</option>
        <option value="Repair/Maintenance">Repair/Maintenance</option>
        <option value="Permanent Disconnection">Permanent Disconnection</option>
        <option value="Reconnection">Reconnection</option>
    </select>
</div>