document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");
    let errorMsg = document.getElementById("errorMsg");

    // Retry mechanism to ensure the element is found
    if (!errorMsg) {
        console.error("Error message element not found on initial load. Retrying...");
        setTimeout(() => {
            errorMsg = document.getElementById("errorMsg");
            console.log("Retrying to find errorMsg:", errorMsg);
        }, 100); // Retry after 100ms
    }

    if (!registerForm) {
        console.error("Register form not found!");
        return;
    }

    registerForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const username = registerForm.username.value.trim();
        const password = registerForm.password.value;
        const confirmPassword = registerForm.confirm_password.value;
        const birthdate = new Date(registerForm.birthdate.value);
        const today = new Date();
        const minAge = 13; // Updated minimum age
        let error = "";

        // Username validation
        if (!/^[a-zA-Z0-9_]{5,16}$/.test(username)) { // Ensure username is between 5 and 16 characters
            error = "Username must be between 5 and 16 characters and contain only letters, numbers, or underscores.";
        }
        // Password validation
        else if (password.length < 8) {
            error = "Password must be at least 8 characters.";
        }
        // Confirm password
        else if (password !== confirmPassword) {
            error = "Passwords do not match.";
        }
        // Birthdate validation
        else {
            const age = today.getFullYear() - birthdate.getFullYear();
            const m = today.getMonth() - birthdate.getMonth();
            const day = today.getDate() - birthdate.getDate();
            let realAge = age;
            if (m < 0 || (m === 0 && day < 0)) realAge--;
            if (birthdate > today) {
                error = "Birthdate cannot be in the future.";
            } else if (realAge < minAge) {
                error = "You must be at least 13 years old to register."; // Updated error message
            }
        }
        // Phone number validation
        const phoneNumber = registerForm.phone_number.value.trim();
        if (!/^\d{11}$/.test(phoneNumber)) {
            error = "Phone number must contain exactly 11 digits.";
        }

        if (error) {
            errorMsg.textContent = error;
            return;
        }

        const formData = new FormData(registerForm);

        fetch("process_register_user.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes("Registration successful")) {
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('registerSuccessModal'));
                modal.show();
                errorMsg.textContent = "";
            } else {
                errorMsg.textContent = data;
            }
        })
        .catch(error => {
            errorMsg.textContent = "An error occurred. Please try again.";
        });
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

document.addEventListener("DOMContentLoaded", function () {
    const usernameInput = document.querySelector('input[name="username"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    const birthdateInput = document.querySelector('input[name="birthdate"]');
    const errorMsg = document.getElementById("errorMsg");

    // Username live validation
    usernameInput.addEventListener("input", function () {
        if (!/^[a-zA-Z0-9_]{5,16}$/.test(usernameInput.value)) {
            errorMsg.textContent = "Username must be at least 5-16 characters and contain only letters, numbers, or underscores.";
        } else {
            errorMsg.textContent = "";
        }
    });

    // Password live validation
    passwordInput.addEventListener("input", function () {
        if (passwordInput.value.length < 8) {
            errorMsg.textContent = "Password must be at least 8 characters.";
        } else {
            errorMsg.textContent = "";
        }
    });

    // Confirm password live validation
    confirmPasswordInput.addEventListener("input", function () {
        if (confirmPasswordInput.value !== passwordInput.value) {
            errorMsg.textContent = "Passwords do not match.";
        } else {
            errorMsg.textContent = "";
        }
    });

    // Birthdate live validation
    birthdateInput.addEventListener("change", function () {
        const today = new Date();
        const birthdate = new Date(birthdateInput.value);
        const minAge = 13;
        let age = today.getFullYear() - birthdate.getFullYear();
        const m = today.getMonth() - birthdate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }
        if (birthdate > today) {
            errorMsg.textContent = "Birthdate cannot be in the future.";
        } else if (age < minAge) {
            errorMsg.textContent = "You must be at least 13 years old to register.";
        } else {
            errorMsg.textContent = "";
        }
    });

    // Phone number validation
    const phoneNumberInput = document.querySelector('input[name="phone_number"]');
    phoneNumberInput.addEventListener("input", function () {
        if (!/^\d{11}$/.test(phoneNumberInput.value)) {
            errorMsg.textContent = "Phone number must contain exactly 11 digits.";
        } else {
            errorMsg.textContent = "";
        }
    });

    // Check username availability
    usernameInput.addEventListener("blur", function () {
        const username = usernameInput.value.trim();
        if (username.length >= 5 && username.length <= 16) {
            fetch("check_username.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `username=${encodeURIComponent(username)}`,
            })
                .then((response) => response.text())
                .then((data) => {
                    if (data === "taken") {
                        errorMsg.textContent = "Username is already taken. Please choose another.";
                    } else {
                        errorMsg.textContent = "";
                    }
                })
                .catch((error) => {
                    console.error("Error checking username:", error);
                    errorMsg.textContent = "An error occurred while checking the username.";
                });
        }
    });
});


