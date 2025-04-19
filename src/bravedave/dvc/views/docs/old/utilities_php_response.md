# Response

[Docs](.) | [Utilities](utilities) | [PHP](utilities_php) | **Response**

<table class="table">
  <tbody>
    <tr>
      <td>description</td>
      <td>PHP Abstract Response Class to handle initiating a response from the server</td>
    </tr>
    <tr>
      <td>type</td>
      <td>php</td>
    </tr>
    <tr>
      <td>source</td>
      <td>src\dvc\Response.php</td>
    </tr>
  </tbody>
</table>

## Example

```php
  dvc\Response::json_headers();
  print json_encode([
    'response' => 'ack',
    'description' => 'Good thing to do'
  ])
```

## Functions (public)

* css_headers( $modifyTime = 0, $expires = nul
* csv_headers( $filename = "download.csv", $modifyTime = 0, $expires = 0)
* excel_headers( $filename = "download.xml" )
* exe_headers( $filename = null, $modifyTime = 0)
* headers( $mimetype, $modifyTime = 0, $expires = 0)
* html_docType()
* html_headers( $charset = false)
* gif_headers( $modifyTime = 0, $expires = null)
* icon_headers( $modifyTime = 0, $expires = null)
* javascript_headers( $modifyTime = 0, $expires = 0)
* jpg_headers( $modifyTime = 0, $expires = null)
* json_headers( $modifyTime = 0)
* mso_docType()
* pdf_headers( $filename = null, $modifyTime = 0)
* png_headers( $modifyTime = 0, $expires = null)
* redirect( $url = null, $message = "", $auto = true )
* text_headers( $modifyTime = 0, $expires = 0)
* tiff_headers( $filename = null, $modifyTime = 0)
* xml_docType()
* xml_headers( $modifyTime = 0)
* zip_headers( $filename = null, $modifyTime = 0)