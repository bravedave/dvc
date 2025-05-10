<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace amazing;

use bravedave\dvc\{strings, theme};

?>
<div id="<?= $_container = strings::rand() ?>">
  <h5>Bootstrap 5 Modal</h1>
  <p><em><small>Bootstrap 5 modal with form</small></em></p>
  <button type="button" class="btn btn-primary js-demo">
    Launch demo modal
  </button>
</div>

<script type="module">
  
  // prettier-ignore
  import { h, render } from 'preact';
  import htm from 'htm';
  import { modal } from '/<?= $this->route  ?>/js/modal';
  import { renderToNode } from '/<?= $this->route  ?>/js/utility';

  const html = htm.bind(h);
  const container = document.getElementById('<?= $_container ?>');
  const _ = _brayworth_;

  container.querySelector('.js-demo').addEventListener('click', e => {

    _.hideContexts(e);

    const modalInstance = modal({
      title: 'Send Email',
      theme : '<?= theme::modalHeader() ?>'
    });

    const Form = (props) => {

      const handleSubmit = function(e) {

        e.preventDefault();
        modalInstance.hide();
        // Add any additional logic here
        console.log('Form submitted');
      };

      return html`
        <>
        <form id="${props.uid}" onSubmit=${handleSubmit}>
          <input type="text" name="name" class="form-control" placeholder="Name">
        </form>
        </>
      `;
    };

    modalInstance._element.addEventListener('shown.bs.modal', function(e) {

      const body = this.querySelector('.modal-body');
      const footer = this.querySelector('.modal-footer');
      const uid = _.randomString();
      render(html`<${Form} uid=${uid} />`, body);

      footer.appendChild(renderToNode(
        html`<button type="submit" class="btn btn-primary" form="${uid}">Submit</button>`
      ));

      this.querySelector('input[name="name"]').focus();
    });
  });
</script>