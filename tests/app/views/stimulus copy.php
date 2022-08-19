<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>
<script type="module">
  import {
    Application,
    Controller
  } from '/assets/brayworth/stimulus';

  window.Stimulus = Application.start();

  Stimulus.register("hello", class extends Controller {
    static targets = ["name"]

    greet() {
      const element = this.nameTarget;
      const name = element.value;
      console.log(`Hello, ${name}!`);
    }

    connect() {
      console.log("Hello, Stimulus!", this.element)
    }
  });
</script>

<div data-controller="hello">
  <input data-hello-target="name" type="text">
  <button data-action="click->hello#greet">Greet</button>
</div>

<script type="module">
  // Application,
  import {
    Controller
  } from '/assets/brayworth/stimulus';

  // let Stimulus = Application.start();

  Stimulus.register("goodbye", class extends Controller {
    static targets = ["name"]

    ciao() {
      // console.log(this);
      const element = this.nameTarget;
      const name = element.value;

      console.log(`Ciao, ${name}!`);
    }

    connect() {
      console.log("Goodbye, Stimulus!", this.element)
    }
  });

  console.log('bro');
</script>

<div data-controller="goodbye">
  <input data-goodbye-target="name" type="text">
  <button data-action="click->goodbye#ciao">Ciao</button>
</div>
