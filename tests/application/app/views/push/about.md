#### Push Notifications

Are available with the _minishlink/web-push_ library

```bash
composer require minishlink/web-push
```

Then registering your interest

```javascript

( _ => {
  _.push.url = _.url( '');
  _.push.applicationServerKey = '<?= trim( \config::notification_keys()->pubKey) ?>'; // note php on this line
  _.push.serviceWorker = _.url( 'serviceWorker');
  _.push.load();

  _.push.subscribe();

  // or _.push.unsubscribe();

  // or _.push.subscribeIfPermissive();

})(_brayworth_);

```

Noting that this is a PHP Library, so sending messages from javascript is not the primary goal, but you can test the interface ..
```javascript
_brayworth_.push.testMessage();
```

Which will trigger (to each deice you are subscribed too ...):

```php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class push {
  static function test( int $user) {
    $dao = new \dao\notifications;
    if ( $dtoSet = $dao->getForUserID( $user)) {

      foreach ($dtoSet as $dto) {
        $subscription = Subscription::create( (array)json_decode( $dto->json));
        self::webPush( $subscription, "Hello! 👋");

      }

    }

  }

}
```
