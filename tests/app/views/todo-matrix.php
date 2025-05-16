<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/ ?>

<div class="container p-4" id="<?= $_container = strings::rand() ?>"></div>
<script type="module">
  import {
    h,
    render
  } from 'preact';
  import {
    useState,
    useEffect,
    useRef
  } from 'hooks';
  import htm from 'htm';

  const html = htm.bind(h);
  const _ = _brayworth_;
  const container = $('#<?= $_container ?>');

  const Matrix = () => {
    const [data, setData] = useState([]);
    const [error, setError] = useState(null);
    const [refresh, setRefresh] = useState(0); // State to trigger re-fetch
    const newItemRef = useRef(null); // Create a ref for the input field

    const fetchData = () => {

      const payload = {
        action: 'get-todo-data'
      };

      _.api(_.url('<?= $this->route ?>'), payload)
        .then(data => setData(data))
        .catch(e => setError(e.description || e));
    };

    useEffect(() => fetchData(), [refresh]);

    if (error) return html`<div>Error: ${error}</div>`;
    if (!data.length) return html`<div>Loading...</div>`;

    const Delete = ({
      dto
    }) => {

      return html`
        <button type="button" class="btn btn-light" onClick=${handleDelete} data-id="${dto.id}">
          <i class="bi bi-trash"></i>
        </button>`;
    };

    const Description = ({
      dto,
      newItemRef
    }) => {
      const [isEditing, setIsEditing] = useState(false);
      const [value, setValue] = useState(dto.description);
      const inputRef = useRef(null); // Create a ref for the input field

      const handleSave = () => {
        setIsEditing(false);

        // Save the updated description
        const payload = {
          action: 'todo-update',
          id: dto.id,
          description: value
        };

        _.api(_.url('<?= $this->route ?>'), payload)
          .then(d => {})
          .catch(_.growl);
      };

      const handleKeyDown = e => {

        if (e.key === 'Enter') {

          handleSave();
        } else if (e.key === 'Escape') {

          setIsEditing(false);
          setValue(dto.description); // Reset to original value

          if (newItemRef.current) {
            newItemRef.current.focus(); // Focus on the new item input field
          }
        }
      };

      useEffect(() => {
        if (isEditing && inputRef.current) {
          inputRef.current.focus(); // Focus the input field when editing starts
        }
      }, [isEditing]); // Run this effect when `isEditing` changes

      return isEditing ?
        html`
        <input
          ref=${inputRef}
          type="text"
          class="form-control"
          value=${value}
          onInput=${(e) => setValue(e.target.value)}
          onKeyDown=${handleKeyDown}
          autoFocus
        />` :
        html`<div class="p-2 border border-light" onClick=${() => setIsEditing(true)}>${value}</div>`;
    };

    const handleDelete = function(e) {

      _.hideContexts(e);

      const payload = {
        action: 'todo-delete',
        id: e.currentTarget.dataset.id
      };

      _.ask.alert.confirm({
        title: 'Confirm Delete',
        text: 'Are you sure ?'
      }).then(e => {

        _.api(_.url('<?= $this->route ?>'), payload)
          .then(d => setRefresh(prev => prev + 1))
          .catch(_.growl);
      });
    };

    const newItem = ({
      inputRef
    }) => {
      const [value, setValue] = useState('');

      useEffect(() => {
        if (inputRef.current) {
          inputRef.current.focus(); // Set focus to the input field on load
        }
      }, []); // Empty dependency array ensures this runs only once on mount

      const handleAdd = () => {
        if (!value.trim()) return; // Prevent empty submissions

        const payload = {
          action: 'todo-add',
          description: value.trim()
        };

        _.api(_.url('<?= $this->route ?>'), payload)
          .then(d => {
            setValue(''); // Clear the input field
            setRefresh(prev => prev + 1); // Trigger re-fetch
          })
          .catch(_.growl);
      };

      const handleKeyPress = e => {
        if (e.key === 'Enter') handleAdd();
      };

      return html`
        <div class="row g-2">
          <div class="col">
            <input
              ref=${inputRef}
              type="text"
              class="form-control"
              name="description"
              placeholder="new todo"
              value=${value}
              onInput=${(e) => setValue(e.target.value)}
              onKeyPress=${handleKeyPress} />
          </div>
          <div class="col-auto">
            <button
              type="button"
              class="btn btn-primary"
              onClick=${handleAdd}>
              Add
            </button>
          </div>
        </div>
      `;
    };

    return html`
      <h4><?= config::label_todo ?></h4>
      ${data.map(dto => html`
        <div class="row g-2 mb-2" data-id="${dto.id}">
          <div class="col"><${Description} dto=${dto} newItemRef=${newItemRef} /></div>
          <div class="col-auto"><${Delete} dto=${dto} /></div>
        </div>
        `)}
      <${newItem} inputRef=${newItemRef} />`;
  };

  render(html`<${Matrix} />`, container[0]);
</script>