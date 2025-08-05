
<div class="form-group">
    <label for="business_name">Business Name</label>
    <input type="text" class="form-control" id="business_name" name="business_name" required>
</div>

<div class="form-group">
    <label for="business_type">Business Type</label>
    <select class="form-control" id="business_type" name="business_type" required>
        <option value="Solo">Solo</option>
        <option value="Shared">Shared</option>
    </select>
</div>

<div class="form-group" id="co_owner_group" style="display: none;">
    <label for="co_owner">Co-owner</label>
    <input type="text" class="form-control" id="co_owner" name="co_owner">
</div>

<div class="form-group">
    <label for="nature_of_business">Nature of Business</label>
    <input type="text" class="form-control" id="nature_of_business" name="nature_of_business" required>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const businessType = document.getElementById('business_type');
        const coOwnerGroup = document.getElementById('co_owner_group');

        businessType.addEventListener('change', function () {
            coOwnerGroup.style.display = businessType.value === 'Shared' ? 'block' : 'none';
        });
    });
</script>