<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/

namespace dvc;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class push {
  static function enabled() {
    return class_exists( 'Minishlink\WebPush\WebPush');

  }

  static function serviceWorker() {
    sys::serve( implode( DIRECTORY_SEPARATOR, [
      __DIR__,
      'js',
      'service-worker.js'

    ]));

  }

  static function test() {
    $path = implode( DIRECTORY_SEPARATOR, [
      config::notification_KeyPath(),
      'subscription.json'

    ]);

    if ( \file_exists( $path)) {
      $subscription = Subscription::create( (array)json_decode( file_get_contents( $path)));

      $auth = array(
        'VAPID' => array(
          'subject' => 'https://github.com/bravedave/dvc-chat/',
          'publicKey' => config::notification_keys()->pubKey,
          'privateKey' => config::notification_keys()->privKey,

        ),

      );

      // \sys::logger( sprintf('<%s> %s', print_r( $auth, true), __METHOD__));

      $defaultOptions = array(
        'TTL' => 300, // defaults to 4 weeks
        'urgency' => 'normal', // protocol defaults to "normal"
        'topic' => 'push', // not defined by default - collapse_key
      );


      $webPush = new WebPush( $auth); //, $defaultOptions);
      $report = $webPush->sendOneNotification(
          $subscription,
          "Hello! 👋"
      );

      /**
       * handle eventual errors here,
       * and remove the subscription from your
       * server if it is expired
       *
       */
      $endpoint = $report->getRequest()->getUri()->__toString();

      if ($report->isSuccess()) {
        \sys::logger(
          sprintf(
            '<Message sent successfully for subscription {$%s}> %s',
            $subscription->getEndpoint(),
            __METHOD__

          )

        );

      }
      else {
        \sys::logger(
          sprintf(
            '<Message failed to send for subscription {$%s}> <%s> %s',
            $subscription->getEndpoint(),
            $report->getReason(),
            __METHOD__

          )

        );

      }

    }

  }

}
