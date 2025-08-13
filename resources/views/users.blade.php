<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Usuarios</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Nuevo Usuario</a>
    </div>

    <table class="table table-striped table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody id="user-table-body">
            <tr>
                <td colspan="3" class="text-center">Cargando usuarios...</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    async function loadUsers() {
        try {
            let response = await fetch("{{ url('/api/users') }}");
            let data = await response.json();

            let tbody = document.getElementById('user-table-body');
            tbody.innerHTML = '';

            data.forEach(user => {
                let row = `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } catch (error) {
            console.error("Error cargando usuarios:", error);
        }
    }

    loadUsers();
</script>

</body>
</html>
