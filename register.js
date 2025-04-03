document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");

    registerForm.addEventListener("submit", function (event) {
        const phoneNumber = document.getElementById("phone_number");
        const houseNumber = document.getElementById("house_number");
        const errorMessage = document.getElementById("errorMessage");

        // Regular expression to allow only numbers
        const numericPattern = /^[0-9]+$/;

        // Validate Phone Number
        if (!numericPattern.test(phoneNumber.value)) {
            event.preventDefault(); // Prevent form submission
            errorMessage.textContent = "Phone Number must contain only numbers.";
            return;
        }

        // Validate House Number
        if (!numericPattern.test(houseNumber.value)) {
            event.preventDefault(); // Prevent form submission
            errorMessage.textContent = "House Number must contain only numbers.";
            return;
        }

        errorMessage.textContent = ""; // Clear error message if valid
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


