<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
    exit();
}

include 'db.php';

// Fetch user information
$username = $_SESSION['username'];
$user_query = $conn->query("SELECT * FROM users WHERE username = '$username'");
$user = $user_query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link href="reservation_form.css" rel="stylesheet">
<body>
<?php include 'includes/index_header.php'; ?>

<div class="container-fluid hero-landing p-0">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-9 col-md-11 col-12 mx-auto">
            <div class="row gy-4 gx-2">
                <!-- Form Box -->
                <div class="col-lg-7">
                    <div class="register-card p-4 bg-light rounded shadow"> <!-- Added background and shadow -->
                        <h2 class="text-center mb-4">Sports Venue Reservation Form</h2>
                        <form id="reservationForm" action="" method="POST">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['street'] . ' ' . $user['house_number']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="contact_number">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="venue_type">Venue Type</label>
                                <select class="form-control" id="venue_type" name="venue_type" required>
                                    <option value="Court A">Court A (Basketball / Volleyball Court)</option>
                                    <option value="Court B">Court B (Badminton Court)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="is_big_group" name="is_big_group">
                                <label class="form-check-label" for="is_big_group">Bigger groups (Liga, Party, Meeting, etc.)</label>
                            </div>
                            <div class="form-check mt-3 security-option">
                                <input type="checkbox" class="form-check-input" id="security_option" name="security_option">
                                <label class="form-check-label" for="security_option">Security Option (300 Php)</label>
                            </div>
                            <div class="form-check mt-3 caretaker-option">
                                <input type="checkbox" class="form-check-input" id="caretaker_option" name="caretaker_option">
                                <label class="form-check-label" for="caretaker_option">Caretaker Option (200 Php)</label>
                            </div>
                            <div class="form-group">
                                <p id="feedbackMessage" class="text-danger mt-3" style="display: none;"></p>
                            </div>
                            <button type="button" class="btn btn-primary mt-3" id="submitReservationButton">Submit</button>
                        </form>
                    </div>
                </div>
                <!-- Receipt Box -->
                <div class="col-lg-5">
                    <div class="receipt-box p-4 bg-light rounded shadow d-flex flex-column justify-content-center">
                        <h3 class="mb-3 text-center">Receipt Breakdown</h3>
                        <ul id="receiptList" class="receipt-list">
                            <!-- Receipt items will be dynamically updated here -->
                        </ul>
                        <h4 class="mt-3 text-center">Rates</h4>
                        <ul class="rates-list">
                            <li><strong>Court A:</strong> 100 Php/hour</li>
                            <li><strong>Court B:</strong> 200 Php/hour</li>
                            <li><strong>Bigger Groups (30 pax max):</strong> Initial 4 hours: 4000 Php, Extra hours: 1000 Php/hour</li>
                            <li><strong>Power Supply Fee:</strong> 100 Php/hour</li>
                            <li><strong>Security Option:</strong> 300 Php (one-time)</li>
                            <li><strong>Caretaker Option:</strong> 200 Php (one-time)</li>
                            <li><strong>Cash Bond:</strong> 1000 Php (refundable)</li>
                            <li>Cash bond is refundable after the reservation (2 days max) if no damages or violations occur.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Guidelines Modal -->
<div class="modal fade" id="guidelinesModal" tabindex="-1" aria-labelledby="guidelinesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="guidelinesModalLabel">Reservation Guidelines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul>
                    <li>Always maintain proper decorum and sportsmanship</li>
                    <li>Proper athletic attire/footwear is required. Playing shirtless, barefoot, slippers, sandals, and flip-flops are not allowed</li>
                    <li>No pets, bicycles, skateboards, scooters, and the like are allowed</li>
                    <li>Use the trash bins provided; Strictly no littering and spitting</li>
                    <li>Bringing and playing under the influence of drugs, alcohol, and intoxicating drinks are strictly prohibited</li>
                    <li>Deadly weapons are prohibited</li>
                    <li>Use of equipment and/or materials that will cause excessively loud noises are prohibited</li>
                    <li>Practice and Enforce Clean as You Go</li>
                </ul>
                <p class="mt-3"><strong>This is a first pay first serve basis.</strong></p>
                <p>After submitting, your request will be placed on hold until a payment is done. All payments are done physically in the barangay hall and will require your on-site signature.</p>
                <div class="form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="agreeGuidelines">
                    <label class="form-check-label" for="agreeGuidelines">I agree with the terms and guidelines</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmReservationButton" disabled>Confirm Reservation</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Reservation Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Your reservation has been successfully submitted!</p>
                <p><strong>Control Number:</strong> <span id="controlNumber"></span></p>
                <p>Please proceed to the barangay hall to complete your payment and signature.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="redirectToDashboard">Go to Dashboard</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const isBigGroupCheckbox = document.getElementById('is_big_group');
        const securityOptionContainer = document.querySelector('.security-option');
        const caretakerOptionContainer = document.querySelector('.caretaker-option');
        const securityOptionCheckbox = document.getElementById('security_option');
        const caretakerOptionCheckbox = document.getElementById('caretaker_option');
        const reservationForm = document.getElementById('reservationForm');
        const feedbackMessage = document.getElementById('feedbackMessage');
        const receiptList = document.getElementById('receiptList');
        const venueTypeInput = document.getElementById('venue_type');
        const confirmReservationButton = document.getElementById('confirmReservationButton');
        const agreeGuidelinesCheckbox = document.getElementById('agreeGuidelines');
        const submitReservationButton = document.getElementById('submitReservationButton');
        const successModalElement = document.getElementById('successModal');
        const successModal = new bootstrap.Modal(successModalElement);

        // Hide Security and Caretaker options initially
        securityOptionContainer.style.display = 'none';
        caretakerOptionContainer.style.display = 'none';

        // Enforce minutes to 00 and adjust for timezone
        startTimeInput.addEventListener('change', () => {
            const startTime = new Date(startTimeInput.value);
            const timezoneOffset = startTime.getTimezoneOffset() * 60000; // Convert offset to milliseconds
            const localTime = new Date(startTime.getTime() - timezoneOffset); // Adjust to local time
            localTime.setMinutes(0); // Set minutes to 00
            startTimeInput.value = localTime.toISOString().slice(0, 16); // Update the input value
        });

        endTimeInput.addEventListener('change', () => {
            const endTime = new Date(endTimeInput.value);
            const timezoneOffset = endTime.getTimezoneOffset() * 60000; // Convert offset to milliseconds
            const localTime = new Date(endTime.getTime() - timezoneOffset); // Adjust to local time
            localTime.setMinutes(0); // Set minutes to 00
            endTimeInput.value = localTime.toISOString().slice(0, 16); // Update the input value
        });

        // Restrict date selection to at least a week from now
        const today = new Date();
        today.setDate(today.getDate() + 7); // Add 7 days to the current date
        today.setHours(0, 0, 0, 0); // Reset time to midnight

        const minDate = today.toISOString().slice(0, 16); // Format as "YYYY-MM-DDTHH:mm"
        startTimeInput.setAttribute('min', minDate);
        endTimeInput.setAttribute('min', minDate);

        // Ensure end time is always after start time
        startTimeInput.addEventListener('change', () => {
            const startTime = new Date(startTimeInput.value);
            if (startTime) {
                const minEndTime = new Date(startTime.getTime() + 60 * 60 * 1000); // Add 1 hour to start time
                endTimeInput.setAttribute('min', minEndTime.toISOString().slice(0, 16));
            }
        });

        // Show/Hide Security and Caretaker options based on Big Group checkbox
        isBigGroupCheckbox.addEventListener('change', () => {
            if (isBigGroupCheckbox.checked) {
                securityOptionContainer.style.display = 'block';
                caretakerOptionContainer.style.display = 'block';
            } else {
                securityOptionContainer.style.display = 'none';
                caretakerOptionContainer.style.display = 'none';
                securityOptionCheckbox.checked = false; // Uncheck Security Option
                caretakerOptionCheckbox.checked = false; // Uncheck Caretaker Option
            }
            calculateTotal(); // Recalculate total when Big Group is toggled
        });

        // Calculate total cost and update receipt
        function calculateTotal() {
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(endTimeInput.value);

            // Reset feedback message
            feedbackMessage.style.display = 'none';
            feedbackMessage.textContent = '';
            receiptList.innerHTML = ''; // Clear receipt list

            // Validate fields
            if (!startTimeInput.value || !endTimeInput.value) {
                feedbackMessage.textContent = 'Start Time and End Time cannot be blank.';
                feedbackMessage.style.display = 'block';
                return;
            }

            if (startTime >= endTime) {
                feedbackMessage.textContent = 'Start Time must be earlier than End Time.';
                feedbackMessage.style.display = 'block';
                return;
            }

            // Check if the end time is on the same day as the start time
            if (
                startTime.getFullYear() !== endTime.getFullYear() ||
                startTime.getMonth() !== endTime.getMonth() ||
                startTime.getDate() !== endTime.getDate()
            ) {
                feedbackMessage.textContent = 'End Time must be on the same day as Start Time.';
                feedbackMessage.style.display = 'block';
                return;
            }

            if (startTime.getHours() < 8 || endTime.getHours() > 22) {
                feedbackMessage.textContent = 'Reservations are only allowed between 8:00 AM and 10:00 PM.';
                feedbackMessage.style.display = 'block';
                return;
            }

            const hours = Math.ceil((endTime - startTime) / (1000 * 60 * 60)); // Calculate total hours
            let totalCost = 0;

            if (isBigGroupCheckbox.checked) {
                // Big group calculation
                if (hours < 4) {
                    feedbackMessage.textContent = 'Big group reservations must be at least 4 hours.';
                    feedbackMessage.style.display = 'block';
                    return;
                }

                const extraHours = hours > 4 ? hours - 4 : 0;
                const powerSupplyFee = hours * 100;

                totalCost = 4000 + 1000 + (extraHours * 1000) + powerSupplyFee;
                if (securityOptionCheckbox.checked) totalCost += 300;
                if (caretakerOptionCheckbox.checked) totalCost += 200;

                // Update receipt breakdown
                receiptList.innerHTML += `<li>Base Cost (Initial 4 hours): 4000 Php</li>`;
                receiptList.innerHTML += `<li>Cash Bond: 1000 Php</li>`;
                if (extraHours > 0) {
                    receiptList.innerHTML += `<li>Extra Hours (${extraHours} hours): ${extraHours * 1000} Php</li>`;
                }
                receiptList.innerHTML += `<li>Power Supply Fee (${hours} hours): ${powerSupplyFee} Php</li>`;
                if (securityOptionCheckbox.checked) {
                    receiptList.innerHTML += `<li>Security Option: 300 Php</li>`;
                }
                if (caretakerOptionCheckbox.checked) {
                    receiptList.innerHTML += `<li>Caretaker Option: 200 Php</li>`;
                }
            } else {
                // Non-big group calculation
                const rate = venueTypeInput.value === 'Court A' ? 100 : 200;
                totalCost = rate * hours;

                // Update receipt breakdown
                receiptList.innerHTML += `<li>Rate (${rate} Php/hour): ${rate * hours} Php</li>`;
            }

            // Display total cost
            receiptList.innerHTML += `<li><strong>Total Cost: ${totalCost} Php</strong></li>`;
        }

        // Recalculate total cost on input changes
        startTimeInput.addEventListener('change', calculateTotal);
        endTimeInput.addEventListener('change', calculateTotal);
        securityOptionCheckbox.addEventListener('change', calculateTotal);
        caretakerOptionCheckbox.addEventListener('change', calculateTotal);
        venueTypeInput.addEventListener('change', calculateTotal);

        // Show guidelines modal on submit button click
        submitReservationButton.addEventListener('click', () => {
            calculateTotal(); // Ensure total is calculated before showing the modal

            if (feedbackMessage.style.display === 'block') {
                return; // Prevent showing modal if there are validation errors
            }

            // Show the guidelines modal
            const guidelinesModal = new bootstrap.Modal(document.getElementById('guidelinesModal'));
            guidelinesModal.show();
        });

        // Enable confirm button only if guidelines are agreed
        agreeGuidelinesCheckbox.addEventListener('change', () => {
            confirmReservationButton.disabled = !agreeGuidelinesCheckbox.checked;
        });

        // Redirect to the dashboard when the success modal is closed
        successModalElement.addEventListener('hidden.bs.modal', () => {
            window.location.href = 'dashboard.php'; // Replace with the desired redirect URL
        });

        // Handle reservation confirmation
        confirmReservationButton.addEventListener('click', () => {
            // Gather form data
            const formData = new FormData(reservationForm);

            // Send data to the server
            fetch('process_reservation.php', {
                method: 'POST',
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Close the guidelines modal
                        const guidelinesModal = bootstrap.Modal.getInstance(document.getElementById('guidelinesModal'));
                        guidelinesModal.hide();

                        // Wait for the server to return the reservation ID
                        const reservationId = data.reservation_id;

                        // Show success modal with the reservation ID
                        document.getElementById('controlNumber').textContent = reservationId; // Set the control number
                        successModal.show();

                        // Clear form fields
                        reservationForm.reset();
                        securityOptionContainer.style.display = 'none';
                        caretakerOptionContainer.style.display = 'none';
                        confirmReservationButton.disabled = true;
                    } else {
                        alert('Failed to submit reservation: ' + data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your reservation.');
                });
        });

        // Redirect to dashboard
        document.getElementById('redirectToDashboard').addEventListener('click', () => {
            window.location.href = 'dashboard.php';
        });
    });
</script>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>