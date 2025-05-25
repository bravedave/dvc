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

namespace bravedave\dvc;

use config;
use Minishlink\WebPush\{WebPush, Subscription};

class push {

  static function enabled() {

    if (config::checkDBconfigured()) return class_exists('Minishlink\WebPush\WebPush');
    return false;
  }

  static function send($message, $user) {
    $dao = new \dao\notifications;
    if ($dtoSet = $dao->getForUserID($user)) {

      foreach ($dtoSet as $dto) {

        /** @disregard P1009 Undefined type */
        $subscription = Subscription::create((array)json_decode($dto->json));
        self::webPush($subscription, $message);
      }
    }
  }

  static function serviceWorker() {

    Response::javascript_headers();

    printf(
      'self.addEventListener(\'push\', function (event) {
        if (!(self.Notification && self.Notification.permission === \'granted\')) {
          return;
        }

        const sendNotification = body => {
          // you could refresh a notification badge here with postMessage API
          const title = "%s";
          return self.registration.showNotification(title, {
            body,

          });

        };

        if (event.data) {
          const message = event.data.text();
          event.waitUntil(sendNotification(message));

        }

      });',

      htmlentities(config::$WEBNAME)
    );
  }

  static function test(int $user): bool {

    self::send("Hello! 👋", $user);
    return true;
  }

  static protected function WebPush($subscription, $message) {

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

    /** @disregard P1009 Undefined type */
    $webPush = new WebPush($auth); //, $defaultOptions);

    $report = $webPush->sendOneNotification($subscription, $message);

    /**
     * handle eventual errors here,
     * and remove the subscription from your
     * server if it is expired
     *
     */
    $endpoint = $report->getRequest()->getUri()->__toString();

    if ($report->isSuccess()) {

      logger::info(
        sprintf(
          '<Message sent successfully for subscription {$%s}> %s',
          $subscription->getEndpoint(),
          __METHOD__
        )
      );
    } else {

      if (\preg_match('@(401 Unauthorized|403 Forbidden)@', $report->getReason())) {

        $dao = new \dao\notifications;
        $dao->deleteByEndPoint($subscription->getEndpoint());

        logger::info(
          sprintf(
            '<Unregistered on failed send for subscription {$%s}> <%s> %s',
            $subscription->getEndpoint(),
            $report->getReason(),
            __METHOD__
          )
        );
      } else {

        logger::info(
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
