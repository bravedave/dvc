/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * version 1.0.0
 * */

const post = (url, data) => new Promise((resolve, reject) => {

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
});

onmessage = (event) => {

  if ('string' == typeof event.data) {

    postMessage(`Hi, ${event.data}`);
  } else if ('object' == typeof event.data) {

    if ('post' in event.data) {

      post(event.data.post.url, event.data.post.data)
        .then(d => {

          const response = {
            request : event.data.post.data,
            reference : event.data.post.reference,
            response : d
          };
          postMessage(response);
        })
        .catch(e => evt.error(e));
    }
  }
};
