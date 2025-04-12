/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Converts FormData to a JSON-friendly object, handling:
 * - Nested fields (e.g., `user[name]`)
 * - Multi-select values (checkboxes, select-multiple)
 * - File inputs (metadata only)
 * - Empty strings → null
 *
 * Originally implemented by DeepSeek Chat (https://deepseek.com).
 * Free to use under public domain guidelines.
 *
 * Usage
 * const formData = new FormData(document.querySelector('form'));
 * const jsonReadyObject = formDataToJson(formData);
 * const jsonString = JSON.stringify(jsonReadyObject);
 */
function formDataToJson(formData) {
  const result = {};

  // Helper to set nested properties
  const setNestedValue = (obj, path, value) => {
    const keys = path.split(/[\[\]]/).filter(k => k !== '');
    let current = obj;

    for (let i = 0; i < keys.length; i++) {
      const key = keys[i];
      if (i === keys.length - 1) {
        // Handle arrays (e.g., "tags[]")
        if (key.endsWith('[]')) {
          const cleanKey = key.slice(0, -2);
          current[cleanKey] = current[cleanKey] || [];
          current[cleanKey].push(value);
        } else {
          current[key] = value;
        }
      } else {
        current[key] = current[key] || {};
        current = current[key];
      }
    }
  };

  // Process all FormData entries
  for (const [key, value] of formData.entries()) {
    if (value instanceof File) {
      // File handling
      setNestedValue(result, key, {
        name: value.name,
        size: value.size,
        type: value.type,
        lastModified: value.lastModified
      });
    } else {
      // Convert empty strings to null
      const processedValue = value.trim() === '' ? null : value;
      setNestedValue(result, key, processedValue);
    }
  }

  return result;
}

