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
<style>
    /* Success modal scrolling fix */
    .modal-dialog {
        max-height: 90vh;
    }
    
    .modal-content {
        max-height: 90vh;
    }
    
    .modal-body {
        max-height: calc(90vh - 120px); /* subtract header and footer height */
        overflow-y: auto;
    }
    
    /* Hidden print container */
    #printContainer {
        display: none;
    }
    
    @page {
        margin: 0;
        size: auto;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        
        #printContainer, #printContainer * {
            display: block !important;
            visibility: visible !important;
        }
        
        #printContainer {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            padding: 20px;
            margin: 0;
        }
        
        /* Style for receipt items in print view */
        #printContainer .receipt-item {
            display: flex !important;
            justify-content: space-between !important;
            margin-bottom: 2px !important;
        }
        
        #printContainer .receipt-item-label,
        #printContainer .receipt-item-value {
            display: inline-block !important;
        }
        
        #printContainer .receipt-header {
            margin-bottom: 20px !important;
        }
        
        #printContainer .receipt-section {
            margin-bottom: 10px !important;
        }
        
        #printContainer .receipt-total {
            font-weight: bold !important;
            margin-top: 5px !important;
            border-top: 1px solid #ddd !important;
            padding-top: 5px !important;
        }
        
        /* Hide modal elements during print */
        .modal, .modal-dialog, .modal-content, .modal-body {
            display: none;
        }
    }
</style>
<body>
<!-- Print Container -->
<div id="printContainer"></div>
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
                                    <option value="Multi Purpose Hall">Multi Purpose Hall (Bulwagan)</option>
                                    <option value="Community Center">Community Center</option>
                                    <option value="Session Hall">Session Hall</option>
                                    <option value="Conference Room">Conference Room</option>
                                    <option value="Small Meeting Room">Small Meeting Room</option>
                                </select>
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
                            <div id="approvedListContainer" class="alert alert-info mt-2" style="display:none;"></div>
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
                
                <!-- Printable Receipt Section -->
                <div id="printableReceipt" class="mt-4 p-3 border rounded">
                    <div class="text-center mb-3">
                        <h4>Barangay Blue Ridge B</h4>
                        <h5>Facilities Reservation Receipt</h5>
                        <p class="mb-1">Date: <span id="currentDate"></span></p>
                        <p>Name: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <hr>
                    </div>
                    
                    <div>
                        <p><strong>Facility Type:</strong> <span id="receiptFacilityType"></span></p>
                        <p><strong>Date:</strong> <span id="receiptDate"></span></p>
                        <p><strong>Time:</strong> <span id="receiptTime"></span></p>
                        <p><strong>Control Number:</strong> <span id="receiptControlNumber"></span></p>
                    </div>
                    
                    <div id="receiptDetails" class="mt-3">
                        <!-- Receipt details will be populated dynamically -->
                    </div>
                    
                    <div class="mt-3">
                        <p class="font-italic">This is not an official receipt. Please proceed to the barangay hall to complete your payment.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" id="printButton">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
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
        const approvedListContainer = document.getElementById('approvedListContainer');

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
                if (extraHours > 0) {
                    receiptList.innerHTML += `<li>Extra Hours (${extraHours} hours): ${extraHours * extraHourCost} Php</li>`;
                }

                // Add mandatory charges
                totalCost += 1000; // Cash bond
                totalCost += 250; // Security/Parking Assistance
                if (groupOver50Checkbox.checked) totalCost += 250; // Group over 50 guests
                totalCost += 250; // Caretaker/Cleaning Post Event
                totalCost += 100; // Sound system setup operator

                receiptList.innerHTML += `<li>Cash Bond: 1000 Php</li>`;
                receiptList.innerHTML += `<li>Security/Parking Assistance: 250 Php</li>`;
                if (groupOver50Checkbox.checked) {
                    receiptList.innerHTML += `<li>Group Over 50 Guests: 250 Php</li>`;
                }
                receiptList.innerHTML += `<li>Caretaker/Cleaning Post Event: 250 Php</li>`;
                receiptList.innerHTML += `<li>Sound System Setup Operator: 100 Php</li>`;

                // Add Rooftop Option cost if selected
                if (rooftopOptionCheckbox.checked) {
                    const rooftopCost = hours * 600; // 600 Php/hour
                    totalCost += rooftopCost;
                    receiptList.innerHTML += `<li>Rooftop Option (${hours} hours): ${rooftopCost} Php</li>`;
                }
            } else if (facilityTypeInput.value === 'Session Hall') {
                // Session Hall logic
                totalCost = hours * 600; // 600 Php/hour
                receiptList.innerHTML += `<li>Session Hall (${hours} hours): ${totalCost} Php</li>`;
            } else if (facilityTypeInput.value === 'Conference Room') {
                // Conference Room logic
                totalCost = hours * 400; // 400 Php/hour
                receiptList.innerHTML += `<li>Conference Room (${hours} hours): ${totalCost} Php</li>`;
            } else if (facilityTypeInput.value === 'Small Meeting Room') {
                // Small Meeting Room logic
                totalCost = hours * 200; // 200 Php/hour
                receiptList.innerHTML += `<li>Small Meeting Room (${hours} hours): ${totalCost} Php</li>`;
            }

            // Add additional costs
            if (soundSystemCheckbox.checked) {
                totalCost += 1000;
                receiptList.innerHTML += `<li>Sound System: 1000 Php</li>`;
            }
            if (projectorCheckbox.checked) {
                totalCost += 1500;
                receiptList.innerHTML += `<li>Projector With Screen: 1500 Php</li>`;
            }
            const lifetimeTableCost = lifetimeTableInput.value * 150;
            if (lifetimeTableInput.value > 0) {
                totalCost += lifetimeTableCost;
                receiptList.innerHTML += `<li>Life-time Table (${lifetimeTableInput.value}): ${lifetimeTableCost} Php</li>`;
            }
            const lifetimeChairCost = lifetimeChairInput.value * 50;
            if (lifetimeChairInput.value > 0) {
                totalCost += lifetimeChairCost;
                receiptList.innerHTML += `<li>Life-time Chair (${lifetimeChairInput.value}): ${lifetimeChairCost} Php</li>`;
            }
            const longTableCost = longTableInput.value * 200;
            if (longTableInput.value > 0) {
                totalCost += longTableCost;
                receiptList.innerHTML += `<li>Long Table (${longTableInput.value}): ${longTableCost} Php</li>`;
            }
            const monoblockChairCost = monoblockChairInput.value * 10;
            if (monoblockChairInput.value > 0) {
                totalCost += monoblockChairCost;
                receiptList.innerHTML += `<li>Monoblock Chair (${monoblockChairInput.value}): ${monoblockChairCost} Php</li>`;
            }

            // Display total cost
            receiptList.innerHTML += `<li><strong>Total Cost: ${totalCost} Php</strong></li>`;
        }

        // --- Show approved reservations for the selected day ---
        function fetchApprovedReservations() {
            approvedListContainer.style.display = 'none';
            approvedListContainer.innerHTML = '';
            const facility = facilityTypeInput.value;
            const start = startTimeInput.value;
            if (!facility || !start) return;
            const date = new Date(start);
            if (isNaN(date.getTime())) return;
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            const dayStr = `${yyyy}-${mm}-${dd}`;
            fetch(`get_approved_reservations.php?facility_type=${encodeURIComponent(facility)}&date=${dayStr}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.reservations.length > 0) {
                        let html = '<strong>Approved Reservations for this day:</strong><ul class="mb-0">';
                        data.reservations.forEach(r => {
                            const st = new Date(r.start_time);
                            const et = new Date(r.end_time);
                            html += `<li>${st.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${et.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</li>`;
                        });
                        html += '</ul>';
                        approvedListContainer.innerHTML = html;
                        approvedListContainer.style.display = 'block';
                    } else {
                        approvedListContainer.style.display = 'none';
                        approvedListContainer.innerHTML = '';
                    }
                })
                .catch(() => {
                    approvedListContainer.style.display = 'none';
                    approvedListContainer.innerHTML = '';
                });
        }
        startTimeInput.addEventListener('change', fetchApprovedReservations);
        facilityTypeInput.addEventListener('change', fetchApprovedReservations);

        // --- Prevent double booking (conflict check) ---
        async function checkReservationConflict() {
            const facility = facilityTypeInput.value;
            const start = startTimeInput.value;
            const end = endTimeInput.value;
            if (!facility || !start || !end) return;
            try {
                const params = new URLSearchParams({
                    facility_type: facility,
                    start_time: start,
                    end_time: end
                });
                const res = await fetch('check_reservation_conflict.php?' + params.toString());
                const data = await res.json();
                if (data.conflict) {
                    feedbackMessage.textContent = 'There is already an approved reservation for this time slot.';
                    feedbackMessage.style.display = 'block';
                    submitReservationButton.disabled = true;
                    confirmReservationButton.disabled = true;
                } else {
                    feedbackMessage.style.display = 'none';
                    submitReservationButton.disabled = false;
                    confirmReservationButton.disabled = !agreeGuidelinesCheckbox.checked;
                }
            } catch (e) {
                feedbackMessage.textContent = 'Could not check for conflicts. Please try again.';
                feedbackMessage.style.display = 'block';
                submitReservationButton.disabled = true;
                confirmReservationButton.disabled = true;
            }
        }
        startTimeInput.addEventListener('change', checkReservationConflict);
        endTimeInput.addEventListener('change', checkReservationConflict);
        facilityTypeInput.addEventListener('change', checkReservationConflict);

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
            // Make sure the total is calculated and receipt is up to date
            calculateTotal();
            
            // Store current form values in session storage for receipt
            sessionStorage.setItem('facilityReservationStartTime', startTimeInput.value);
            sessionStorage.setItem('facilityReservationEndTime', endTimeInput.value);
            sessionStorage.setItem('facilityReservationType', facilityTypeInput.options[facilityTypeInput.selectedIndex].text);
            
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
                        successModal.show();
                        
                        // Note: We'll clear the form after the modal is closed
                        // We don't reset here to ensure data is available for the receipt
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
        
        // Print functionality
        document.getElementById('printButton').addEventListener('click', () => {
            // Get the print container
            const printContainer = document.getElementById('printContainer');
            
            // Clear it
            printContainer.innerHTML = '';
            
            // Create receipt header
            const receiptHeader = document.createElement('div');
            receiptHeader.classList.add('receipt-header', 'text-center');
            
            const headerTitle = document.createElement('h4');
            headerTitle.textContent = 'Barangay Blue Ridge B';
            headerTitle.style.marginBottom = '5px';
            
            const headerSubtitle = document.createElement('h5');
            headerSubtitle.textContent = 'Facilities Reservation Receipt';
            headerSubtitle.style.marginBottom = '10px';
            
            const headerInfo = document.createElement('div');
            headerInfo.style.display = 'flex';
            headerInfo.style.justifyContent = 'space-between';
            
            const leftInfo = document.createElement('div');
            leftInfo.style.textAlign = 'left';
            
            const currentDate = document.createElement('p');
            currentDate.style.margin = '2px 0';
            currentDate.innerHTML = '<strong>Date:</strong> ' + document.getElementById('currentDate').textContent;
            
            const userName = document.createElement('p');
            userName.style.margin = '2px 0';
            userName.innerHTML = '<strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>';
            
            leftInfo.appendChild(currentDate);
            leftInfo.appendChild(userName);
            
            const rightInfo = document.createElement('div');
            rightInfo.style.textAlign = 'right';
            
            const controlNum = document.createElement('p');
            controlNum.style.margin = '2px 0';
            controlNum.innerHTML = '<strong>Control Number:</strong> ' + document.getElementById('receiptControlNumber').textContent;
            
            rightInfo.appendChild(controlNum);
            
            headerInfo.appendChild(leftInfo);
            headerInfo.appendChild(rightInfo);
            
            const hr = document.createElement('hr');
            hr.style.margin = '10px 0';
            
            receiptHeader.appendChild(headerTitle);
            receiptHeader.appendChild(headerSubtitle);
            receiptHeader.appendChild(headerInfo);
            receiptHeader.appendChild(hr);
            
            // Create receipt details section
            const receiptDetails = document.createElement('div');
            receiptDetails.classList.add('receipt-section');
            
            // Facility type
            const facilityItem = document.createElement('div');
            facilityItem.classList.add('receipt-item');
            facilityItem.innerHTML = '<span class="receipt-item-label"><strong>Facility Type:</strong></span>' + 
                                    '<span class="receipt-item-value">' + document.getElementById('receiptFacilityType').textContent + '</span>';
            
            // Date
            const dateItem = document.createElement('div');
            dateItem.classList.add('receipt-item');
            dateItem.innerHTML = '<span class="receipt-item-label"><strong>Date:</strong></span>' + 
                                '<span class="receipt-item-value">' + document.getElementById('receiptDate').textContent + '</span>';
            
            // Time
            const timeItem = document.createElement('div');
            timeItem.classList.add('receipt-item');
            timeItem.innerHTML = '<span class="receipt-item-label"><strong>Time:</strong></span>' + 
                                '<span class="receipt-item-value">' + document.getElementById('receiptTime').textContent + '</span>';
            
            receiptDetails.appendChild(facilityItem);
            receiptDetails.appendChild(dateItem);
            receiptDetails.appendChild(timeItem);
            
            // Create costs breakdown section
            const costsSection = document.createElement('div');
            costsSection.classList.add('receipt-section');
            costsSection.style.marginTop = '15px';
            
            const costsSectionTitle = document.createElement('h5');
            costsSectionTitle.textContent = 'Cost Breakdown';
            costsSectionTitle.style.marginBottom = '10px';
            
            costsSection.appendChild(costsSectionTitle);
            
            // Get receipt items
            const receiptItems = document.getElementById('receiptDetails').querySelectorAll('ul li');
            
            // Parse and add receipt items
            let isTotal = false;
            receiptItems.forEach(item => {
                const itemText = item.textContent.trim();
                
                // Check if this is a total line
                isTotal = itemText.includes('Total');
                
                // Create item container
                const itemContainer = document.createElement('div');
                itemContainer.classList.add('receipt-item');
                
                if (isTotal) {
                    itemContainer.classList.add('receipt-total');
                }
                
                // Split by colon if it has one
                if (itemText.includes(':')) {
                    const parts = itemText.split(':');
                    const label = parts[0].trim();
                    const value = parts[1].trim();
                    
                    // Make sure we have the colon in the label
                    itemContainer.innerHTML = '<span class="receipt-item-label">' + label + ':</span>' + 
                                            '<span class="receipt-item-value">' + value + '</span>';
                } else {
                    // For items without a colon, try to intelligently split between label and value
                    const match = itemText.match(/^(.*?)(\d+\s*Php\s*)$/);
                    if (match) {
                        const label = match[1].trim();
                        const value = match[2].trim();
                        itemContainer.innerHTML = '<span class="receipt-item-label">' + label + ':</span>' + 
                                                '<span class="receipt-item-value">' + value + '</span>';
                    } else {
                        itemContainer.textContent = itemText;
                    }
                }
                
                costsSection.appendChild(itemContainer);
            });
            
            // Create footer
            const footer = document.createElement('div');
            footer.classList.add('receipt-section');
            footer.style.marginTop = '20px';
            footer.style.fontSize = '12px';
            footer.style.fontStyle = 'italic';
            footer.textContent = 'This is not an official receipt. Please proceed to the barangay hall to complete your payment.';
            
            // Append all sections to print container
            printContainer.appendChild(receiptHeader);
            printContainer.appendChild(receiptDetails);
            printContainer.appendChild(costsSection);
            printContainer.appendChild(footer);
            
            // Print with specific settings
            const printOptions = {
                printBackground: true,
                headerTemplate: ' ',
                footerTemplate: ' '
            };
            
            window.print();
        });
        
        // Populate the receipt when the success modal shows
        successModalElement.addEventListener('shown.bs.modal', (event) => {
            // Set current date on receipt
            const today = new Date();
            const formattedDate = today.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('currentDate').textContent = formattedDate;
            
            // Copy control number to receipt
            const controlNumber = document.getElementById('controlNumber').textContent;
            document.getElementById('receiptControlNumber').textContent = controlNumber;
            
            // Get stored facility type
            const facilityTypeText = sessionStorage.getItem('facilityReservationType');
            document.getElementById('receiptFacilityType').textContent = facilityTypeText;
            
            // Get stored reservation date and time
            const startTimeValue = sessionStorage.getItem('facilityReservationStartTime');
            const endTimeValue = sessionStorage.getItem('facilityReservationEndTime');
            
            if (startTimeValue && endTimeValue) {
                const startTime = new Date(startTimeValue);
                const endTime = new Date(endTimeValue);
                
                // Format date
                const reservationDate = startTime.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById('receiptDate').textContent = reservationDate;
                
                // Format time
                const formatTimeStr = (date) => {
                    let hours = date.getHours();
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // the hour '0' should be '12'
                    const minutes = date.getMinutes().toString().padStart(2, '0');
                    return `${hours}:${minutes} ${ampm}`;
                };
                
                const timeRange = `${formatTimeStr(startTime)} - ${formatTimeStr(endTime)}`;
                document.getElementById('receiptTime').textContent = timeRange;
            } else {
                // Fallback if values aren't available
                document.getElementById('receiptDate').textContent = "Not available";
                document.getElementById('receiptTime').textContent = "Not available";
            }
            
            // Copy receipt details directly from the form receipt list
            const receiptDetails = document.getElementById('receiptDetails');
            receiptDetails.innerHTML = '';
            
            // Create details list
            const detailsList = document.createElement('ul');
            detailsList.classList.add('list-unstyled');
            
            // Copy existing receipt items from the form's receipt list
            const existingReceiptItems = document.getElementById('receiptList').getElementsByTagName('li');
            
            // Convert HTMLCollection to Array to use forEach
            Array.from(existingReceiptItems).forEach(item => {
                const itemText = item.textContent.trim();
                
                // Check if this is a total line
                const isTotal = itemText.includes('Total Cost:');
                
                // Split by colon if it has one
                if (itemText.includes(':')) {
                    const parts = itemText.split(':');
                    const label = parts[0].trim();
                    const value = parts[1].trim();
                    
                    // Create a receipt item with the same content
                    addReceiptItem(detailsList, 
                                  isTotal ? 'Total' : label,
                                  value,
                                  isTotal);
                } else {
                    // Just add the item as is
                    const item = document.createElement('li');
                    item.textContent = itemText;
                    detailsList.appendChild(item);
                }
            });
            
            receiptDetails.appendChild(detailsList);
        });
        
        // Helper function to add items to receipt
        function addReceiptItem(list, label, value, isBold = false) {
            const item = document.createElement('li');
            const row = document.createElement('div');
            row.classList.add('d-flex', 'justify-content-between');
            
            const labelSpan = document.createElement('span');
            labelSpan.textContent = label;
            
            const valueSpan = document.createElement('span');
            valueSpan.textContent = value;
            
            if (isBold) {
                labelSpan.style.fontWeight = 'bold';
                valueSpan.style.fontWeight = 'bold';
            }
            
            row.appendChild(labelSpan);
            row.appendChild(valueSpan);
            item.appendChild(row);
            list.appendChild(item);
        }
        
        // Redirect to the dashboard when the success modal is closed
        successModalElement.addEventListener('hidden.bs.modal', () => {
            // Clear form fields
            document.getElementById('facilitiesReservationForm').reset();
            
            // Reset displays and states
            if (facilityTypeInput.value !== 'Multi Purpose Hall' && facilityTypeInput.value !== 'Community Center') {
                withAirconCheckbox.parentElement.style.display = 'none';
            }
            
            if (facilityTypeInput.value !== 'Community Center') {
                rooftopOptionCheckbox.parentElement.style.display = 'none';
            }
            
            confirmReservationButton.disabled = true;
            
            // Clear session storage
            sessionStorage.removeItem('facilityReservationStartTime');
            sessionStorage.removeItem('facilityReservationEndTime');
            sessionStorage.removeItem('facilityReservationType');
            
            // Redirect to dashboard
            window.location.href = 'dashboard.php';
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>