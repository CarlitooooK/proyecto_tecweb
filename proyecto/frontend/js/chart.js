$(document).ready(function () {
    loadCharts();
    setInterval(loadCharts, 30000);

    function loadCharts() {
        loadTipos();
        loadDias();
        loadHoras();
    }

    // ============================
    // TIPOS MÁS DESCARGADOS
    // ============================
    function loadTipos() {
        $.ajax({
            url: "",
            type: "GET",
            dataType: "json",
            success: function (data) {
                const ctx = document.getElementById("chartTipos");
                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            borderWidth: 1
                        }]
                    }
                });
            }
        });
    }

    // ============================
    // DESCARGAS POR DÍA
    // ============================
    function loadDias() {
        $.ajax({
            url: "",
            type: "GET",
            dataType: "json",
            success: function (data) {
                const ctx = document.getElementById("chartDias");
                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            borderWidth: 1
                        }]
                    }
                });
            }
        });
    }

    // ============================
    // ACTIVIDAD POR HORA
    // ============================
    function loadHoras() {
        $.ajax({
            url: "",
            type: "GET",
            dataType: "json",
            success: function (data) {
                const ctx = document.getElementById("chartHoras");
                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            borderWidth: 2,
                            tension: 0.3
                        }]
                    }
                });
            }
        });
    }
});
