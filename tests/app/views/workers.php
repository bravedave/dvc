<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace cms;

extract((array)($this->data ?? []));  ?>
<h1><?= $title ?></h1>
<script>
  (_ => {

    if (window.Worker) {

      const w = new Worker('<?= $webWorker ?>');
      w.onmessage = event => {
        // event is a MessageEvent object
        console.log(`The worker sent me a message: ${event.data}`);
      };

      setTimeout(() => {
        w.postMessage('hi');
        console.log('sent a message');
      }, 1000);
    } else {

      console.log('no worker');
    }
  })(_brayworth_);
  (_ => {
    return;
    // paste the below code in your main.js
    // in the page being controlled
    if (navigator.serviceWorker) {

      navigator.serviceWorker.register('<?= $serviceWorker ?>');

      navigator.serviceWorker.addEventListener('message', event => {
        // event is a MessageEvent object
        console.log(`The service worker sent me a message: ${event.data}`);
      });

      navigator.serviceWorker.ready.then(registration => {
        registration.active.postMessage("Hi service worker");
        console.log('sent a message');
      });
    }
  })(_brayworth_);
  (_ => {
    return;

    const registerServiceWorker = () => new Promise((resolve, reject) => {

      if ('serviceWorker' in navigator) {

        navigator.serviceWorker.register(
          '<?= $serviceWorker ?>', {
            scope: './',
          }
        ).then(swr => {

          swr.onstatechange = () => {
            // swr.installing = null;
            // At this point, swr.waiting OR swr.active might be true. This is because the statechange
            // event gets queued, meanwhile the underlying worker may have gone into the waiting
            // state and will be immediately activated if possible.
            // console.log(swr);
          };

          swr.addEventListener("updatefound", () => {
            // If updatefound is fired, it means that there's
            // a new service worker being installed.
            const installingWorker = swr.installing;
            console.log(
              "A new service worker is being installed:",
              installingWorker,
            );

            // You can listen for changes to the installing service worker's
            // state via installingWorker.onstatechange
          });

          // registration.unregister().then((boolean) => {
          //   // if boolean = true, unregister is successful
          //   if (boolean) console.log('Service worker unregistered')
          // });
          resolve(swr);
        });

        // if (registration.installing) {

        //   console.log('Service worker installing');
        // } else if (registration.waiting) {

        //   console.log('Service worker installed');
        // } else if (registration.active) {

        //   console.log('Service worker active');
        // }
      }
    });

    console.log('<?= $serviceWorker ?>');
    // navigator.serviceWorker.register('<?= $serviceWorker ?>');

    // navigator.serviceWorker.ready.then((registration) => {
    //   registration.active.postMessage(
    //     "Test message sent immediately after creation",
    //   );
    // });
    registerServiceWorker()
      .then(swr => {

        console.log('Service worker registered ..');
        navigator.serviceWorker.addEventListener('message', event => {
          // event is a MessageEvent object
          console.log(`The service worker sent me a message: ${event.data}`);
        });
        //     // registration.unregister().then((boolean) => {
        //     //   // if boolean = true, unregister is successful
        //     //   if (boolean) console.log('Service worker unregistered')
        //     // });
        // navigator.serviceWorker.ready.then(rego => rego.active.postMessage({
        //   type: 'hi',
        //   name: 'Dave',
        //   greeting: 'Hello there'
        // }));
        // swr.active.postMessage({
        //   type: 'hi',
        //   name: 'Dave',
        //   greeting: 'Hello there'
        // });
        navigator.serviceWorker.controller.postMessage('hi');
        swr.active.postMessage('hi ..');
        console.log('hello..');
      });

    navigator.serviceWorker.ready
      .then(subscription => {

        navigator.serviceWorker.controller.postMessage({
          'data': 'abc'
        });
      })

  })(_brayworth_);
</script>