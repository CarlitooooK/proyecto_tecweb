let allResources = [];
let currentFilter = 'all';
let currentPage = 1;
const resourcesPerPage = 9;

$(document).ready(function() {
    loadCatalogResources();
    
    // Filtros
    $('.filter-btn').click(function() {
        currentFilter = $(this).data('filter');
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentPage = 1;
        renderResources();
    });
    
    // Búsqueda
    $('#search-catalog-btn').click(searchCatalog);
    $('#search-catalog').keypress(function(e) {
        if (e.which == 13) searchCatalog();
    });
});

function loadCatalogResources() {
    $.ajax({
        url: '../api/catalog-list.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                allResources = response.data;
                updateCatalogStats();
                renderResources();
            }
        }
    });
}

function renderResources() {
    // Filtrar recursos
    let filteredResources = allResources;
    
    if (currentFilter !== 'all') {
        filteredResources = allResources.filter(resource => {
            const extension = resource.archivo.split('.').pop().toLowerCase();
            return extension === currentFilter;
        });
    }
    
    // Aplicar búsqueda si existe
    const searchTerm = $('#search-catalog').val().toLowerCase();
    if (searchTerm) {
        filteredResources = filteredResources.filter(resource =>
            resource.nombre.toLowerCase().includes(searchTerm) ||
            resource.autor.toLowerCase().includes(searchTerm) ||
            resource.descripcion.toLowerCase().includes(searchTerm) ||
            resource.departamento.toLowerCase().includes(searchTerm)
        );
    }
    
    // Paginación
    const totalPages = Math.ceil(filteredResources.length / resourcesPerPage);
    const startIndex = (currentPage - 1) * resourcesPerPage;
    const endIndex = startIndex + resourcesPerPage;
    const pageResources = filteredResources.slice(startIndex, endIndex);
    
    // Renderizar recursos
    let html = '';
    
    pageResources.forEach(resource => {
        const extension = resource.archivo.split('.').pop().toLowerCase();
        const iconClass = getFileIconClass(extension);
        const fileSize = formatFileSize(resource.tamano || 0);
        
        html += `
        <div class="col-md-4 mb-4">
            <div class="card resource-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">
                            <i class="fas ${iconClass} me-2"></i>
                            ${resource.nombre}
                        </h5>
                        <span class="badge bg-secondary category-badge">${extension.toUpperCase()}</span>
                    </div>
                    
                    <p class="card-text text-muted">
                        <small>
                            <i class="fas fa-user"></i> ${resource.autor}<br>
                            <i class="fas fa-building"></i> ${resource.empresa || 'Independiente'}<br>
                            <i class="fas fa-calendar"></i> ${resource.fecha_creacion}
                        </small>
                    </p>
                    
                    <p class="card-text">${resource.descripcion.substring(0, 100)}...</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-primary">
                            <i class="fas fa-download"></i> ${resource.descargas || 0}
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-hdd"></i> ${fileSize}
                        </span>
                    </div>
                    
                    <button class="btn btn-primary download-btn mt-3" 
                            onclick="downloadCatalogResource(${resource.id})">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>
        </div>
        `;
    });
    
    $('#resources-grid').html(html);
    renderPagination(totalPages);
}

function getFileIconClass(extension) {
    const icons = {
        'pdf': 'fa-file-pdf text-danger',
        'zip': 'fa-file-archive text-warning',
        'rar': 'fa-file-archive text-warning',
        'json': 'fa-file-code text-info',
        'xml': 'fa-file-code text-info',
        'exe': 'fa-cogs text-secondary',
        'jar': 'fa-coffee text-warning',
        'default': 'fa-file text-muted'
    };
    
    return icons[extension] || icons.default;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function renderPagination(totalPages) {
    let html = '';
    
    // Botón Anterior
    html += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">
            <i class="fas fa-chevron-left"></i>
        </a>
    </li>
    `;
    
    // Números de página
    for (let i = 1; i <= totalPages; i++) {
        html += `
        <li class="page-item ${currentPage === i ? 'active' : ''}">
            <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
        </li>
        `;
    }
    
    // Botón Siguiente
    html += `
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">
            <i class="fas fa-chevron-right"></i>
        </a>
    </li>
    `;
    
    $('#pagination').html(html);
}

function changePage(page) {
    if (page < 1) return;
    
    currentPage = page;
    renderResources();
    
    // Scroll al inicio
    $('html, body').animate({
        scrollTop: $('#resources-grid').offset().top - 100
    }, 500);
}

function searchCatalog() {
    currentPage = 1;
    renderResources();
}

function downloadCatalogResource(id) {
    // Registrar descarga
    $.ajax({
        url: '../api/register-download.php',
        type: 'POST',
        data: { resource_id: id }
    });
    
    // Descargar archivo
    window.open(`../api/resource-download.php?id=${id}`, '_blank');
}

function updateCatalogStats() {
    if (allResources.length === 0) return;
    
    // Total recursos
    $('#total-files').text(allResources.length);
    
    // Total autores únicos
    const uniqueAuthors = [...new Set(allResources.map(r => r.autor))];
    $('#total-authors').text(uniqueAuthors.length);
    
    // Tamaño total
    const totalSize = allResources.reduce((sum, r) => sum + (r.tamano || 0), 0);
    $('#total-size').text((totalSize / (1024 * 1024 * 1024)).toFixed(2));
    
    // Total descargas
    const totalDownloads = allResources.reduce((sum, r) => sum + (r.descargas || 0), 0);
    $('#total-downloads').text(totalDownloads);
    
    // Top descargado
    const topResource = allResources.reduce((max, r) => 
        (r.descargas || 0) > (max.descargas || 0) ? r : max
    );
    $('#top-downloaded').text(topResource.descargas || 0);
}