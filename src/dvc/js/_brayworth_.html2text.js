/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( _ => {
  _.html2text = html => {

    /* * Sanitize the HTML **/
    //~ console.log( 'sanitizing');

    html = String(html);
    html = html.replace(/<style([\s\S]*?)<\/style>/gi, '');
    html = html.replace(/<script([\s\S]*?)<\/script>/gi, '');
    html = html.replace(/<\/div>/ig, '\n');
    html = html.replace(/<\/tr>/ig, '\n');
    html = html.replace(/<\/td>[^<]*<td[^>]+>/ig, ' ... ');
    html = html.replace(/<\/li>/ig, '\n');
    // html = html.replace(/<tr>/ig, '  *  ');
    html = html.replace(/<li>/ig, '  *  ');
    html = html.replace(/<\/ul>/ig, '\n');
    html = html.replace(/<\/p>/ig, '\n');
    html = html.replace(/<br\s*[\/]?>/gi, "\n");
    html = html.replace(/<[^>]+>/ig, '');

    return html;

  };

}) (_brayworth_);
