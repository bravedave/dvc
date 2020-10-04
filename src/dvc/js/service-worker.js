/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

self.addEventListener('push', function (event) {
  if (!(self.Notification && self.Notification.permission === 'granted')) {
    return;
  }

  console.log('push');
  const sendNotification = body => {
    // you could refresh a notification badge here with postMessage API
    const title = "Web Push example";
    console.log('sendNotification');
    return self.registration.showNotification(title, {
      body,
    });

  };

  if (event.data) {
    const message = event.data.text();
    event.waitUntil(sendNotification(message));

  }

});
