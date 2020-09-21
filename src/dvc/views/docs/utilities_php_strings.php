<div class="markdown-body">
  <h6>
    <a href="/docs/">Docs</a>
    |
    <a href="/docs/utilities">Utilities</a>
    |
    <a href="/docs/utilities_php">PHP</a>
    |
    Strings

  </h6>
  <table class="table">
      <tbody>
          <tr>
              <td>description</td>
              <td>Lots of utility string conversions</td>

          </tr>

          <tr>
              <td>type</td>
              <td>php</td>

          </tr>

          <tr>
              <td>source</td>
              <td>src\dvc\strings.php</td>

          </tr>

      </tbody>

  </table>

  <h4>Functions (public)</h4>

  <h5>Dates</h5>

  <ul>
    <li>asLocalDate( $date, $time = false, $epoch = 0)</li>

  </ul>

<pre><code class="language-php hljs">
  print strings::asLocalDate( '2020-12-01');  // In Austalia : <?= strings::asLocalDate( '2020-12-01') ?>
</code></pre>

  <hr />

  <ul>
    <li>asShortDate( $date, $time = false)</li>

  </ul>

<pre><code class="language-php hljs">
  print strings::asShortDate( '<?= date('Y') ?>-12-01');  // In Austalia : <?= strings::asShortDate( date('Y') . '-12-01') ?>
</code></pre>

  <hr />

  <ul>
    <li>asLongDate( $date, $time = false)</li>

  </ul>

<pre><code class="language-php hljs">
  print strings::asLongDate( '2020-12-01');  // <?= strings::asLongDate( '2020-12-01') ?>

  print strings::asLongDate( '2020-12-01 14:30:00', $time = true);  // <?= strings::asLongDate( '2020-12-01 14:30:00', $time = true) ?>
</code></pre>

  <hr />

  <h5>Telephone Numbers</h5>
  <p>using giggsey : https://github.com/giggsey/libphonenumber-for-php</p>

  <ul>
    <li>asLocalPhone( $_tel = '' )</li>
  </ul>

<pre><code class="language-php hljs">
    print strings::asLocalPhone( '0755332255');  // In Austalia : <?= strings::asLocalPhone( '0755332255') ?>
</code></pre>

  <hr />

  <ul>
    <li>asMobilePhone( $mobile = '' ) - <em>alias for asLocalPhone</em></li>

  </ul>

  <h5>Other ..</h5>

  <ul>
    <li>array2csv(array &$array)</li>
    <li>BRITISHDateAsANSI( $strDate)</li>
    <li>CheckEmailAddress( $email)</li>
    <li>DateDiff( $date1, $date2 = null, $format = '%R%a')</li>
    <li>endswith($string, $test)</li>
    <li>getCommonPath( array $paths)</li>
    <li>getRelativePath( string $from, string $to, string $ps = DIRECTORY_SEPARATOR)</li>
    <li>getDateAsANSI( $strDate)</li>
    <li>getGUID()</li>
    <li>getUID()</li>
    <li>html2text($document)</li>
    <li>htmlSanitize( $html )</li>
    <li>initials( $name )</li>
    <li>InLocalTimeZone($format="r", $timestamp=false, $timezone=false)</li>
    <li>isValidMd5($md5 ='')</li>
    <li>IsEmailAddress( $email)</li>
    <li>isEmail( $email)</li>
    <li>isMobilePhone( string $_tel = '')</li>
    <li>isPhone( string $_tel = '')</li>
    <li>isOurEmailDomain( $email)</li>
    <li>lorum()
      <ul>
        <li>prints out some lorum ispum</li>

      </ul>

    </li>
    <li>pixel()</li>
    <li>rand( $prefix = 'uid_')</li>
    <li>replaceWordCharacters( $text)</li>
    <li>SmartCase($name)</li>
    <li>text2html( $inText, $maxrows = -1, $allAsteriskAsList = false )</li>
    <li>url( string $url = '', bool $protocol = false)</li>
    <li>xml_entities($text, $charset = 'UTF-8')</li>

  </ul>

</div>
