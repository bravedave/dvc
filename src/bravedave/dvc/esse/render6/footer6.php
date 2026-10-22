<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc\esse\render6;

use bravedave\dvc\strings;

?>

<footer class="container-fluid border-top py-3 fg-2"
  id="<?= $_uid = strings::rand() ?>">
  <div class="row">

    <div class="col js-left"></div>

    <div class="col text-center">
      <small>Bootstrap 6 demo</small>
    </div>

    <div class="col text-end js-right"></div>
  </div>
  <script type="module">
    import {
      Alert
    } from '@bootstrap';

    const version = () => {
      if (/^6/.test(Alert.VERSION)) return 6;
      return 0;
    };

    const _ = _brayworth_;
    const footer = $('#<?= $_uid ?>');

    footer.find('.js-left').text(`Bootstrap ${version()}`);
    footer.find('.js-right').text('Right content');
  </script>
</footer>