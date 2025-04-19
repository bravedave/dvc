<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace bravedave\dvc\views\docs;  ?>

<script type="module">
  import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';

  // Initialize Mermaid to detect both class formats
  mermaid.initialize({ 
    startOnLoad: true,
    // Add this to handle GitHub-style code blocks
    codeBlocks: [
      { 
        selector: 'pre code.language-mermaid', 
        // Extract content from nested <code> tag
        getCode: block => block.textContent 
      }
    ]
  });
  
  // Manually process all valid blocks (including existing .mermaid ones)
  document.addEventListener('DOMContentLoaded', () => {
    mermaid.run({
      querySelector: '.mermaid, pre code.language-mermaid'
    });
  });
</script>