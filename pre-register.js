document.addEventListener("DOMContentLoaded", function () {
    const preRegisterBtn = document.getElementById("preRegisterBtn");
    const preRegisterModal = new bootstrap.Modal(document.getElementById("preRegisterModal"));
    const eventNameElement = document.getElementById("eventName");
    const eventActiveUntilElement = document.getElementById("eventActiveUntil");
    const preRegisterMessage = document.getElementById("preRegisterMessage");
    const confirmPreRegisterBtn = document.getElementById("confirmPreRegisterBtn");

    if (preRegisterBtn) {
        preRegisterBtn.addEventListener("click", function () {
            const eventId = preRegisterBtn.dataset.id;
            const eventName = preRegisterBtn.dataset.name;
            const eventActiveUntil = preRegisterBtn.dataset.activeUntil;

            // Populate modal with event details
            eventNameElement.textContent = eventName;
            eventActiveUntilElement.textContent = eventActiveUntil;
            preRegisterMessage.textContent = ""; // Clear any previous messages

            // Show the modal
            preRegisterModal.show();

            // Handle confirmation
            confirmPreRegisterBtn.onclick = function () {
                fetch("process_pre_register.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "event_id=" + encodeURIComponent(eventId)
                })
                .then(response => response.text())
                .then(data => {
                    console.log("DEBUG: Response from server =", data); // Debug response

                    // Display appropriate message in the modal
                    if (data.includes("success")) {
                        preRegisterMessage.textContent = "Pre-registration successful!";
                        preRegisterMessage.className = "text-success";
                    } else if (data.includes("already_registered")) {
                        preRegisterMessage.textContent = "Already registered!";
                        preRegisterMessage.className = "text-warning";
                    } else if (data.includes("event_full")) {
                        preRegisterMessage.textContent = "Event is already full.";
                        preRegisterMessage.className = "text-danger";
                    } else if (data.includes("not_logged_in")) {
                        preRegisterMessage.textContent = "You need to log in first.";
                        preRegisterMessage.className = "text-danger";
                        setTimeout(() => {
                            window.location.href = "user_login.php";
                        }, 2000);
                        return; // Exit early to avoid hiding the modal
                    } else {
                        preRegisterMessage.textContent = "An error occurred: " + data;
                        preRegisterMessage.className = "text-danger";
                    }

                    // Add a timeout to hide the modal and reset the message
                    setTimeout(() => {
                        preRegisterModal.hide(); // Hide the modal
                        preRegisterMessage.textContent = ""; // Clear the message
                    }, 3000); // 3 seconds timeout
                })
                .catch(error => {
                    console.error("Error:", error);
                    preRegisterMessage.textContent = "An unexpected error occurred.";
                    preRegisterMessage.className = "text-danger";

                    // Add a timeout to hide the modal and reset the message
                    setTimeout(() => {
                        preRegisterModal.hide(); // Hide the modal
                        preRegisterMessage.textContent = ""; // Clear the message
                    }, 3000); // 3 seconds timeout
                });
            };
        });
    }
});
