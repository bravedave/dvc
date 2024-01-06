const version = 3.34;

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
  // event is an ExtendableMessageEvent object
  console.log(`The client sent me a message: ${event.data}`, version);
  postMessage("Hi client ...");
  // console.log(window.location.href);
  console.log(this.location);
  let url = new URL(this.location);

  post(this.location, {
    action: 'who-am-i'
  })
};
