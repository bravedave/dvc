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
    if ( \config::checkDBconfigured()) {
      return class_exists( 'Minishlink\WebPush\WebPush');

    }

    return false;

  }

  static function send( $message, $user) {
    $dao = new \dao\notifications;
    if ( $dtoSet = $dao->getForUserID( $user)) {

      foreach ($dtoSet as $dto) {
        $subscription = Subscription::create( (array)json_decode( $dto->json));
        self::webPush( $subscription, $message);

      }

    }

  }

  static function serviceWorker() {
    sys::serve( implode( DIRECTORY_SEPARATOR, [
      __DIR__,
      'js',
      'service-worker.js'

    ]));

  }

  static function test( int $user) {
    self::send( "Hello! 👋", $user);

  }

  static protected function WebPush( $subscription, $message) {
    $auth = array(
      'VAPID' => array(
        'subject' => 'https://github.com/bravedave/dvc-chat/',
        'publicKey' => config::notification_keys()->pubKey,
        'privateKey' => config::notification_keys()->privKey,

      ),

    );

    $defaultOptions = array(
      'TTL' => 300, // defaults to 4 weeks
      'urgency' => 'normal', // protocol defaults to "normal"
      'topic' => 'push', // not defined by default - collapse_key
    );

    $webPush = new WebPush( $auth); //, $defaultOptions);

    $report = $webPush->sendOneNotification( $subscription, $message);

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
      if ( \preg_match( '@(401 Unauthorized|403 Forbidden)@', $report->getReason())) {
        $dao = new \dao\notifications;
        $dao->deleteByEndPoint( $subscription->getEndpoint());

        \sys::logger(
          sprintf(
            '<Unregistered on failed send for subscription {$%s}> <%s> %s',
            $subscription->getEndpoint(),
            $report->getReason(),
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
