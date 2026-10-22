<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc\esse\render6;

use bravedave\dvc\strings;
use function bravedave\dvc\esc;

$asideId = $this->data->asideId;
?>

<nav class="navbar bg-1 fg-2">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">Navbar</span>
    <button type="button" class="btn btn-outline-secondary bs6-aside-toggle"
      id="<?= $toggleId = strings::rand() ?>" aria-label="Toggle navigation" title="Toggle navigation"
      aria-controls="<?= esc($asideId) ?>" aria-expanded="false" disabled>
      <i class="bi bi-list" aria-hidden="true"></i>
    </button>
  </div>
  <script type="module">
    import { Collapse } from '@bootstrap';

    const toggle = document.getElementById('<?= $toggleId ?>');
    const aside = document.querySelector('.render6 [data-role="content-secondary"]');
    if (aside) {
      aside.id = toggle.getAttribute('aria-controls');
      const collapse = Collapse.getOrCreateInstance(aside);
      const viewport = window.matchMedia('(min-width: 768px)');
      const sync = () => toggle.setAttribute('aria-expanded', String(viewport.matches || aside.classList.contains('show')));
      toggle.disabled = false;
      toggle.addEventListener('click', () => collapse.toggle());
      aside.addEventListener('shown.bs.collapse', sync);
      aside.addEventListener('hidden.bs.collapse', sync);
      aside.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !viewport.matches) {
          collapse.hide();
          toggle.focus();
        }
      });
      viewport.addEventListener('change', sync);
      sync();
    } else {
      toggle.hidden = true;
    }
  </script>
</nav>