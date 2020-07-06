###### [Docs](/docs/) | [Utilities](/docs/utilities) | [PHP](/docs/utilities_php) | Strings

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


#### Functions (public)
* asLocalDate( $date, $time = false, $epoch = 0)
```php
    print strings::asLocalDate( '2020-12-01');  // In Austalia : 01/12/2020
```

<hr />
* asLocalPhone( $_tel = '' )
```php
    print strings::asLocalPhone( '0755332255');  // In Austalia : (07) 5533 2255
```

<hr />
* asMobilePhone( $mobile = '' )
  * alias for asLocalPhone

<hr />
* asShortDate( $date, $time = false)
* array2csv(array &$array)
* BRITISHDateAsANSI( $strDate)
* CheckEmailAddress( $email)
* DateDiff( $date1, $date2 = null, $format = '%R%a')
* endswith($string, $test)
* getCommonPath( array $paths)
* getRelativePath( string $from, string $to, string $ps = DIRECTORY_SEPARATOR)
* getDateAsANSI( $strDate)
* getGUID()
* getUID()
* html2text($document)
* htmlSanitize( $html )
* initials( $name )
* InLocalTimeZone($format="r", $timestamp=false, $timezone=false)
* isValidMd5($md5 ='')
* IsEmailAddress( $email)
* isEmail( $email)
* isMobilePhone( string $_tel = '')
* isPhone( string $_tel = '')
* isOurEmailDomain( $email)
* lorum()
  * prints out some lorum ispum

<hr />
* pixel()
* rand( $prefix = 'uid_')
* replaceWordCharacters( $text)
* SmartCase($name)
* text2html( $inText, $maxrows = -1, $allAsteriskAsList = false )
* url( string $url = '', bool $protocol = false)
* xml_entities($text, $charset = 'UTF-8')