# TextPrompt

###### [Docs](/docs/) | [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript) | TextPrompt

```javascript
( _ => {
    $('#textPromptExample').on( 'click', function( e) {
        _.textPrompt().then( d => console.log( d));

    });

})(_brayworth_);
```
<div class="input-group">
    <div class="input-group-prepend">
        <button id="textPromptExample" class="btn btn-primary">Example</button>

    </div>

    <input type="text" class="form-control" readonly id="textPromptResult" />

</div>

<script>
( _ => {
    $('#textPromptExample').on( 'click', function( e) {
        _.textPrompt().then( d => $('#textPromptResult').val( d));

    });

})(_brayworth_);
</script>

```javascript
( _ => {
    $('#textPromptExample2').on( 'click', function( e) {
        _.textPrompt({
            title : 'tell me about yourself',
            text : 'What is Your Name ?',
            verbatim : 'Just your First name will do'

        }).then( d => console.log( d));

    });

})(_brayworth_);
```
<div class="input-group">
    <div class="input-group-prepend">
        <button id="textPromptExample2" class="btn btn-primary">Example</button>

    </div>

    <input type="text" class="form-control" readonly id="textPromptResult2" />

</div>

<script>
( _ => {
    $('#textPromptExample2').on( 'click', function( e) {
        _.textPrompt({
            title : 'tell me about yourself',
            text : 'What is Your Name?',
            verbatim : 'Just your First name will do'

        }).then( d => $('#textPromptResult2').val( d));

    });

})(_brayworth_);
</script>