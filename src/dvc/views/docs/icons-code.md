```html
<div class="row mb-4">
  <div class="col"><i class="bi bi-arrow-90deg-left"></i></div>
  <div class="col"><i class="bi bi-arrow-90deg-right"></i></div>
  <div class="col"><i class="bi bi-arrow-90deg-up"></i></div>
  <div class="col-2">&nbsp;</div>
  <div class="col"><i class="bi bi-arrow-bar-down"></i></div>
  <div class="col"><i class="bi bi-arrow-bar-left"></i></div>
  <div class="col"><i class="bi bi-arrow-bar-right"></i></div>
  <div class="col"><i class="bi bi-arrow-bar-up"></i></div>
  <div class="col"><i class="bi bi-arrow-clockwise"></i></div>
  <div class="col"><i class="bi bi-arrow-counterclockwise"></i></div>
  <div class="col"><i class="bi bi-arrow-down-circle-fill"></i></div>
</div>
```

```php
<?php

use dvc\icon;

<div class="row mb-4">
  <div class="col"><?= icon::get( icon::envelope ) ?></div>

  <div class="col text-primary"><?= icon::get( icon::envelope ) ?></div>

  <div class="col text-danger"><?= icon::get( icon::envelope ) ?></div>

  <div class="col text-success"><?= icon::get( icon::envelope ) ?></div>

  <div class="col"><?= icon::get( icon::envelope_fill ) ?></div>

  <div class="col"><?= icon::get( icon::envelope_open ) ?></div>

  <div class="col"><?= icon::get( icon::envelope_open_fill ) ?></div>

  <div class="col"><?= icon::get( icon::file_rich_text ) ?></div>

  <div class="col"><?= icon::get( icon::file_text ) ?></div>

  <div class="col"><?= icon::get( icon::file_text_fill ) ?></div>

  <div class="col text-primary"><?= icon::get( icon::file_text_fill ) ?></div>

  <div class="col text-danger"><?= icon::get( icon::file_text_fill ) ?></div>

</div>
```
