<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="markdown-body">
  <h6>
    <a href=".">Docs</a>
    |
    <a href="utilities">Utilities</a>
    |
    <a href="utilities_javascript">Javascript</a>
    |
    Autofill

  </h6>

  <h3>Autofill</h3>
  <table class="table">
    <tbody>
      <tr>
        <td>description</td>
        <td>Input Autocomplete</td>

      </tr>

      <tr>
        <td>type</td>
        <td>javascript</td>

      </tr>

      <tr>
        <td>source</td>
        <td>src/dvc/js/autofill.js</td>

      </tr>

    </tbody>

  </table>

  inspired by jquery-ui and should behave as such<br>

  <h4>Example</h4>

  <div class="row my-2">
    <div class="col">
      <input type="text" id="<?= $_uid = strings::rand() ?>" class="form-control" placeholder="david bill or mary">
      <script>
        $('#<?= $_uid ?>').autofill({
          source : ['david', 'bill', 'mary']

        });
      </script>

    </div>

  </div>

  <pre><code class="language-html hljs">
    &lt;input type="text" name="name" id="name"&gt;
  </code></pre>

  <pre><code class="language-javascript hljs">
    $('#name').autofill({
      source : ['david', 'bill', 'mary']

    });
  </code></pre>
or ...<br>
a ajax request can be sent, the request is passed in the format <code class="language-javascript hljs">[&lt;jsonObject&gt;.term]</code><br>
and the response is expected <code class="language-javascript hljs">[{'label':'David','value':'David'},{'label':'Bill','value':'Bill'}]</code><br>

  <pre><code class="language-javascript hljs">
    $('#name').autofill(
      source : ( request, response) => {
        _.post({
          url : _.url(''),
          data : {
            action : 'search',
            term : request.term

          },

        }).then( d => response( 'ack' == d.response ? d.data : []));
      },
      select : (e,ui) => {
        /**
         * by default the data is populated to the field,
         * but additional processing can be done here
         */

      }

    );
  </code></pre>
</div>
