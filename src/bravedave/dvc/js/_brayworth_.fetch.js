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
    get: url => new Promise(resolve => {

      fetch(url)
        .then(response => {

          if (!response.ok) throw new Error('Network Error');
          return response.text();
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
})(_brayworth_);