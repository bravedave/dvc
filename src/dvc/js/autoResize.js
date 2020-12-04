/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
($ => {
  $.fn.autoResize = function () {
    let hiddenDiv = $('<div class="autosize-common autosize-hiddendiv"></div>');

    hiddenDiv
      .width(this.width())
      .appendTo(this.parent());


    let update = function () {
      let _me = (this instanceof jQuery ? this : $(this));
      let content = _me.val();

      hiddenDiv
          .css('line-height', _me.css('line-height'))
          .width(_me.width());

      content = content.replace(/\n/g, '<br />');
      hiddenDiv.html(content + '<br class="autosize-lbr">');

      _me.css('height', (hiddenDiv.height() * 1.1) + 14);
      console.log('height', (hiddenDiv.height()*1.1) + 14);

    }

    this
      .addClass('autosize-textarea autosize-common')
      .off('keyup.autoResize')
      .off('change.autoResize')
      .off('resize', update)
      .on('resize', update)
      .on('keyup.autoResize', update)
      .on('change.autoResize', update);

    update.call(this);
    console.log('resize');
    return (this);

  }

})(jQuery);
