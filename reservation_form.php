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
                
                <!-- Printable Receipt Section -->
                <div id="printableReceipt" class="mt-4 p-3 border rounded">
                    <div class="text-center mb-3">
                        <h4>Barangay Blue Ridge B</h4>
                        <h5>Sports Venue Reservation Receipt</h5>
                        <p class="mb-1">Date: <span id="currentDate"></span></p>
                        <p>Name: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <hr>
                    </div>
                    
                    <div>
                        <p><strong>Venue Type:</strong> <span id="receiptVenueType"></span></p>
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

                // Update receipt breakdown with proper spacing around the colon
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
            // Clear form fields
            reservationForm.reset();
            securityOptionContainer.style.display = 'none';
            caretakerOptionContainer.style.display = 'none';
            confirmReservationButton.disabled = true;
            
            // Clear session storage
            sessionStorage.removeItem('reservationStartTime');
            sessionStorage.removeItem('reservationEndTime');
            sessionStorage.removeItem('reservationVenueType');
            
            // Redirect to dashboard
            window.location.href = 'dashboard.php';
        });

        // Handle reservation confirmation
        confirmReservationButton.addEventListener('click', () => {
            // Make sure the total is calculated and receipt is up to date
            calculateTotal();
            
            // Store current form values in session storage for receipt
            sessionStorage.setItem('reservationStartTime', startTimeInput.value);
            sessionStorage.setItem('reservationEndTime', endTimeInput.value);
            sessionStorage.setItem('reservationVenueType', venueTypeInput.options[venueTypeInput.selectedIndex].text);
            
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

                        // Note: We'll clear the form after the modal is closed
                        // We don't reset here to ensure data is available for the receipt
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
            headerSubtitle.textContent = 'Sports Venue Reservation Receipt';
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
            
            // Venue type
            const venueItem = document.createElement('div');
            venueItem.classList.add('receipt-item');
            venueItem.innerHTML = '<span class="receipt-item-label"><strong>Venue Type:</strong></span>' + 
                                '<span class="receipt-item-value">' + document.getElementById('receiptVenueType').textContent + '</span>';
            
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
            
            receiptDetails.appendChild(venueItem);
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
            
            // Get stored venue type
            const venueTypeText = sessionStorage.getItem('reservationVenueType');
            document.getElementById('receiptVenueType').textContent = venueTypeText;
            
            // Get stored reservation date and time
            const startTimeValue = sessionStorage.getItem('reservationStartTime');
            const endTimeValue = sessionStorage.getItem('reservationEndTime');
            
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
    });
</script>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>