<div class="form-group">
    <label for="birthdate">Birthdate</label>
    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>" readonly>
</div>
<div class="form-group">
    <label for="resident_since">Resident of Blue Ridge B Since</label>
    <input type="date" class="form-control" id="resident_since" name="resident_since" required>
    <small id="residentSinceError" class="text-danger" style="display: none;">The "Resident Since" date must be at least 6 months in the past.</small>
</div>
<div class="form-group">
    <label for="date">Date</label>
    <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly>
</div>
<div class="form-group">
    <label for="id_image">Attach ID Image</label>
    <input type="file" class="form-control" id="id_image" name="id_image" required>
</div>

<script>
    document.getElementById('resident_since').addEventListener('input', function () {
        const residentSinceInput = document.getElementById('resident_since');
        const errorElement = document.getElementById('residentSinceError');
        const selectedDate = new Date(residentSinceInput.value);
        const today = new Date();
        const sixMonthsAgo = new Date(today.setMonth(today.getMonth() - 6));

        if (selectedDate <= sixMonthsAgo) {
            errorElement.style.display = 'none';
            residentSinceInput.setCustomValidity('');
        } else {
            errorElement.style.display = 'block';
            residentSinceInput.setCustomValidity('Invalid date');
        }
    });
</script>