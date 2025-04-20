const modal = params => {

  const options = {
    title: 'string' == typeof params ? params : 'Modal',
    size : 'modal-fullscreen-sm-down',
    ...params
  };

  // a random string
  const uid = Math.random().toString(36).slice(2);

  const modal = document.createElement('div');
  modal.className = `modal fade`;
  modal.role = "dialog";
  modal.id = `${uid}-modal`;
  modal.ariaLabelledby = `${uid}-modal-label`;


  modal.innerHTML = `
    <div class="" tabindex="-1" role="" id="${uid}-modal" aria-labelledby="${uid}-modal-label">
      <div class="modal-dialog ${options.size} modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header text-bg-primary">
            <h5 class="modal-title" id="${uid}-modal-label">${options.title}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"></button>
          </div>
          <div class="modal-body">...</div>
          <div class="modal-footer">
            <div class="js-message"></div>
          </div>
        </div>
      </div>
    </div>`;

  document.body.appendChild(modal);

  // Initialize Bootstrap toast
  const bsModal = new bootstrap.Modal(modal);

  bsModal.show(); // Show the modal

  // Remove the modal from the DOM after it is hidden
  modal.addEventListener('hidden.bs.modal', () => modal.remove());
  return modal;
};

export { modal };