<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link rel="stylesheet" href="register_user.css">
<body>
<?php include 'includes/index_header.php'; ?>

<div class="container-fluid hero-landing p-0" style="position:relative; min-height:100vh;">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-9 col-md-11 col-12 mx-auto">
            <div class="row gy-4 gx-2">
                <!-- Guidelines Box -->
                <div class="col-lg-5">
                    <div class="guidelines-box p-4 d-flex flex-column justify-content-center">
                        <h3 class="mb-3 text-center">Guidelines</h3>
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
                    <div class="register-card p-4 ">
                        <h2 class="text-center mb-4">Create an Account</h2>
                        <form id="registerForm" action="process_register_user.php" method="POST">
                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="text" class="form-control input-dark" placeholder="Username" style="height: 55px;" name="username" required>
                                </div>
                                <div class="col-12">
                                    <input type="password" class="form-control input-dark" placeholder="Password" style="height: 55px;" id="password" name="password" required>
                                </div>
                                <div class="col-12">
                                    <input type="password" class="form-control input-dark" placeholder="Confirm Password" style="height: 55px;" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control input-dark" placeholder="First name" style="height: 55px;" name="first_name" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control input-dark" placeholder="Middle name" style="height: 55px;" name="middle_name" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control input-dark" placeholder="Last name" style="height: 55px;" name="last_name" required>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="form-control input-dark" placeholder="Phone Number" style="height: 55px;" id="phone_number" name="phone_number" required>
                                </div>
                                <div class="col-12">
                                    <input type="email" class="form-control input-dark" placeholder="Email" style="height: 55px;" name="email" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label>Birthdate:</label>
                                    <input type="date" class="form-control input-dark" name="birthdate" required max="<?= date('Y-m-d'); ?>">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label>Gender:</label>
                                    <select class="form-control input-dark" name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="street">Street:</label>
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
                                    <label for="blood_type">Blood Type:</label>
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
                                    <input type="text" class="form-control input-dark" placeholder="Lot, Block" style="height: 55px;" id="lot_block" name="lot_block" required>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="form-control input-dark" placeholder="House Number" style="height: 55px;" id="house_number" name="house_number" required>
                                </div>
                                <div class="col-12">
                                    <p id="errorMsg" style="color: red;"> </p>
                                    <button class="btn btn-primary w-100 py-3" type="submit">Register</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Success Modal -->
<div class="modal fade" id="registerSuccessModal" tabindex="-1" aria-labelledby="registerSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header border-0 justify-content-center"></div>
            <div class="modal-body">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#198754" class="mb-4" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM7 10.414l5.707-5.707-1.414-1.414L7 7.586 4.707 5.293 3.293 6.707 7 10.414z"/>
                </svg>
                <div class="mb-3 fs-5 text-secondary">Registration successful! Please check your email to verify your account.</div>
                <div class="mb-3 fs-6 text-secondary">Check the "Spam" section in case not found.</div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <a href="user_login.php" class="btn btn-primary px-4">Okay, Proceed to Log-in</a>
            </div>
        </div>
    </div>
</div>

<script src="register.js?v=1" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
