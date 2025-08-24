document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    fetch("login_handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username: username, password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = "admin_dashboard.php";
        } else {
            document.getElementById("errorMsg").innerText = data.message;
        }
    });
});
