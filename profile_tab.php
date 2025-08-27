
<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <h3 class="mt-4">Profile Information</h3>

    <?php if (isset($_SESSION['profile_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['profile_success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?></div>
    <?php endif; ?>

    <form id="profileForm" action="process_update_profile.php" method="POST">
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label>Username:</label>
                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" minlength="4" maxlength="16" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>First Name:</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>Middle Name:</label>
                <input type="text" class="form-control" name="middle_name" value="<?php echo htmlspecialchars($userData['middle_name']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>Last Name:</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>Phone Number:</label>
                <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($userData['phone_number']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>Birthdate:</label>
                <input type="date" class="form-control" name="birthdate" value="<?php echo htmlspecialchars($userData['birthdate']); ?>" required max="<?= date('Y-m-d'); ?>">
            </div>
            <div class="col-12 col-sm-6">
                <label>Gender:</label>
                <select class="form-control" name="gender" required>
                    <option value="Male" <?php if($userData['gender']=='Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if($userData['gender']=='Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if($userData['gender']=='Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label>Street:</label>
                <select class="form-control" name="street" required>
                    <?php
                    $streets = [
                        "Boni Serrano Avenue (formerly Santolan Road)",
                        "Comets Loop",
                        "Crestline Road",
                        "Evening Glow Road",
                        "FVR Road",
                        "Highland Drive",
                        "Hillside Drive",
                        "Hillside Loop",
                        "Milky Way Drive",
                        "Moonlight Loop",
                        "Promenade Lane",
                        "Rajah Matanda Street",
                        "Riverside Drive",
                        "Starline Drive",
                        "Twin Peaks Drive",
                        "Union Lane"
                    ];
                    foreach ($streets as $street) {
                        $selected = ($userData['street'] == $street) ? 'selected' : '';
                        echo "<option value=\"$street\" $selected>$street</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label>Blood Type:</label>
                <select class="form-control" name="blood_type" required>
                    <?php
                    $blood_types = ["A+","A-","B+","B-","AB+","AB-","O+","O-","Unknown"];
                    foreach ($blood_types as $bt) {
                        $selected = ($userData['blood_type'] == $bt) ? 'selected' : '';
                        echo "<option value=\"$bt\" $selected>$bt</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-12">
                <label>Sector <span class="text-muted" style="font-weight:normal; font-size:90%;">(Select all that apply)</span></label>
                <div class="row g-2">
                    <?php
                    $sector_options = ["Widow", "Solo Parent", "PWD", "LGBTQ", "Senior Citizen", "Student"];
                    $user_sectors = isset($userData['sector']) ? explode(',', $userData['sector']) : [];
                    foreach ($sector_options as $sector) {
                        $checked = in_array($sector, $user_sectors) ? 'checked' : '';
                        $id = 'sector_' . strtolower(str_replace(' ', '_', $sector));
                        echo '<div class="col-6 col-md-4">';
                        echo '<div class="form-check">';
                        echo '<input class="form-check-input" type="checkbox" name="sector[]" id="' . $id . '" value="' . $sector . '" ' . $checked . '>';
                        echo '<label class="form-check-label" for="' . $id . '">' . $sector . '</label>';
                        echo '</div></div>';
                    }
                    ?>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <label>Lot, Block:</label>
                <input type="text" class="form-control" name="lot_block" value="<?php echo htmlspecialchars($userData['lot_block']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>House Number:</label>
                <input type="text" class="form-control" name="house_number" value="<?php echo htmlspecialchars($userData['house_number']); ?>" required>
            </div>
            <div class="col-12">
                <h4 class="mt-4">Security</h4>
            </div>
            <div class="col-12">
                <label>New Password:</label>
                <input type="password" class="form-control" name="new_password" minlength="8" maxlength="32" placeholder="Leave blank to keep current password">
            </div>
            <div class="col-12">
                <label>Confirm New Password:</label>
                <input type="password" class="form-control" name="confirm_new_password" minlength="8" maxlength="32" placeholder="Re-enter new password">
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </div>
    </form>
</div>