
<div class="form-group">
    <label for="schedule">Schedule</label>
    <input type="datetime-local" class="form-control" id="schedule" name="schedule" required>
    <small id="scheduleError" class="text-danger" style="display: none;">The scheduled date must be at least 1 week from now.</small>
</div>
<div class="form-group">
    <label for="contractor">Contractor</label>
    <input type="text" class="form-control" id="contractor" name="contractor" required>
</div>
<div class="form-group">
    <label for="construction_address">Construction Address</label>
    <input type="text" class="form-control" id="construction_address" name="construction_address" required>
</div>
<div class="form-group">
    <label for="infrastructures">Infrastructures</label>
    <input type="text" class="form-control" id="infrastructures" name="infrastructures" required>
</div>

<script>
    document.getElementById('schedule').addEventListener('input', function () {
        const scheduleInput = document.getElementById('schedule');
        const errorElement = document.getElementById('scheduleError');
        const selectedDate = new Date(scheduleInput.value);
        const today = new Date();
        const oneWeekFromNow = new Date(today.setDate(today.getDate() + 7));

        if (selectedDate >= oneWeekFromNow) {
            errorElement.style.display = 'none';
            scheduleInput.setCustomValidity('');
        } else {
            errorElement.style.display = 'block';
            scheduleInput.setCustomValidity('Invalid date');
        }
    });
</script>