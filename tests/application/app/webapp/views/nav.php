<div class="navbar-brand" ><?= $this->data->title	?></div>

<ul class="ml-auto navbar-nav">
  <li class="nav-item">
    <a class="nav-link" href="<?= strings::url( $this->route) ?>">
      <?= dvc\icon::get( dvc\icon::house ) ?>

    </a>

  </li>

  <li class="nav-item">
    <a class="nav-link" href="https://github.com/bravedave/">
      <?= dvc\icon::get( dvc\icon::github ) ?>

    </a>

  </li>

</ul>
