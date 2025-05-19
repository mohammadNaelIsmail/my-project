<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Humans CRUD</title>
</head>
<body>
  <h1>Humans</h1>

  <ul id="list"></ul>

  <h2 id="form-title">Add Human</h2>
  <form id="form">
    <input id="name" placeholder="Name" required>
    <input id="age" type="number" placeholder="Age" required>
    <input id="email" type="email" placeholder="Email" required>
    <input id="location" placeholder="Location">
    <input id="creditcard" placeholder="Credit Card">
    <input id="password" type="password" placeholder="Password">
    <button type="submit">Save</button>
    <button type="button" onclick="resetForm()">Cancel</button>
  </form>

  <script>
    const api = "{{ url('/api') }}/humans";
    let editId = null;


    
    async function load() {
      const res = await fetch(api);
      const data = await res.json();
      const ul = document.getElementById('list');
      ul.innerHTML = '';
      data.data.forEach(h => {
        const li = document.createElement('li');
        li.innerHTML = `
          ${h.name} (${h.age}) - ${h.email}
          <button onclick="edit(${h.human_id})">Edit</button>
          <button onclick="remove(${h.human_id})">Delete</button>
        `;
        ul.appendChild(li);
      });
    }

    async function remove(id) {
      if (confirm("Delete this human?")) {
        await fetch(`${api}/${id}`, { method: 'DELETE' });
        load();
      }
    }

    function edit(id) {
      const h = [...document.querySelectorAll('#list li')][id - 1].innerText.split(' - ')[0];
      const human = humans.find(x => x.human_id === id);
      document.getElementById('name').value = human.name;
      document.getElementById('age').value = human.age;
      document.getElementById('email').value = human.email;
      document.getElementById('location').value = human.location || '';
      document.getElementById('creditcard').value = human.creditcard || '';
      document.getElementById('password').value = '';
      editId = id;
      document.getElementById('form-title').innerText = 'Edit Human';
    }

    function resetForm() {
      document.getElementById('form').reset();
      editId = null;
      document.getElementById('form-title').innerText = 'Add Human';
    }

    document.getElementById('form').onsubmit = async e => {
      e.preventDefault();
      const body = {
        name: name.value,
        age: age.value,
        email: email.value,
        location: location.value,
        creditcard: creditcard.value,
      };
      if (password.value) body.password = password.value;

      const method = editId ? 'PUT' : 'POST';
      const url = editId ? `${api}/${editId}` : api;

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
        alert(err.message || 'Error');
      }
    };

    let humans = [];
    async function fetchHumans() {
      const res = await fetch(api);
      const json = await res.json();
      humans = json.data;
      load();
    }

    fetchHumans();
  </script>
</body>
</html>
