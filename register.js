document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");
    let errorMessage = document.getElementById("errorMsg");

    // Retry mechanism to ensure the element is found
    if (!errorMessage) {
        console.error("Error message element not found on initial load. Retrying...");
        setTimeout(() => {
            errorMessage = document.getElementById("errorMsg");
            console.log("Retrying to find errorMsg:", errorMessage);
        }, 100); // Retry after 100ms
    }

    if (!registerForm) {
        console.error("Register form not found!");
        return;
    }

    registerForm.addEventListener("submit", function (event) {
        const phoneNumber = document.getElementById("phone_number");
        const numericPattern = /^[0-9]+$/;

        // Validate Phone Number
        if (!numericPattern.test(phoneNumber.value)) {
            event.preventDefault(); // Prevent form submission
            if (errorMessage) {
                errorMessage.textContent = "Phone Number must contain only numbers.";
            } else {
                console.error("Error message element is still null!");
            }
            return;
        }

        // Clear error message if valid
        if (errorMessage) {
            errorMessage.textContent = "";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const backButton = document.getElementById("backButton");
    if (backButton) {
        backButton.addEventListener("click", function () {
            window.history.back();
        });
    }
});


