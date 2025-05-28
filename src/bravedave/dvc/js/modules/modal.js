/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * import( '/js/modal').then( ({ modal }) => modal({title : 'Hello World!'}));
 * */

const _ = _brayworth_;

function modal({
  title = 'Modal title',
  theme = 'text-bg-primary',
  size = 'modal-fullscreen-sm-down',
  show = () => { },
  shown = () => { },
  hide = () => { },
  hidden = () => { },
} = {}) {

  const id = _.randomString();
  const bs = _.bootstrap.version() < 5 ? '' : 'bs-';

  const mountPoint = $('<div></div>');
  mountPoint.append(`<div class="modal fade" id="m${id}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog ${size} modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header ${theme}">
            <h5 class="modal-title">${title}</h5>
            <button type="button" class="btn-close btn-close-white" data-${bs}dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-${bs}dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>`)
    .appendTo(document.body);

    if ( _.bootstrap.version() < 5) {

      const mdl = $(`#m${id}`);

      if (typeof show === 'function') mdl.on('show.bs.modal', show);
      if (typeof shown === 'function') mdl.on('shown.bs.modal', shown);
      if (typeof hide === 'function') mdl.on('hide.bs.modal', hide);
      if (typeof hidden === 'function') mdl.on('hidden.bs.modal', hidden);

      mdl.one('hidden.bs.modal', () => {
        mountPoint.empty();
        mountPoint.remove();
      });

      mdl.modal('show');
      return mdl;
    }

    const modalEl = document.getElementById(`m${id}`);
    const mdl = new bootstrap.Modal(modalEl);

    if (typeof show === 'function') modalEl.addEventListener('show.bs.modal', show, { bubbles: true });
    if (typeof shown === 'function') modalEl.addEventListener('shown.bs.modal', shown, { bubbles: true });
    if (typeof hide === 'function') modalEl.addEventListener('hide.bs.modal', hide, { bubbles: true });
    if (typeof hidden === 'function') modalEl.addEventListener('hidden.bs.modal', hidden, { bubbles: true });

    modalEl.addEventListener('hidden.bs.modal', () => {
      mountPoint.empty();
      mountPoint.remove();
    }, { once: true });

    mdl.show();
    return mdl;
}

export { modal };