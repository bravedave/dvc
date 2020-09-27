<div class="container d-flex flex-row justify-content-between" id="<?= $_uid = strings::rand() ?>">
  <a class="py-2" href="#item0" aria-label="Product">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
      stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="d-block mx-auto" role="img"
      viewBox="0 0 24 24" focusable="false">
      <title>Product</title>
      <circle cx="12" cy="12" r="10" />
      <path
        d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83m13.79-4l-5.74 9.94" />
    </svg>
  </a>
  <a class="py-2 d-md-inline-block" href="#item1">item 1</a>
  <a class="py-2 d-md-inline-block" href="#">item 2</a>
  <a class="py-2 d-md-inline-block" href="#">item 3</a>
  <a class="py-2 d-none d-md-inline-block" href="#">item 4</a>
  <a class="py-2 d-none d-md-inline-block" href="#">item 5</a>
  <a class="py-2 d-none d-md-inline-block" href="#">item 6</a>

</div>
<script>
  $(document).ready( () => {
    ( _ => {
      $('a[href="#item0"]','#<?= $_uid ?>').on( 'click', function( e) {
        e.stopPropagation();e.preventDefault();

        $(document).trigger('get-content', { 'main' : _.url( '<?= $this->route ?>/content')});

      })

      $('a[href="#item1"]','#<?= $_uid ?>').on( 'click', function( e) {
        e.stopPropagation();e.preventDefault();

        $(document).trigger('get-content', { 'main' : _.url( '<?= $this->route ?>/content2')});

      })

    }) (_brayworth_);

  });
</script>
