# Extend

###### [Docs](/docs/) | [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript) | Dates

<p>Previously MomentJS was used, but is being deprecated, dayjs seems a viable alternative - <a class="h4" href="https://day.js.org/">https://day.js.org/</a></p>

<p>Being Loaded as (not seeing it documented like this):</p>
```javascript
$(document).ready( () => {
  dayjs.extend(dayjs_plugin_localeData);
  dayjs.extend(dayjs_plugin_localizedFormat);
  dayjs.extend(dayjs_plugin_utc);
  dayjs.extend(dayjs_plugin_timezone);
  dayjs.extend(dayjs_plugin_updateLocale);
  // dayjs.extend(dayjs_plugin_customParseFormat); // buggy as at 21/9/2020

  if ('' !== _.timezone) {
    dayjs.tz.setDefault(_.timezone);

  }

});
```

<p class="text-muted">Note: that dayjs is not fully available until DOMContentLoaded, using jQuery here ..</p>

```javascript
$(document).ready( () => {
  ( _ => {
    $('#date_example_1').html( _.dayjs( '04/8/2013').format('L'));

  }) (_brayworth_);

});
```
Result: <span id="date_example_1" class="font-weight-bold"></span>
<script>
$(document).ready( () => {
  ( _ => {
    $('#date_example_1').html( _.dayjs( '04/8/2013').format('L'));

  }) (_brayworth_);

});
</script>

```javascript
$(document).ready( () => {
  ( _ => {
    $('#date_example_2').html( _.dayjs( '04/8/2013').format('LL'));

  }) (_brayworth_);

});
```
Result: <span id="date_example_2" class="font-weight-bold"></span>
<script>
$(document).ready( () => {
  ( _ => {
    $('#date_example_2').html( _.dayjs( '04/8/2013').format('LL'));

  }) (_brayworth_);

});
</script>

```javascript
$(document).ready( () => {
  ( _ => {
    $('#date_example_3').html( _.dayjs( '04/8/2013', 'DD/MM/YYYY').format('ddd LL'));

  }) (_brayworth_);

});
```
Result: <span id="date_example_3" class="font-weight-bold"></span>
<script>
$(document).ready( () => {
  ( _ => {
    $('#date_example_3').html( _.dayjs( '04/8/2013', 'DD/MM/YYYY').format('ddd LL'));

  }) (_brayworth_);

});
</script>

