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
        <em>BootStrap: <span id="<?= $_uidBSVersion = strings::rand() ?>"></span></em>

      </div>

      <div class="col-auto" id="brayworthLOGO">
        <a title="software by BrayWorth using php" href="https://brayworth.com" target="_blank">BrayWorth</a>

      </div>

    </div>

  </div>

</footer>
<script>
  $(document).ready(() => $('#<?= $_uidBSVersion ?>').html(_brayworth_.bootstrap_version()));
</script>
