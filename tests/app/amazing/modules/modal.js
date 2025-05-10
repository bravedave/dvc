
import { h, render } from 'preact';
import htm from 'htm';
const html = htm.bind(h);

const _ = _brayworth_;

function modal({
  title = 'Modal title',
  theme = 'text-bg-primary',
  size = 'modal-fullscreen-sm-down',
}) {

  // console.log(children);

  const id = _.randomString();

  const mountPoint = document.createElement('div');
  document.body.appendChild(mountPoint);

  render(html`<div class="modal fade" id="m${id}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog ${size} modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header ${theme}">
            <h5 class="modal-title">${title}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>`, mountPoint);

  const modalEl = document.getElementById(`m${id}`);
  const mdl = new bootstrap.Modal(modalEl);
  mdl.show();

  modalEl.addEventListener('hidden.bs.modal', () => {
    render(null, mountPoint);
    mountPoint.remove();
  }, { once: true });

  return mdl;
}

export { modal };