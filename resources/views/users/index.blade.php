@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">User Management</h2>

    <!-- Alerts -->
    <div id="alertBox" class="alert d-none" role="alert"></div>

    <!-- Create User Form -->
    <div class="card mb-4">
        <div class="card-header">Create New User</div>
        <div class="card-body">
            <form id="createUserForm">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                <input type="password" class="form-control mb-2" name="password_confirmation" placeholder="Confirm Password" required>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>

    <!-- User List -->
    <div class="card">
        <div class="card-header">Users</div>
        <div class="card-body">
            <table class="table table-bordered" id="userTable">
                <thead>
                    <tr>
                        <th width="25%">Name</th>
                        <th width="25%">Email</th>
                        <th width="50%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Users will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const alertBox = document.getElementById("alertBox");

    function showAlert(message, type = 'success') {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
        setTimeout(() => alertBox.classList.add('d-none'), 3000);
    }

    function loadUsers() {
        fetch("/api/users")
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector("#userTable tbody");
                tbody.innerHTML = "";
                data.data.forEach(user => {
                    const row = document.createElement("tr");
                    row.dataset.id = user.id;
                    row.innerHTML = `
                        <td><input type="text" class="form-control name" value="${user.name}"></td>
                        <td><input type="email" class="form-control email" value="${user.email}"></td>
                        <td>
                            <button class="btn btn-sm btn-info update-btn">Update</button>
                            <button class="btn btn-sm btn-danger delete-btn">Delete</button>
                            <button class="btn btn-sm btn-warning change-pass-btn">Change Password</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
    }

    document.addEventListener("DOMContentLoaded", function () {
        loadUsers();

        // Create user
        document.getElementById("createUserForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const form = new FormData(this);

            fetch("/api/users", {
                method: "POST",
                headers: { "Accept": "application/json" },
                body: form
            }).then(res => res.json())
              .then(data => {
                  if (data.success) {
                      showAlert("User created!");
                      this.reset();
                      loadUsers();
                  } else {
                      showAlert("Error: " + JSON.stringify(data.data), "danger");
                  }
              });
        });

        // Update, Delete, Change Password
        document.querySelector("#userTable").addEventListener("click", function (e) {
            const row = e.target.closest("tr");
            const userId = row.dataset.id;

            if (e.target.classList.contains("update-btn")) {
                const name = row.querySelector(".name").value;
                const email = row.querySelector(".email").value;

                fetch(`/api/users/${userId}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ name, email })
                }).then(res => res.json())
                  .then(data => {
                      if (data.success) {
                          showAlert("User updated!");
                          loadUsers();
                      } else {
                          showAlert("Error updating user: " + JSON.stringify(data.data), "danger");
                      }
                  });

            } else if (e.target.classList.contains("delete-btn")) {
                if (confirm("Are you sure you want to delete this user?")) {
                    fetch(`/api/users/${userId}`, {
                        method: "DELETE",
                        headers: { "Accept": "application/json" }
                    }).then(res => res.json())
                      .then(data => {
                          showAlert("User deleted!");
                          loadUsers();
                      });
                }

            } else if (e.target.classList.contains("change-pass-btn")) {
                const password = prompt("Enter new password:");
                const password_confirmation = prompt("Confirm new password:");
                if (password && password === password_confirmation) {
                    fetch(`/api/users/${userId}/change-password`, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ password, password_confirmation })
                    }).then(res => res.json())
                      .then(data => {
                          if (data.success) {
                              showAlert("Password updated!");
                          } else {
                              showAlert("Error: " + JSON.stringify(data.data), "danger");
                          }
                      });
                } else {
                    showAlert("Passwords do not match.", "danger");
                }
            }
        });
    });
</script>
@endpush
