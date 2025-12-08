// NO usar import aquí; usamos Chart.js cargado desde CDN en dashboard.php

// Cargar gráficas al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  cargarGraficaTipoRecurso()
  cargarGraficaDiaSemana()
  cargarGraficaHoraDia()
  cargarGraficaLenguaje()
})

function cargarGraficaTipoRecurso() {
  fetch("./api/index.php?endpoint=estadisticas&tipo=tipo_recurso")
    .then((response) => response.json())
    .then((result) => {
      const data = result.data || []
      const ctx = document.getElementById("chartTipoRecurso")
      if (!ctx || !data.length) return

      new Chart(ctx, {
        type: "pie",
        data: {
          labels: data.map((item) => item.tipo_recurso),
          datasets: [
            {
              data: data.map((item) => item.total_descargas),
              backgroundColor: ["#2563eb", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6"],
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "bottom",
            },
          },
        },
      })
    })
    .catch(err => console.error('Error grafica tipo:', err))
}

function cargarGraficaDiaSemana() {
  fetch("./api/index.php?endpoint=estadisticas&tipo=dia_semana")
    .then((response) => response.json())
    .then((result) => {
      const data = result.data || []
      const ctx = document.getElementById("chartDiaSemana")
      if (!ctx || !data.length) return

      const diasEspanol = {
        Monday: "Lunes",
        Tuesday: "Martes",
        Wednesday: "Miércoles",
        Thursday: "Jueves",
        Friday: "Viernes",
        Saturday: "Sábado",
        Sunday: "Domingo",
      }

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: data.map((item) => diasEspanol[item.dia_semana] || item.dia_semana),
          datasets: [
            {
              label: "Descargas",
              data: data.map((item) => item.total),
              backgroundColor: "#2563eb",
            },
          ],
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true } },
        },
      })
    })
    .catch(err => console.error('Error grafica dias:', err))
}

function cargarGraficaHoraDia() {
  fetch("./api/index.php?endpoint=estadisticas&tipo=hora_dia")
    .then((response) => response.json())
    .then((result) => {
      const data = result.data || []
      const ctx = document.getElementById("chartHoraDia")
      if (!ctx || !data.length) return

      new Chart(ctx, {
        type: "line",
        data: {
          labels: data.map((item) => item.hora_dia + ":00"),
          datasets: [
            {
              label: "Descargas",
              data: data.map((item) => item.total),
              borderColor: "#2563eb",
              backgroundColor: "rgba(37, 99, 235, 0.1)",
              tension: 0.4,
              fill: true,
            },
          ],
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
      })
    })
    .catch(err => console.error('Error grafica horas:', err))
}

function cargarGraficaLenguaje() {
  fetch("./api/index.php?endpoint=estadisticas&tipo=lenguaje")
    .then((response) => response.json())
    .then((result) => {
      const data = result.data || []
      const ctx = document.getElementById("chartLenguaje")
      if (!ctx || !data.length) return

      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: data.map((item) => item.lenguaje_programacion),
          datasets: [
            {
              data: data.map((item) => item.total_descargas),
              backgroundColor: ["#2563eb", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6", "#06b6d4"],
            },
          ],
        },
        options: { responsive: true, plugins: { legend: { position: "bottom" } } },
      })
    })
    .catch(err => console.error('Error grafica lenguaje:', err))
}

function editarRecurso(id) {
  window.location.href = "editar-recurso.php?id=" + id
}

function eliminarRecurso(id) {
  if (!confirm("¿Estás seguro de que deseas eliminar este recurso?")) return

  fetch("./api/eliminar-recurso.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id: id }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log('Respuesta eliminar:', data)
      if (data && data.success) {
        location.reload()
      } else {
        alert("Error al eliminar el recurso")
      }
    })
    .catch((err) => {
      console.error('Error fetch eliminar:', err)
      alert("Error al conectarse con el servidor")
    })
}
