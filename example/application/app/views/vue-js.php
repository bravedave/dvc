<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/	?>
<div id="app-4">
	<ol>
		<li v-for="todo in todos">
			{{ todo.text }}
		</li>

	</ol>

</div>
<script>
var app4 = new Vue({
	el: '#app-4',
	data: {
		todos: [
			{ text: 'Learn JavaScript' },
			{ text: 'Learn Vue' },
			{ text: 'Build something awesome' }
		]
	}

})
</script>
<ul id="repeat-object" class="demo">
	<li v-for="value in object">
		{{ value }}
	</li>
</ul>
<script>
new Vue({
	el: '#repeat-object',
	data: {
		object: {
			firstName: 'John',
			lastName: 'Doe',
			age: 30
		}
	}
})
</script>

<div id="example-2">
	<!-- `greet` is the name of a method defined below -->
	<button v-on:click="greet">Greet</button>
</div>
<script>
var example2 = new Vue({
	el: '#example-2',
	data: {
		name: 'Vue.js'
	},
	// define methods under the `methods` object
	methods: {
		greet: function (event) {
			// `this` inside methods points to the Vue instance
			alert('Hello ' + this.name + '!')
			// `event` is the native DOM event
			if (event) {
				alert(event.target.tagName)
			}
		}
	}
})
// you can invoke methods in JavaScript too
example2.greet() // -> 'Hello Vue.js!'
</script>
