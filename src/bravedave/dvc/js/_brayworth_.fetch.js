/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
 * */

(_ => {
  _.fetch = {
    get: url => new Promise((resolve, reject) => {

      fetch(url)
        .then(response => {

          if (!response.ok) {

            reject('Network Error');
          } else {

            return response.text();
          }
        })
        .then(data => resolve(data));
    }),
    json: (url, data) => new Promise(resolve => {

      if (!/^http/.test(url)) url = location.protocol + url;

      let _url = new URL(url);

      if (!!data) {

        for (let k in data) {
          _url.searchParams.set(k, data[k]);
        }
      }

      fetch(_url.toString())
        .then(response => {

          if (!response.ok) throw new Error('Network Error');

          let contentType = response.headers.get("content-type");
          return /application\/json/.test(String(contentType)) ? response.json() : response.text();
        })
        .then(data => resolve(data));
    }),
    post: (url, data) => new Promise((resolve, reject) => {

      fetch(url, {
        method: "POST", // or 'PUT'
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then(response => {

          if (!response.ok) throw new Error('Network Error');
          return response.json();
        })
        .then(data => resolve(data))
        .catch(error => reject(error));
    })
  };

  /**
   * this firms up what an api call should look like
   * _brayworth_.api( url, data)
   *  .then( d => {}).catch(_.growl);
   */
  _.api = (url, data) => new Promise((resolve, reject) => {

    _.fetch.post(url, data)
      .then(d => ('ack' == d.response) ? resolve(d.data) : reject(d));
  });

  // https://stackoverflow.com/questions/46640024/how-do-i-post-form-data-with-fetch-api
  _.fetch.post.form = (url, form, method = 'application/x-www-form-urlencoded') => new Promise((resolve, reject) => {

    let data = new FormData(form);
    if ('multipart/form-data' == method) {

    } else {

      data = new URLSearchParams(data);
    }
    // console.log('method', method);

    fetch(url, {
      method: "POST",
      body: data,
    })
      .then(response => {

        if (!response.ok) throw new Error('Network Error');
        return response.json();
      })
      .then(data => resolve(data))
      .catch(error => reject(error));
  });

  _.api.form = (url, form, method = 'application/x-www-form-urlencoded') => new Promise((resolve, reject) => {

    _.fetch.post.form(url, form, method)
      .then(d => ('ack' == d.response) ? resolve(d.data) : reject(d));
  });
})(_brayworth_);