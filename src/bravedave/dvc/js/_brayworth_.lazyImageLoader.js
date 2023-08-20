/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => _.lazyImageLoader = () => {

  let imgStack = [];

  $('div[data-delayedimg="true"]').each((i, el) => {

    let _el = $(el);
    if (_el.visible(true)) {

      _el
        .css('background-image', `url("${_el.data('src')}")`)
        .data('delayedimg', false);
    }
    else {

      imgStack.push(_el);
    }
  });

  if (imgStack.length > 0) {
    // console.log( 'unloaded images', imgStack.length);

    $(document).on('scroll.lazyLoader', e => {

      let unProcessed = 0;
      $.each(imgStack, (i, el) => {

        let _el = $(el);
        if (_el.data('delayedimg')) {

          if (_el.visible(true)) {

            _el
              .css('background-image', `url("${_el.data('src')}")`)
              .data('delayedimg', false);
          }
          else {

            unProcessed++;
          }
        }
      });

      if (unProcessed < 1) $(document).off('scroll.lazyLoader');
    });
  }
})(_brayworth_);
