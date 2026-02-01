<?php
  // file: src/app/contacts/views/view.php
  // MIT License

namespace contacts;

// note: $dto and $title into the environment ?>
<div>

  <!-- --[name]-- -->
  <div class="row g-2">

    <div class="col-md-3 text-truncate fw-bold">name</div>
    <div class="col mb-2">

      <?= $dto->name ?>
    </div>
  </div>

  <!-- --[email]-- -->
  <div class="row g-2">

    <div class="col-md-3 text-truncate fw-bold">email</div>
    <div class="col mb-2">

      <?= $dto->email ?>
    </div>
  </div>

  <!-- --[mobile]-- -->
  <div class="row g-2">

    <div class="col-md-3 text-truncate fw-bold">mobile</div>
    <div class="col mb-2">

      <?= $dto->mobile ?>
    </div>
  </div>

</div>
