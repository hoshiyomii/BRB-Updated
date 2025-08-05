<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <!-- Custom overlay for blur and darkness -->
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center modal-outline modal-glow">
      <div class="modal-header border-0 justify-content-center"></div>
      <div class="modal-body">
        <!-- Warning Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" class="mb-4" viewBox="0 0 16 16">
          <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
        </svg>
        <div class="mb-3 fs-5 text-secondary">Are you sure you want to log out?</div>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-secondary me-2 px-4" data-bs-dismiss="modal">Cancel</button>
        <a href="logout.php" class="btn btn-danger px-4">Yes, Log-out</a>
      </div>
    </div>
  </div>
</div>
<style>
/* Custom overlay for blur and blue-black darkness */
.custom-modal-bg {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  z-index: 1050;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(5px) saturate(160%);
  display: none;
  transition: opacity 0.3s;
}
/* Show the overlay when the modal is open */
.modal.show ~ .custom-modal-bg,
.custom-modal-bg.active {
  display: block;
}
.modal-outline {
  border: 2px solid #356afd;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById('logoutModal');
  var overlay = document.querySelector('.custom-modal-bg');
  if (modal && overlay) {
    modal.addEventListener('show.bs.modal', function () {
      overlay.classList.add('active');
    });
    modal.addEventListener('hide.bs.modal', function () {
      overlay.classList.remove('active');
    });
  }
});
</script>
