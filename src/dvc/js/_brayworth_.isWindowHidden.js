/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( _ => {
  _.isWindowHidden = () => {
    // https://stackoverflow.com/questions/7389328/detect-if-browser-tab-has-focus
    let getHiddenProp = () => {
      let prefixes = ['webkit', 'moz', 'ms', 'o'];

      // if 'hidden' is natively supported just return it
      if ('hidden' in document) return 'hidden';

      // otherwise loop over all the known prefixes until we find one
      for (var i = 0; i < prefixes.length; i++) {
        if ((prefixes[i] + 'Hidden') in document)
          return prefixes[i] + 'Hidden';

      }

      return null;  // otherwise it's not supported

    }

    let prop = getHiddenProp();
    if (!prop) return false;
    return document[prop];

  };

}) (_brayworth_);
