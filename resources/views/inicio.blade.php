
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo para la línea vertical */
        .vertical-line {
            width: 3px;
            height: 100%;
            background-color: #000;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="row w-100 border border-dark rounded p-4 bg-white">
            <div class="col-md-4">
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-primary" id="btn-create">Crear</button>
                    <button type="button" class="btn btn-secondary" id="btn-list-users">Listar Usuarios</button>
                    <button type="button" class="btn btn-success" id="btn-update">Actualizar</button>
                    <button type="button" class="btn btn-danger" id="btn-delete">Eliminar</button>
                    <button type="button" class="btn btn-info" id="btn-stats">Estadísticas</button>
                </div>
            </div>
            <div class="col-md-1 d-flex justify-content-center align-items-center">
                <div class="vertical-line"></div>
            </div>
            <div class="col-md-7" id="info-col" style="max-height: 400px; overflow-y: auto;">
            <p>Selecciona una opción para ver la información.</p>
            <canvas id="miGrafica"></canvas>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // 1. Declaración de elementos del DOM
    const btnStats = document.getElementById('btn-stats');
    const btnUsers = document.getElementById('btn-list-users');
    const infoCol = document.getElementById('info-col');

   
    function crearGrafica(title, labels, data, canvasId, chartType) {
    // Se crea dinámicamente un contenedor y un canvas para cada gráfica
    const chartContainer = document.createElement('div');
    chartContainer.innerHTML = `<h3>${title}</h3><canvas id="${canvasId}"></canvas>`;
    infoCol.appendChild(chartContainer);

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: chartType, // esto define el tipo de grafica como line, bar, ect.
        data: {
            labels: labels,
            datasets: [{
                label: 'Usuarios Registrados',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function mostrarGraficas(data) {
    // 1. Limpiamos el área de contenido
    infoCol.innerHTML = '';

    // 2. Grafica por Día
    const datosDiarios = data.diario.reverse();
    const labelsDia = datosDiarios.map(item => item.fecha);
    const totalesDia = datosDiarios.map(item => item.total);
    crearGrafica('Gráfica de Usuarios Diarios', labelsDia, totalesDia, 'diariaCanvas', 'bar');

    // 3. Grafica por Semana
    const datosSemanales = data.semanal.reverse();
    const labelsSemana = datosSemanales.map(item => `Año ${item.año}, Semana ${item.semana}`);
    const totalesSemana = datosSemanales.map(item => item.total);
    crearGrafica('Gráfica de Usuarios Semanal', labelsSemana, totalesSemana, 'semanalCanvas', 'bar');

    // 4. Grafica por Mes
    const datosMensuales = data.mensual.reverse();
    const labelsMes = datosMensuales.map(item => `${item.año}-${item.mes}`);
    const totalesMes = datosMensuales.map(item => item.total);
    crearGrafica('Gráfica de Usuarios Mensual', labelsMes, totalesMes, 'mensualCanvas', 'bar');
}

    // 2. Agregar eventos a los botones
    btnUsers.addEventListener('click', () => {
        infoCol.innerHTML = 'cargando...';
        
        fetch('http://127.0.0.1:8000/api/user')
            .then(response => response.json())
            .then(data => {
                let html = '<ul class="list-group">';

                data.data.forEach(user => {
                    html += `<li class="list-group-item">
                    <h5 class="mb-1">Nombre de Usuario:</h5>
                    <p class="mb-1">${user.name}</p>
                    <h5 class="mb-1 mt-3">Email:</h5>
                    <p class="mb-1">${user.email}</p>
                    </li>`;
                });

                html += '</ul>';
                infoCol.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al obtener usuarios:', error);
                infoCol.innerHTML = `<p style="color:red;">Error al cargar los usuarios: ${error.message}</p>`;
            });
    });

    btnStats.addEventListener('click', () => {
    infoCol.innerHTML = 'cargando...';

    fetch('http://127.0.0.1:8000/api/estadisticas')
        .then(response => {
            if (!response.ok) {
                throw new Error('No se pudieron cargar los datos de la API.');
            }
            return response.json();
        })
        .then(data => {
            // Se crea una función para mostrar las tres gráficas
            mostrarGraficas(data);
        })
        .catch(error => {
            console.error('Error al obtener las estadísticas:', error);
            infoCol.innerHTML = `<p style="color:red;">Hubo un error: ${error.message}</p>`;
        });
});
</script>
</body>
</html>


