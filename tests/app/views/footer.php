<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<footer class="footer-fixed">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col text-muted">
        <em id="<?= $_uid = strings::rand() ?>"></em>
        <script>
          (_ => $(document).ready(() => $('#<?= $_uid ?>').html(`BootStrap: ${_.bootstrap_version.extended()}`)))(_brayworth_);
        </script>

      </div>

      <div class="col-auto" id="brayworthLOGO">
        <a title="software by BrayWorth using php" href="https://brayworth.com" target="_blank">BrayWorth</a>

      </div>

    </div>

  </div>

</footer>
