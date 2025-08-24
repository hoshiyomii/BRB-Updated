document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const errorLogin = document.getElementById("errorLogin");

        if (!username || !password) {
            errorLogin.textContent = "Username and password are required.";
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
                errorLogin.textContent = "Invalid credentials.";
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});
