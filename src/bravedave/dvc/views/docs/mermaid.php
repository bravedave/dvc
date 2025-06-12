<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace bravedave\dvc\views\docs;

use bravedave\dvc\strings;

?>

<script src="<?= strings::url('assets/mermaid/js/mermaid.min.js') ?>"></script>
<script type="module">

  // Initialize Mermaid to detect both class formats
  mermaid.initialize({
    startOnLoad: true,
    // Add this to handle GitHub-style code blocks
    codeBlocks: [{
      selector: 'pre code.language-mermaid',
      // Extract content from nested <code> tag
      getCode: block => block.textContent
    }]
  });

  // Manually process all valid blocks (including existing .mermaid ones)
  document.addEventListener('DOMContentLoaded', () => {
    mermaid.run({
      querySelector: '.mermaid, pre code.language-mermaid'
    });
  });
</script>