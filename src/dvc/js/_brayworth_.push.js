/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

( _ => {
  _.push = {
    active : false,
    url : '',
    serviceWorker : '',
    applicationServerKey : ''

  };

  let urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  };

  let checkNotificationPermission = () => {
    return new Promise((resolve, reject) => {
      if (Notification.permission === 'denied') {
        // console.log('Push messages are blocked.');
        reject(new Error('Push messages are blocked.'));

      }

      if (Notification.permission === 'granted') {
        resolve();

      }
      else if (Notification.permission === 'default') {
        Notification.requestPermission().then(result => {
          if (result !== 'granted') {
            reject(new Error('Bad permission result'));

          }
          else {
            resolve();

          }

        });

      }
      else {
        console.log('Unknown permission');
        return reject(new Error('Unknown permission'));

      }

    });

  }

  _.push.load = () => {

    return new Promise( (resolve, reject) => {
      // Check the current Notification permission.
      // If its denied, the button should appears as such, until the user changes the permission manually

      if (Notification.permission === 'denied') {
        console.warn('Notifications are denied by the user');

      }
      else {
        navigator.serviceWorker.register( _.push.serviceWorker).then(
          () => {
            console.log('[SW] Service worker has been registered');
            _.push.updateSubscription().then( () => resolve());

          },
          e => {
            console.error('[SW] Service worker registration failed', e);
            reject();

          }

        );

      }

    });

  };

  _.push.deleteSubscriptionFromServer = (subscription) => {

    // console.log( 'deleteSubscriptionFromServer');

    return _.post({
      url : _.push.url,
      data : {
        action : 'subscription-delete',
        endpoint: subscription.endpoint,

      },

    }).then( d => {
      _.growl( d);
      return subscription;

    });

  };

  _.push.sendSubscriptionToServer = (subscription) => {
    const key = subscription.getKey('p256dh');
    const token = subscription.getKey('auth');
    const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

    // console.log( 'sendSubscriptionToServer');

    return _.post({
      url : _.push.url,
      data : {
        action : 'subscription-save',
        json: JSON.stringify({
          endpoint: subscription.endpoint,
          publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
          authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
          contentEncoding,
        }),
        endpoint: subscription.endpoint,
        publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
        authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
        encoding: contentEncoding,

      },

    }).then( d => { return subscription; });

  };

  _.push.subscribe = () => {
    // console.log('subscribe ..');

    return checkNotificationPermission()
      .then( () => {
        navigator.serviceWorker.register( _.push.serviceWorker)
        .then( serviceWorkerRegistration => {
          serviceWorkerRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(_.push.applicationServerKey),
          })
          .then( subscription => {
            _.push.active = true;
            return _.push.sendSubscriptionToServer(subscription);

          });

        });

      })
      .catch(e => {
        if (Notification.permission === 'denied') {
          console.warn('Notifications are denied by the user.');

        }
        else {
          console.error('Impossible to subscribe to push notifications', e);

        }

      });

  };

  _.push.subscribeIfPermissive = () => {
    if ( !_.push.active && 'granted' == Notification.permission) {
      _.push.subscribe();

    }

  };

  _.push.testMessage = () => {
    _.post({
      url: _.push.url,
      data: {
        action: 'send-test-message'

      },

    }).then(d => _.growl(d));

  }

  _.push.unsubscribe = () => {
    // console.log('unsubscribe ..');

    /**
     * To unsubscribe from push messaging,
     * you need to get the subscription object
     *  */
    navigator.serviceWorker.ready
    .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
    .then(subscription => {

      // Check that we have a subscription to unsubscribe
      if (!subscription) {
        /**
         * No subscription object, so set the state
         * to allow the user to subscribe to push
         */
        _.push.active = false;
        return;

      }

      /**
       * We have a subscription, unsubscribe
       * Remove push subscription from server
       */

      return _.push.deleteSubscriptionFromServer(subscription);

    })
    .then(subscription => {
      subscription.unsubscribe()
      _.push.active = false;

    })
    .catch(e => console.error('Error when unsubscribing the user', e));

  };

  _.push.updateSubscription = () => {
    return new Promise( ( resolve, reject) => {
      navigator.serviceWorker.ready
        .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
        .then(subscription => {

          if (!!subscription) {
            _.push.active = true;
            // console.log( 'Keep server push in sync with the latest endpoint');
            return _.push.sendSubscriptionToServer(subscription);

          }

          resolve();

        })
        .catch(e => console.error('Error when updating the subscription', e));

    });

  };

}) (_brayworth_);

