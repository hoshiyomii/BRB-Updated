document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    let errorLogin = document.getElementById("errorMsg"); // Updated to match the ID in user_login.php

    // Retry mechanism to ensure the element is found
    if (!errorLogin) {
        console.error("Error message element not found on initial load. Retrying...");
        setTimeout(() => {
            errorLogin = document.getElementById("errorMsg");
            if (!errorLogin) {
                console.error("Error message element still not found after retry.");
            } else {
                console.log("Retrying to find errorMsg:", errorLogin);
            }
        }, 500); // Retry after 500ms
    }

    if (!loginForm) {
        console.error("Login form not found!");
        return;
    }

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!username || !password) {
            if (errorLogin) {
                errorLogin.textContent = "Username and password are required.";
            } else {
                console.error("Error message element is still null!");
            }
            return;
        }

        // Send login data using Fetch API
        fetch("process_user_login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`,
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                window.location.href = "index.php"; // Redirect to homepage after login
            } else {
                if (errorLogin) {
                    errorLogin.textContent = data.trim(); // Show the actual error from PHP
                } else {
                    console.error("Error message element is still null!");
                }
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});
