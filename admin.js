document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("logoutModal");
    const overlay = document.querySelector(".custom-modal-bg");
    if (modal && overlay) {
        modal.addEventListener("show.bs.modal", function () {
            overlay.classList.add("active");
        });
        modal.addEventListener("hide.bs.modal", function () {
            overlay.classList.remove("active");
        });
    }
});