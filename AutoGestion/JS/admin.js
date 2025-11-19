let users = [];
let editIndex = null;

const form = document.getElementById("userForm");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");

const userTableBody = document.getElementById("userTableBody");

form.addEventListener("submit", function (e) {
  e.preventDefault();
  const user = {
    name: nameInput.value,
    email: emailInput.value,
  
  };

  if (editIndex === null) {
    users.push(user);
  } else {
    users[editIndex] = user;
    editIndex = null;
  }

  form.reset();
  renderUsers();
});

function renderUsers() {
  userTableBody.innerHTML = "";
  users.forEach((user, index) => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${user.role}</td>
      <td class="actions">
        <button class="edit-btn" onclick="editUser(${index})">Editar</button>
        <button class="delete-btn" onclick="deleteUser(${index})">Eliminar</button>
      </td>
    `;

    userTableBody.appendChild(row);
  });
}

function editUser(index) {
  const user = users[index];
  nameInput.value = user.name;
  emailInput.value = user.email;
  editIndex = index;
}

function deleteUser(index) {
  if (confirm("¿Estás seguro de eliminar este usuario?")) {
    users.splice(index, 1);
    renderUsers();
  }
}
