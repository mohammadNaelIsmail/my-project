<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Events CRUD</title>
</head>
<body>
  <h1>Events</h1>

  <input id="search" placeholder="Search by title or description" />
  <button onclick="applySearch()">Search</button>
  <button onclick="clearSearch()">Clear</button>

  <ul id="list"></ul>

  <h2 id="form-title">Add Event</h2>
  <form id="form">
    <input id="title" placeholder="Title" required />
    <input id="type" placeholder="Type" required />
    <input id="category" placeholder="Category" required />
    <textarea id="description" placeholder="Description"></textarea>
    <input id="event_img" placeholder="Event Image URL" />
    <input id="revenue" type="number" step="0.01" placeholder="Revenue" />
    <input id="start_day" type="date" required />
    <input id="end_day" type="date" required />
    <input id="start_hour" placeholder="Start Hour (e.g. 10:00)" required />
    <input id="end_hour" placeholder="End Hour (e.g. 18:00)" required />
    <input id="location" placeholder="Location" required />

    <button type="submit">Save</button>
    <button type="button" onclick="resetForm()">Cancel</button>
  </form>

  <script>
    const apiBase = "{{ url('/api') }}/events";
    let editId = null;
    let events = [];
    let currentSearch = '';

    async function load() {
      let url = apiBase;
if (currentSearch.trim() !== '') {
  url += `?search=${encodeURIComponent(currentSearch.trim())}`;
}

      const res = await fetch(url);
      const data = await res.json();
      events = data.data;
      const ul = document.getElementById('list');
      ul.innerHTML = '';
      events.forEach(event => {
        const li = document.createElement('li');
        li.innerHTML = `
          <strong>${event.title}</strong> (${event.start_day} to ${event.end_day}) - ${event.location}
          <button onclick="edit(${event.event_id})">Edit</button>
          <button onclick="remove(${event.event_id})">Delete</button>
        `;
        ul.appendChild(li);
      });
    }

    function applySearch() {
      const input = document.getElementById('search');
      currentSearch = input.value;
      load();
    }

    function clearSearch() {
      currentSearch = '';
      document.getElementById('search').value = '';
      load();
    }

    async function remove(id) {

        await fetch(`${apiBase}/${id}`, { method: 'DELETE' });
        load();

    }

    function edit(id) {
      const event = events.find(e => e.event_id === id);
      if (!event) return alert('Event not found');

      document.getElementById('title').value = event.title || '';
      document.getElementById('type').value = event.type || '';
      document.getElementById('category').value = event.category || '';
      document.getElementById('description').value = event.description || '';
      document.getElementById('event_img').value = event.event_img || '';
      document.getElementById('revenue').value = event.revenue || '';
      document.getElementById('start_day').value = event.start_day || '';
      document.getElementById('end_day').value = event.end_day || '';
      document.getElementById('start_hour').value = event.start_hour || '';
      document.getElementById('end_hour').value = event.end_hour || '';
      document.getElementById('location').value = event.location || '';

      editId = id;
      document.getElementById('form-title').innerText = 'Edit Event';
    }

    function resetForm() {
      document.getElementById('form').reset();
      editId = null;
      document.getElementById('form-title').innerText = 'Add Event';
    }

    document.getElementById('form').onsubmit = async e => {
      e.preventDefault();

      const body = {
        title: document.getElementById('title').value,
        type: document.getElementById('type').value,
        category: document.getElementById('category').value,
        description: document.getElementById('description').value,
        event_img: document.getElementById('event_img').value,
        revenue: document.getElementById('revenue').value ? parseFloat(document.getElementById('revenue').value) : null,
        start_day: document.getElementById('start_day').value,
        end_day: document.getElementById('end_day').value,
        start_hour: document.getElementById('start_hour').value,
        end_hour: document.getElementById('end_hour').value,
        location: document.getElementById('location').value,
      };

      const method = editId ? 'PUT' : 'POST';
      const url = editId ? `${apiBase}/${editId}` : apiBase;

      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body)
      });

      if (res.ok) {
        resetForm();
        load();
      } else {
        const err = await res.json();
        console.error(err);
        alert(JSON.stringify(err.errors || err.message || 'Error saving event'));
      }
    };

    load();
  </script>
</body>
</html>
