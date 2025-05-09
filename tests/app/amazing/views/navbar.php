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

use bravedave\dvc\{strings, theme}; ?>

<div id="<?= $_navbar = strings::rand() ?>"></div>
<script type="module">
  import {
    h,
    render
  } from 'preact';
  import htm from 'htm';
  import Navbar from '/<?= $this->route ?>/js/navbar';

  const html = htm.bind(h);

  render(html`<${Navbar} 
    title="<?= htmlentities($this->title) ?>" 
    theme="<?= theme::navbar() ?>" />`,
    document.getElementById('<?= $_navbar ?>'));
</script>