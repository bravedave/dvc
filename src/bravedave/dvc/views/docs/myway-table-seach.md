# My Way - Table Search and Line Numbers Implementation

[Docs](.) | [My Way](myway.md) | **Table Search and Line Numbers Implementation**

---

## Table Search and Line Numbers Implementation

**Purpose:** This addendum documents the implementation of table search functionality with automatic line number updates, providing a seamless user experience for filtering and navigating table data.

### Implementation Overview

Table search filters rows based on input text. Line numbers automatically update to show sequential numbering of visible rows.

### Core Implementation

**Table Setup with Line Numbers:**

```javascript
/**
 * table sorting and line numbers
 * - implies there is a cell with class js-line-number
 */
table
  .on('update-line-numbers', _.table._line_numbers_)
  .trigger('update-line-numbers');
```

**Search Implementation:**

```javascript
/**
 * table search - fully self-contained
 * - $tr is a jQuery object
 * - return true from the prefilter to show the row
 */
_.table.search(searchInput, table);
```

### Key Components

1. Line Numbers
   - CSS class js-line-number marks cells for line numbers
   - update-line-numbers event recalculates line numbers
   - Line numbers update when table content changes
1. Search
   - Filters table rows based on search text
   - Optional prefilter function for custom logic
   - Returns true/false to show/hide rows
1. Integration
   - Line numbers initialize with table creation
   - Search automatically updates line numbers
   - Works with existing table sorting

### Implementation Requirements

#### HTML Structure

```html
<table id="data-table">
  <thead>
    <tr>
      <th class="js-line-number">#</th>
      <th>Subscription Name</th>
      <th>User Name</th>
      <th>Cost per Month</th>
      <th>Renewal Date</th>
      <th>Assigned Date</th>
      <th>Status</th>
      <th>Notes</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="js-line-number">1</td>
      <td>Adobe Creative Suite</td>
      <td>John Doe</td>
      <td>$52.99</td>
      <td>2024-12-31</td>
      <td>2024-01-15</td>
      <td>Active</td>
      <td>Design team license</td>
    </tr>
    <!-- more rows -->
  </tbody>
</table>
```

#### JavaScript Integration

```javascript
(_ => {
  const table = $('#data-table');
  const searchInput = $('#search-input');

  // Initialize line numbers
  table
    .on('update-line-numbers', _.table._line_numbers_)
    .trigger('update-line-numbers');

  // Setup search functionality - fully self-contained
  _.table.search(searchInput, table);

  // Line numbers update automatically after search
})(_brayworth_);
```

#### Implementation Details

- Row count updates in table header
- Rows show/hide based on search text
