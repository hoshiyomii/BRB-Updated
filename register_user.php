
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link rel="stylesheet" href="register_user.css">
<body>
<?php include 'includes/index_header.php'; ?>

<div class="container-fluid hero-landing">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-9 col-md-11 col-12 mx-auto">
            <div class="row gy-4 gx-2">
                <!-- Guidelines Box -->
                <div class="col-lg-5">
                    <div class="guidelines-box">
                        <h3 class="mb-3 text-center">Registration Guidelines</h3>
                        <ul class="guideline-list">
                            <li><i class="fa fa-user-shield"></i> Username must be between 5 and 16 characters and unique.</li>
                            <li><i class="fa fa-lock"></i> Password must be at least 8 characters.</li>
                            <li><i class="fa fa-birthday-cake"></i> You must be at least 13 years old to register.</li>
                            <li><i class="fa fa-envelope"></i> Use a valid email address for verification.</li>
                            <li><i class="fa fa-tint"></i> Select your correct blood type.</li>
                            <li><i class="fa fa-id-card"></i> Fill in all required personal information accurately.</li>
                        </ul>
                    </div>
                </div>
                <!-- Registration Form Box -->
                <div class="col-lg-7">
                    <div class="register-card">
                        <h2 class="text-center mb-4">Create an Account</h2>
                        <form id="registerForm" action="process_register_user.php" method="POST">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control input-dark" id="username" name="username" required minlength="5" maxlength="16">
                                </div>
                                <div class="col-12">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control input-dark" id="password" name="password" required minlength="8">
                                </div>
                                <div class="col-12">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" class="form-control input-dark" id="confirm_password" name="confirm_password" required minlength="8">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control input-dark" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" class="form-control input-dark" id="middle_name" name="middle_name" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control input-dark" id="last_name" name="last_name" required>
                                </div>
                                <div class="col-12">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" class="form-control input-dark" id="phone_number" name="phone_number" required>
                                </div>
                                <div class="col-12">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control input-dark" id="email" name="email" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" class="form-control input-dark" id="birthdate" name="birthdate" required max="<?= date('Y-m-d'); ?>">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="gender">Gender</label>
                                    <select class="form-control input-dark" id="gender" name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="street">Street</label>
                                    <select class="form-control input-dark" id="street" name="street" required>
                                        <option value="" disabled selected>Select your street</option>
                                        <option value="Boni Serrano Avenue">Boni Serrano Avenue (formerly Santolan Road)</option>
                                        <option value="Comets Loop">Comets Loop</option>
                                        <option value="Crestline Road">Crestline Road</option>
                                        <option value="Evening Glow Road">Evening Glow Road</option>
                                        <option value="FVR Road">FVR Road</option>
                                        <option value="Highland Drive">Highland Drive</option>
                                        <option value="Hillside Drive">Hillside Drive</option>
                                        <option value="Hillside Loop">Hillside Loop</option>
                                        <option value="Milky Way Drive">Milky Way Drive</option>
                                        <option value="Moonlight Loop">Moonlight Loop</option>
                                        <option value="Promenade Lane">Promenade Lane</option>
                                        <option value="Rajah Matanda Street">Rajah Matanda Street</option>
                                        <option value="Riverside Drive">Riverside Drive</option>
                                        <option value="Starline Drive">Starline Drive</option>
                                        <option value="Twin Peaks Drive">Twin Peaks Drive</option>
                                        <option value="Union Lane">Union Lane</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="blood_type">Blood Type</label>
                                    <select class="form-control input-dark" id="blood_type" name="blood_type" required>
                                        <option value="" disabled selected>Select your blood type</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Sector <span class="text-muted" style="font-weight:normal; font-size:90%;">(Select all that apply)</span></label>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sector[]" id="sector_widow" value="Widow">
                                                <label class="form-check-label" for="sector_widow">Widow</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sector[]" id="sector_solo_parent" value="Solo Parent">
                                                <label class="form-check-label" for="sector_solo_parent">Solo Parent</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sector[]" id="sector_pwd" value="PWD">
                                                <label class="form-check-label" for="sector_pwd">PWD</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sector[]" id="sector_lgbtq" value="LGBTQ">
                                                <label class="form-check-label" for="sector_lgbtq">LGBTQ</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sector[]" id="sector_senior" value="Senior Citizen">
                                                <label class="form-check-label" for="sector_senior">Senior Citizen</label>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sector[]" id="sector_student" value="Student">
                                                <label class="form-check-label" for="sector_student">Student</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="lot_block">Lot, Block</label>
                                    <input type="text" class="form-control input-dark" id="lot_block" name="lot_block" required>
                                </div>
                                <div class="col-12">
                                    <label for="house_number">House Number</label>
                                    <input type="text" class="form-control input-dark" id="house_number" name="house_number" required>
                                </div>
                                <div class="col-12">
                                    <p id="errorMsg" style="color: red;"> </p>
                                    <button class="btn btn-primary w-100 py-3" type="submit">Register</button>
                                </div>
                            </div>
                        </form>
                        <p class="text-center mt-3"><a href="user_login.php">Back to Log-in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Success Modal -->
<div class="modal fade" id="registerSuccessModal" tabindex="-1" aria-labelledby="registerSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerSuccessModalLabel">Registration Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#198754" class="mb-4" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM7 10.414l5.707-5.707-1.414-1.414L7 7.586 4.707 5.293 3.293 6.707 7 10.414z"/>
                </svg>
                <div class="mb-3 fs-5 text-secondary">Registration successful! Please check your email to verify your account.</div>
                <div class="mb-3 fs-6 text-secondary">Check the "Spam" section in case not found.</div>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="user_login.php" class="btn btn-primary px-4">Okay, Proceed to Log-in</a>
            </div>
        </div>
    </div>
</div>

</script>
<script>
// Auto-generate username based on first and last name
document.addEventListener('DOMContentLoaded', function() {
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const usernameInput = document.getElementById('username');

    function generateUsername() {
        const first = firstNameInput.value.trim().toLowerCase();
        const last = lastNameInput.value.trim().toLowerCase();
        if (first && last) {
            // Generate username: first initial + last name + random 2-digit number
            const randomNum = Math.floor(10 + Math.random() * 90); // 2 digits
            let uname = first.charAt(0) + last.replace(/[^a-z0-9]/g, '') + randomNum;
            uname = uname.substring(0, 16); // max 16 chars
            usernameInput.value = uname;
        }
    }

    firstNameInput.addEventListener('input', generateUsername);
    lastNameInput.addEventListener('input', generateUsername);
});
</script>
<script src="register.js?v=1" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
