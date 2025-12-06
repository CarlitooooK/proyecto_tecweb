$(document).ready(function () {
    loadResources();

    // CARGAR LISTA DE RECURSOS
    function loadResources() {
        $.ajax({
            url: "",
            type: "GET",
            dataType: "json",
            success: function (data) {
                renderTable(data);
            },
            error: function () {
                showAlert("Error al cargar los recursos.", "danger");
            }
        });
    }

    // DIBUJAR TABLA
    function renderTable(resources) {
        let html = "";

        resources.forEach(r => {
            html += `
                <tr>
                    <td>${r.id}</td>
                    <td>${r.nombre}</td>
                    <td>${r.autor}</td>
                    <td>${badgeTipo(r.tipo)}</td>
                    <td>${r.departamento ?? "-"}</td>
                    <td>${r.fecha ?? "-"}</td>
                    <td>
                        <button class="btn btn-warning btn-sm editBtn" data-id="${r.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${r.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a class="btn btn-primary btn-sm" href="../backend/resource-download.php?id=${r.id}">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>`;
        });

        $("#resourcesTableBody").html(html);
    }

    // BADGE SEGÚN EL TIPO
    function badgeTipo(tipo) {
        const clases = {
            pdf: "badge-pdf",
            zip: "badge-zip",
            json: "badge-json",
            xml: "badge-xml"
        };

        return `<span class="badge badge-tipo ${clases[tipo] || 'bg-secondary'}">${tipo.toUpperCase()}</span>`;
    }

    // SUBMIT: AGREGAR / EDITAR
    $("#resourceForm").submit(function (e) {
        e.preventDefault();

        let formData = new FormData();
        formData.append("id", $("#resourceId").val());
        formData.append("nombre", $("#nombre").val());
        formData.append("autor", $("#autor").val());
        formData.append("departamento", $("#departamento").val());
        formData.append("empresa", $("#empresa").val());
        formData.append("fecha", $("#fecha").val());
        formData.append("descripcion", $("#descripcion").val());
        formData.append("tipo", $("#tipo").val());

        let file = $("#archivo")[0].files[0];
        if (file) formData.append("archivo", file);

        const url = $("#resourceId").val()
            ? ""
            : "";

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                showAlert("Recurso guardado correctamente.", "success");
                clearForm();
                loadResources();
            },
            error: function () {
                showAlert("Error al guardar el recurso.", "danger");
            }
        });
    });

    // BOTÓN EDITAR
    $(document).on("click", ".editBtn", function () {
        const id = $(this).data("id");

        $.ajax({
            url: "",
            type: "GET",
            data: { id },
            dataType: "json",
            success: function (r) {
                $("#resourceId").val(r.id);
                $("#nombre").val(r.nombre);
                $("#autor").val(r.autor);
                $("#departamento").val(r.departamento);
                $("#empresa").val(r.empresa);
                $("#fecha").val(r.fecha);
                $("#descripcion").val(r.descripcion);
                $("#tipo").val(r.tipo);

                $("#formTitle").text("Editar Recurso");
                $("#formCollapse").collapse("show");
            },
            error: function () {
                showAlert("Error al cargar información del recurso.", "danger");
            }
        });
    });

    // BOTÓN ELIMINAR
    $(document).on("click", ".deleteBtn", function () {
        const id = $(this).data("id");

        if (confirm("¿Seguro que deseas eliminar este recurso?")) {
            $.ajax({
                url: "",
                type: "POST",
                data: { id },
                success: function () {
                    showAlert("Recurso eliminado correctamente.", "success");
                    loadResources();
                },
                error: function () {
                    showAlert("Error al eliminar el recurso.", "danger");
                }
            });
        }
    });

    // LIMPIAR FORMULARIO
    function clearForm() {
        $("#resourceId").val("");
        $("#resourceForm")[0].reset();
        $("#formTitle").text("Agregar Nuevo Recurso");
        $("#formCollapse").collapse("hide");
    }
    
    // ALERTAS
    function showAlert(msg, type) {
        $("#alertContainer").html(`
            <div class="alert alert-${type} alert-dismissible fade show mt-3">
                ${msg}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }
});
