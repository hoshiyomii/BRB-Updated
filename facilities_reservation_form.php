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
                    <div class="register-card p-4 bg-light rounded shadow">
                        <h2 class="text-center mb-4">Facilities Reservation Form</h2>
                        <form id="facilitiesReservationForm" action="" method="POST">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="facility_type">Facility Type</label>
                                <select class="form-control" id="facility_type" name="facility_type" required>
                                    <option value="" disabled selected>Select Facility</option>
                                    <option value="Multi Purpose Hall">Multi Purpose Hall (Bulwagan)</option>
                                    <option value="Community Center">Community Center</option>
                                    <option value="Session Hall">Session Hall</option>
                                    <option value="Conference Room">Conference Room</option>
                                    <option value="Small Meeting Room">Small Meeting Room</option>
                                    <option value="Playground">Playground</option>
                                </select>
                            </div>
                            <div class="form-group" id="courtImageContainer" style="text-align:center;">
                                <img id="courtImage" src="img/logo-brb.png" alt="Select Facility" style="width:100%;max-width:400px;height:300px;object-fit:cover;display:block;margin:auto;">
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="with_aircon" name="with_aircon">
                                <label class="form-check-label" for="with_aircon">With Aircon</label>
                            </div>
                            <div class="form-check mt-3 rooftop-option" style="display: none;">
                                <input type="checkbox" class="form-check-input" id="rooftop_option" name="rooftop_option">
                                <label class="form-check-label" for="rooftop_option">Rooftop Option (600 Php/hour)</label>
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
                                <input type="checkbox" class="form-check-input" id="sound_system" name="sound_system">
                                <label class="form-check-label" for="sound_system">Sound System</label>
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="projector" name="projector">
                                <label class="form-check-label" for="projector">Projector With Screen</label>
                            </div>
                            <div class="form-group mt-3">
                                <label for="lifetime_table">Life-time Table</label>
                                <input type="number" class="form-control" id="lifetime_table" name="lifetime_table" min="0" value="0">
                            </div>
                            <div class="form-group mt-3">
                                <label for="lifetime_chair">Life-time Chair</label>
                                <input type="number" class="form-control" id="lifetime_chair" name="lifetime_chair" min="0" value="0">
                            </div>
                            <div class="form-group mt-3">
                                <label for="long_table">Long Table</label>
                                <input type="number" class="form-control" id="long_table" name="long_table" min="0" value="0">
                            </div>
                            <div class="form-group mt-3">
                                <label for="monoblock_chair">Monoblock Chair (10 Php each)</label>
                                <input type="number" class="form-control" id="monoblock_chair" name="monoblock_chair" min="0" value="0">
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="group_over_50" name="group_over_50">
                                <label class="form-check-label" for="group_over_50">Group Over 50 Guests</label>
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
                        <button class="btn btn-secondary mt-3" id="printReceiptButton" type="button">Print Receipt</button>
                        <div id="printableReceipt" style="display:none;">
                            <h2 style="text-align:center;">Reservation Receipt</h2>
                            <hr>
                            <p><strong>Name:</strong> <span id="receiptName"></span></p>
                            <p><strong>Facility:</strong> <span id="receiptFacility"></span></p>
                            <p><strong>Date:</strong> <span id="receiptDate"></span></p>
                            <p><strong>Start Time:</strong> <span id="receiptStart"></span></p>
                            <p><strong>End Time:</strong> <span id="receiptEnd"></span></p>
                            <p><strong>Equipment/Options:</strong> <span id="receiptEquipment"></span></p>
                            <h4>Breakdown</h4>
                            <ul id="receiptBreakdown"></ul>
                            <h3>Total: <span id="receiptTotal"></span> Php</h3>
                        </div>
                        <h4 class="mt-3 text-center">Rates</h4>
                        <ul class="rates-list">
                            <li><strong>Multi Purpose Hall:</strong> 5000 Php (with aircon), 3500 Php (without aircon) for the first 4 hours</li>
                            <li><strong>Community Center:</strong> 4000 Php (with aircon), 3000 Php (without aircon) for the first 4 hours</li>
                            <li><strong>Rooftop Option (for Community Center):</strong> 600 Php/hour</li>
                            <li><strong>Session Hall:</strong> 600 Php/hour</li>
                            <li><strong>Conference Room:</strong> 400 Php/hour</li>
                            <li><strong>Small Meeting Room:</strong> 200 Php/hour</li>
                            <li><strong>Sound System:</strong> 1000 Php</li>
                            <li><strong>Projector With Screen:</strong> 1500 Php</li>
                            <li><strong>Life-time Table:</strong> 150 Php each</li>
                            <li><strong>Life-time Chair:</strong> 50 Php each</li>
                            <li><strong>Long Table:</strong> 200 Php each</li>
                            <li><strong>Monoblock Chair:</strong> 10 Php each</li>
                            <li><strong>Security/Parking Assistance:</strong> 250 Php</li>
                            <li><strong>Group Over 50 Guests:</strong> 500 Php</li>
                            <li><strong>Caretaker/Cleaning Post Event:</strong> 250 Php</li>
                            <li><strong>Sound System Setup Operator:</strong> 100 Php</li>
                            <li><strong>Cash Bond:</strong> 1000 Php</li>
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
                    <li>Ensure proper use of the facilities and maintain cleanliness.</li>
                    <li>Reservations are on a first-pay, first-serve basis.</li>
                    <li>Payments must be made physically at the barangay hall.</li>
                    <li>All reservations require an on-site signature for confirmation.</li>
                </ul>
                <p class="mt-3"><strong>Note:</strong> Failure to comply with the guidelines may result in cancellation of the reservation.</p>
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
                <a id="downloadPdfReceipt" href="#" class="btn btn-success mt-2" target="_blank" style="display:none;">Download PDF Receipt</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="redirectToDashboard">Go to Dashboard</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const facilityTypeInput = document.getElementById('facility_type');
        const withAirconCheckbox = document.getElementById('with_aircon');
        const rooftopOptionCheckbox = document.getElementById('rooftop_option');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const receiptList = document.getElementById('receiptList');
        const feedbackMessage = document.getElementById('feedbackMessage');
        const submitReservationButton = document.getElementById('submitReservationButton');
        const confirmReservationButton = document.getElementById('confirmReservationButton');
        const agreeGuidelinesCheckbox = document.getElementById('agreeGuidelines');
        const successModalElement = document.getElementById('successModal');
        const successModal = new bootstrap.Modal(successModalElement);
        const groupOver50Checkbox = document.getElementById('group_over_50');
        const soundSystemCheckbox = document.getElementById('sound_system');
        const projectorCheckbox = document.getElementById('projector');
        const lifetimeTableInput = document.getElementById('lifetime_table');
        const lifetimeChairInput = document.getElementById('lifetime_chair');
        const longTableInput = document.getElementById('long_table');
        const monoblockChairInput = document.getElementById('monoblock_chair');

        // Show/Hide "With Aircon" checkbox based on facility type
        facilityTypeInput.addEventListener('change', () => {
            if (facilityTypeInput.value === 'Multi Purpose Hall' || facilityTypeInput.value === 'Community Center') {
                withAirconCheckbox.parentElement.style.display = 'block'; // Show the checkbox
            } else {
                withAirconCheckbox.parentElement.style.display = 'none'; // Hide the checkbox
                withAirconCheckbox.checked = false; // Ensure it is unchecked when hidden
            }

            // Show/Hide Rooftop Option for Community Center
            if (facilityTypeInput.value === 'Community Center') {
                rooftopOptionCheckbox.parentElement.style.display = 'block';
            } else {
                rooftopOptionCheckbox.parentElement.style.display = 'none';
                rooftopOptionCheckbox.checked = false;
            }

            // Change image based on facility type
            let imgSrc = 'img/logo.png'; // Default image
            switch (facilityTypeInput.value) {
                case 'Multi Purpose Hall':
                    imgSrc = 'img/Multi purpose hall.jpg';
                    break;
                case 'Community Center':
                    imgSrc = 'img/logo-brb.png';
                    break;
                case 'Session Hall':
                    imgSrc = 'img/Multi purpose hall.jpg';
                    break;
                case 'Conference Room':
                    imgSrc = 'img/logo-brb.png.';
                    break;
                case 'Small Meeting Room':
                    imgSrc = 'img/logo-brb.png';
                    break;
                case 'Playground':
                    imgSrc = 'img/Playground.jpg';
                    break;
                // Add more cases as needed
            }
            document.getElementById('courtImage').src = imgSrc;

            calculateTotal(); // Recalculate total when facility type changes
        });

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

        // Calculate Total Cost
        function calculateTotal() {
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(endTimeInput.value);

            // Reset feedback message
            feedbackMessage.style.display = 'none';
            feedbackMessage.textContent = '';
            receiptList.innerHTML = ''; // Clear receipt list

            // Also clear printable receipt
            document.getElementById('receiptBreakdown').innerHTML = '';
            document.getElementById('receiptEquipment').textContent = '';
            document.getElementById('receiptTotal').textContent = '';


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

            if (startTime.getHours() < 8 || endTime.getHours() > 23) {
                feedbackMessage.textContent = 'Reservations are only allowed between 8:00 AM and 11:00 PM.';
                feedbackMessage.style.display = 'block';
                return;
            }

            // Fill printable receipt details
            document.getElementById('receiptName').textContent = document.getElementById('name').value;
            document.getElementById('receiptFacility').textContent = facilityTypeInput.value;
            document.getElementById('receiptDate').textContent = startTimeInput.value ? startTimeInput.value.split('T')[0] : '';
            document.getElementById('receiptStart').textContent = startTimeInput.value ? startTimeInput.value.split('T')[1] : '';
            document.getElementById('receiptEnd').textContent = endTimeInput.value ? endTimeInput.value.split('T')[1] : '';

            // Equipment/Options summary
            let equipment = [];
            if (withAirconCheckbox.checked) equipment.push('With Aircon');
            if (rooftopOptionCheckbox.checked) equipment.push('Rooftop Option');
            if (soundSystemCheckbox.checked) equipment.push('Sound System');
            if (projectorCheckbox.checked) equipment.push('Projector With Screen');
            if (groupOver50Checkbox.checked) equipment.push('Group Over 50 Guests');
            if (parseInt(lifetimeTableInput.value) > 0) equipment.push(lifetimeTableInput.value + ' Life-time Table');
            if (parseInt(lifetimeChairInput.value) > 0) equipment.push(lifetimeChairInput.value + ' Life-time Chair');
            if (parseInt(longTableInput.value) > 0) equipment.push(longTableInput.value + ' Long Table');
            if (parseInt(monoblockChairInput.value) > 0) equipment.push(monoblockChairInput.value + ' Monoblock Chair');
            document.getElementById('receiptEquipment').textContent = equipment.length ? equipment.join(', ') : 'None';

            const hours = Math.ceil((endTime - startTime) / (1000 * 60 * 60)); // Calculate total hours
            let totalCost = 0;

            // Apply unique logic for each facility type
            if (facilityTypeInput.value === 'Multi Purpose Hall' || facilityTypeInput.value === 'Community Center') {
                // Enforce 4-hour minimum for Multi Purpose Hall and Community Center
                if (hours < 4) {
                    feedbackMessage.textContent = 'Reservations for Multi Purpose Hall and Community Center must be at least 4 hours.';
                    feedbackMessage.style.display = 'block';
                    return;
                }

                let baseCost = 0;
                let extraHourCost = 0;

                if (facilityTypeInput.value === 'Multi Purpose Hall') {
                    baseCost = withAirconCheckbox.checked ? 5000 : 3500;
                    extraHourCost = withAirconCheckbox.checked ? 1000 : 700;
                } else if (facilityTypeInput.value === 'Community Center') {
                    baseCost = withAirconCheckbox.checked ? 4000 : 3000;
                    extraHourCost = withAirconCheckbox.checked ? 800 : 600;
                }

                const extraHours = hours > 4 ? hours - 4 : 0;
                totalCost += baseCost + (extraHours * extraHourCost);

                // Update receipt breakdown
                receiptList.innerHTML += `<li>Base Cost (First 4 hours): ${baseCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Base Cost (First 4 hours): ${baseCost} Php</li>`;
                if (extraHours > 0) {
                    receiptList.innerHTML += `<li>Extra Hours (${extraHours} hours): ${extraHours * extraHourCost} Php</li>`;
                    document.getElementById('receiptBreakdown').innerHTML += `<li>Extra Hours (${extraHours} hours): ${extraHours * extraHourCost} Php</li>`;
                }

                // Add mandatory charges
                totalCost += 1000; // Cash bond
                totalCost += 250; // Security/Parking Assistance
                if (groupOver50Checkbox.checked) totalCost += 250; // Group over 50 guests
                totalCost += 250; // Caretaker/Cleaning Post Event
                totalCost += 100; // Sound system setup operator

                receiptList.innerHTML += `<li>Cash Bond: 1000 Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Cash Bond: 1000 Php</li>`;
                receiptList.innerHTML += `<li>Security/Parking Assistance: 250 Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Security/Parking Assistance: 250 Php</li>`;
                if (groupOver50Checkbox.checked) {
                    receiptList.innerHTML += `<li>Group Over 50 Guests: 250 Php</li>`;
                    document.getElementById('receiptBreakdown').innerHTML += `<li>Group Over 50 Guests: 250 Php</li>`;
                }
                receiptList.innerHTML += `<li>Caretaker/Cleaning Post Event: 250 Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Caretaker/Cleaning Post Event: 250 Php</li>`;
                receiptList.innerHTML += `<li>Sound System Setup Operator: 100 Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Sound System Setup Operator: 100 Php</li>`;

                // Add Rooftop Option cost if selected
                if (rooftopOptionCheckbox.checked) {
                    const rooftopCost = hours * 600; // 600 Php/hour
                    totalCost += rooftopCost;
                    receiptList.innerHTML += `<li>Rooftop Option (${hours} hours): ${rooftopCost} Php</li>`;
                    document.getElementById('receiptBreakdown').innerHTML += `<li>Rooftop Option (${hours} hours): ${rooftopCost} Php</li>`;
                }
            } else if (facilityTypeInput.value === 'Session Hall') {
                // Session Hall logic
                totalCost = hours * 600; // 600 Php/hour
                receiptList.innerHTML += `<li>Session Hall (${hours} hours): ${totalCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Session Hall (${hours} hours): ${totalCost} Php</li>`;
            } else if (facilityTypeInput.value === 'Conference Room') {
                // Conference Room logic
                totalCost = hours * 400; // 400 Php/hour
                receiptList.innerHTML += `<li>Conference Room (${hours} hours): ${totalCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Conference Room (${hours} hours): ${totalCost} Php</li>`;
            } else if (facilityTypeInput.value === 'Small Meeting Room') {
                // Small Meeting Room logic
                totalCost = hours * 200; // 200 Php/hour
                receiptList.innerHTML += `<li>Small Meeting Room (${hours} hours): ${totalCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Small Meeting Room (${hours} hours): ${totalCost} Php</li>`;
            }

            // Add additional costs
            if (soundSystemCheckbox.checked) {
                totalCost += 1000;
                receiptList.innerHTML += `<li>Sound System: 1000 Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Sound System: 1000 Php</li>`;
            }
            if (projectorCheckbox.checked) {
                totalCost += 1500;
                receiptList.innerHTML += `<li>Projector With Screen: 1500 Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Projector With Screen: 1500 Php</li>`;
            }
            const lifetimeTableCost = lifetimeTableInput.value * 150;
            if (lifetimeTableInput.value > 0) {
                totalCost += lifetimeTableCost;
                receiptList.innerHTML += `<li>Life-time Table (${lifetimeTableInput.value}): ${lifetimeTableCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Life-time Table (${lifetimeTableInput.value}): ${lifetimeTableCost} Php</li>`;
            }
            const lifetimeChairCost = lifetimeChairInput.value * 50;
            if (lifetimeChairInput.value > 0) {
                totalCost += lifetimeChairCost;
                receiptList.innerHTML += `<li>Life-time Chair (${lifetimeChairInput.value}): ${lifetimeChairCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Life-time Chair (${lifetimeChairInput.value}): ${lifetimeChairCost} Php</li>`;
            }
            const longTableCost = longTableInput.value * 200;
            if (longTableInput.value > 0) {
                totalCost += longTableCost;
                receiptList.innerHTML += `<li>Long Table (${longTableInput.value}): ${longTableCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Long Table (${longTableInput.value}): ${longTableCost} Php</li>`;
            }
            const monoblockChairCost = monoblockChairInput.value * 10;
            if (monoblockChairInput.value > 0) {
                totalCost += monoblockChairCost;
                receiptList.innerHTML += `<li>Monoblock Chair (${monoblockChairInput.value}): ${monoblockChairCost} Php</li>`;
                document.getElementById('receiptBreakdown').innerHTML += `<li>Monoblock Chair (${monoblockChairInput.value}): ${monoblockChairCost} Php</li>`;
            }

            // Display total cost
            receiptList.innerHTML += `<li><strong>Total Cost: ${totalCost} Php</strong></li>`;
            document.getElementById('receiptTotal').textContent = totalCost;
        }

        // Recalculate total cost on input changes
        startTimeInput.addEventListener('change', calculateTotal);
        endTimeInput.addEventListener('change', calculateTotal);
        facilityTypeInput.addEventListener('change', calculateTotal);
        withAirconCheckbox.addEventListener('change', calculateTotal);
        rooftopOptionCheckbox.addEventListener('change', calculateTotal);
        groupOver50Checkbox.addEventListener('change', calculateTotal);
        soundSystemCheckbox.addEventListener('change', calculateTotal);
        projectorCheckbox.addEventListener('change', calculateTotal);
        lifetimeTableInput.addEventListener('input', calculateTotal);
        lifetimeChairInput.addEventListener('input', calculateTotal);
        longTableInput.addEventListener('input', calculateTotal);
        monoblockChairInput.addEventListener('input', calculateTotal);

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

        // Handle reservation confirmation
        confirmReservationButton.addEventListener('click', () => {
            const formData = new FormData(document.getElementById('facilitiesReservationForm'));

            fetch('process_facilities_reservation.php', {
                method: 'POST',
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Close the guidelines modal
                        const guidelinesModal = bootstrap.Modal.getInstance(document.getElementById('guidelinesModal'));
                        guidelinesModal.hide();

                        // Show success modal with the reservation ID
                        document.getElementById('controlNumber').textContent = data.reservation_id; // Set the control number
                        // Set the download link for the PDF receipt
                        const downloadBtn = document.getElementById('downloadPdfReceipt');
                        downloadBtn.href = 'download_facility_receipt.php?id=' + encodeURIComponent(data.reservation_id);
                        downloadBtn.style.display = 'inline-block';
                        successModal.show();
                    } else {
                        feedbackMessage.textContent = data.message;
                        feedbackMessage.style.display = 'block';
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    feedbackMessage.textContent = 'An error occurred while processing your reservation.';
                    feedbackMessage.style.display = 'block';
                });
        });

        // Redirect to dashboard
        document.getElementById('redirectToDashboard').addEventListener('click', () => {
            window.location.href = 'dashboard.php';
        });

        // Print receipt functionality
        document.getElementById('printReceiptButton').addEventListener('click', function() {
            const printContents = document.getElementById('printableReceipt').innerHTML;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>