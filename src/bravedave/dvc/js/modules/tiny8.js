/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * import( '/js/tiny8').then( tinymce => console.log(tinymce));
 * */

const tiny8 = () => {
  return new Promise((resolve, reject) => {
    if (window.tinymce) {
      resolve(window.tinymce);
      return;
    }

    // Check if script is already being loaded
    if (document.getElementById('tinymce8-script')) {

      const checkLoaded = setInterval(() => {
        if (window.tinymce) {
          clearInterval(checkLoaded);
          resolve(window.tinymce);
        }
      }, 50);
      return;
    }
    
    const script = document.createElement('script');
    script.id = 'tinymce8-script';
    script.src = '/assets/tinymce8/tinymce.min.js';
    script.onload = () => resolve(window.tinymce);
    script.onerror = reject;
    document.head.appendChild(script);
  });
}

export { tiny8 };