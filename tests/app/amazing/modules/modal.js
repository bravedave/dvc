const modal = params => {

  const options = {
    title: 'string' == typeof params ? params : 'Modal',
    size: 'modal-fullscreen-sm-down',
    ...params
  };

  /**
   * Key Guarantees
   * +---------------------------+-----------------------------------+------------------------
   * | Script/Loading Position	 | Execution Timing	                 | Available in Modules?
   * +---------------------------+-----------------------------------+------------------------
   * | jQuery (in <head>)	       | Blocks HTML until loaded          | ✅ Yes
   * | _brayworth_ (in <head>)	 | Blocks HTML until loaded          | ✅ Yes
   * | Bootstrap (end of <body>) | Runs immediately before DOM ready | ✅ Yes
   * | ES Module (type="module") | Runs after DOM parsed             | ✅ Can safely use both
   * +---------------------------+-----------------------------------+------------------------
   */

  // console.log(
  //   "jQuery:", $ !== undefined,
  //   "Bootstrap:", bootstrap !== undefined,
  //   "Brayworth:", _brayworth_ !== undefined,
  // );

  // a random string
  const uid = Math.random().toString(36).slice(2);

  const modal = document.createElement('div');
  modal.className = `modal fade`;
  modal.role = "dialog";
  modal.id = `${uid}-modal`;
  modal.ariaLabelledby = `${uid}-modal-label`;
  modal.tabIndex = -1;

  modal.innerHTML =
    `<div class="modal-dialog modal-dialog-centered ${options.size}">
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