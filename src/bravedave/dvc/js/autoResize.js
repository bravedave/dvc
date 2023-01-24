/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://learn.jquery.com/plugins/basic-plugin-creation/
 *
 * */
 ($ => {
  $.fn.autoResize = function() {

    let hiddenDiv = $('<div class="autosize-common autosize-hiddendiv"></div>');

    if ( this.hasClass('form-control-sm')) {
      this.addClass('autosize-common-sm');
      hiddenDiv.addClass('autosize-common-sm');
    }

    hiddenDiv
      .width(this.width())
      .appendTo(this.parent());

    let update = () => {
      let content = this.val();

      hiddenDiv
          .css('line-height', this.css('line-height'))
          .width(this.width());

      content = content.replace(/\n/g, '<br>');
      hiddenDiv.html(content + '<br class="autosize-lbr">');

      this.css('height', (hiddenDiv.height() * 1.1) + 14);
      // console.log('height', (hiddenDiv.height()*1.1) + 14);

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
    // console.log('resize');
    return (this);

  }

})(jQuery);
