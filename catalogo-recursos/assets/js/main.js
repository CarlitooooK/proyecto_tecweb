// Búsqueda y filtrado en tiempo real
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput")
  const filterTipo = document.getElementById("filterTipo")
  const recursosGrid = document.getElementById("recursosGrid")

  if (searchInput && recursosGrid) {
    function filtrarRecursos() {
      const searchTerm = searchInput.value.toLowerCase()
      const tipoSeleccionado = filterTipo ? filterTipo.value : ""
      const cards = recursosGrid.querySelectorAll(".recurso-card")

      cards.forEach((card) => {
        const texto = card.textContent.toLowerCase()
        const tipo = card.dataset.tipo

        const matchSearch = texto.includes(searchTerm)
        const matchTipo = !tipoSeleccionado || tipo === tipoSeleccionado

        if (matchSearch && matchTipo) {
          card.style.display = "block"
        } else {
          card.style.display = "none"
        }
      })
    }

    searchInput.addEventListener("input", filtrarRecursos)
    if (filterTipo) {
      filterTipo.addEventListener("change", filtrarRecursos)
    }
  }
})

// Función para cargar datos con AJAX
function cargarDatos(url, callback) {
  fetch(url)
    .then((response) => response.json())
    .then((data) => callback(data))
    .catch((error) => console.error("Error:", error))
}

// Notificaciones
function mostrarNotificacion(mensaje, tipo = "success") {
  const alerta = document.createElement("div")
  alerta.className = `alert alert-${tipo}`
  alerta.textContent = mensaje
  alerta.style.position = "fixed"
  alerta.style.top = "20px"
  alerta.style.right = "20px"
  alerta.style.zIndex = "9999"
  alerta.style.minWidth = "300px"

  document.body.appendChild(alerta)

  setTimeout(() => {
    alerta.remove()
  }, 3000)
}
