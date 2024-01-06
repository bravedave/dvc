const version = 3.33;

self.oninstall = (event) => {
  console.log('hi', version);
};

// https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorker/postMessage
// addEventListener('message', (event) => {

//   if (event.data.type == 'hi') {

//     console.log(`${event.data.greeting}, ${event.data.name}!`);
//   } else {

//     console.log('Unknown message type', event);
//   }
// });
// self.onmessage = evt => {
//   console.log('Message received', evt);
// };

// self.addEventListener('message', function(event) {
//   console.log('Handling message event:', event);
//   event.source.postMessage("Hi client");
// });

// in the service worker
self.onactivate = (event) => {
  console.log('active', event);
};

// service-worker.js
self.onmessage = (event) => {
  // event is an ExtendableMessageEvent object
  console.log(`The client sent me a message: ${event.data}`);

  event.source.postMessage("Hi client ...");
};
