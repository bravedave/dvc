<style>
  div[data-role="content-primary"]>.markdown-body>h1 {
    display: none;
  }

  div[data-role="content-primary"]>.markdown-body>h1,
  div[data-role="content-primary"]>.markdown-body>h2,
  div[data-role="content-primary"]>.markdown-body>h3,
  div[data-role="content-primary"]>.markdown-body>h4,
  div[data-role="content-primary"]>.markdown-body>h5,
  div[data-role="content-primary"]>.markdown-body>h6 {
    margin-top: 1rem;
    margin-bottom: .5rem;
  }

  div[data-role="content-primary"]>.markdown-body>h1,
  div[data-role="content-primary"]>.markdown-body>.h1 {
    font-size: 1.8rem
  }

  div[data-role="content-primary"]>.markdown-body>h2,
  div[data-role="content-primary"]>.markdown-body>.h2 {
    font-size: 1.6rem
  }

  div[data-role="content-primary"]>.markdown-body>h3,
  div[data-role="content-primary"]>.markdown-body>.h3 {
    font-size: 1.4rem
  }

  div[data-role="content-primary"]>.markdown-body>h4,
  div[data-role="content-primary"]>.markdown-body>.h4 {
    font-size: 1.2rem
  }

  div[data-role="content-primary"]>.markdown-body>h5,
  div[data-role="content-primary"]>.markdown-body>.h5 {
    font-size: 1.1rem
  }

  h6,
  .h6 {
    font-size: 1rem
  }
</style>
<script>
  $(document).ready(() => {
    let h = $('[data-role="content-primary"] > .markdown-body > h1');
    if (h.length > 0) {
      let title = h.first().html();

      $('body > nav .navbar-brand')
        .html(`<h4 class="my-0">${title}</h4>`);
      document.title = title;

    }

  })
</script>
