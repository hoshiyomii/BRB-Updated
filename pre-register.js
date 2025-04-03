document.addEventListener("DOMContentLoaded", function () {
    const preRegisterBtn = document.getElementById("preRegisterBtn");

    if (preRegisterBtn) {
        preRegisterBtn.addEventListener("click", function () {
            const eventId = preRegisterBtn.dataset.id; // More reliable way
            console.log("DEBUG: Button dataset-id =", preRegisterBtn.dataset.id);


            console.log("DEBUG: Sending event_id =", eventId); // Debug log

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

                if (data.includes("success")) {
                    alert("Pre-registration successful!");
                    location.reload(); // Refresh page
                } else if (data.includes("already_registered")) {
                    alert("You have already pre-registered for this event.");
                } else if (data.includes("event_full")) {
                    alert("This event is full.");
                } else if (data.includes("not_logged_in")) {
                    alert("You need to log in first.");
                    window.location.href = "user_login.php";
                } else {
                    alert("An error occurred: " + data);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }
});
