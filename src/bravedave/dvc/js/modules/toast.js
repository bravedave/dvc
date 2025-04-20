/**
 * create a simple boostrap5 toast and display it
 * put it top right and close it after 5 seconds
 * @param {string} message - The message to display in the toast.
 * @param {string} [type] - The type of toast (success, error, info, warning).
 */

const Toast = (message, type) => {

  // Create the toast element
  const toast = document.createElement('div');
  toast.className = `toast text-bg-${type || 'info'} position-absolute`;
  toast.role = "alert";
  toast.ariaLive = "assertive";
  toast.ariaAtomic = "true";
  toast.dataset.bsAutohide = "false"
  toast.style.top = '20px';
  toast.style.right = '20px';
  toast.style.zIndex = '9999';
  toast.innerHTML = `
      <div class="toast-body">
        <div class="d-flex">
          <div class="me-auto">${message}</div>
          <button type="button" class="btn-close btn-close-white"
            data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>`;
  document.body.appendChild(toast);

  // Initialize Bootstrap toast
  const bsToast = new bootstrap.Toast(toast, {
    autohide: true,
    delay: 5000
  });

  bsToast.show(); // Show the toast

  // Remove the toast from the DOM after it is hidden
  toast.addEventListener('hidden.bs.toast', () => toast.remove());
};

// export the function
export { Toast };

// example usage
// Toast('This is a toast message', 'success');