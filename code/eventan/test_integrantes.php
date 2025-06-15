<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Integrantes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .formulario-dinamico { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 5px; 
        }
        .form-row-custom { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 15px; 
        }
        .form-group-dinamico { 
            flex: 1; 
        }
        .smaller-input { 
            font-size: 0.9em; 
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Test Formulario Integrantes</h2>
        
        <form action="test_integrantes_process.php" method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nombre de prueba:</label>
                    <input type="text" name="nombre_prueba" class="form-control" value="TEST">
                </div>
                <div class="col-md-3">
                    <label>Cantidad a agregar:</label>
                    <input type="number" id="cant_integVenta" name="cant_integVenta" class="form-control" min="1" max="5" value="1">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary" id="agregar">Agregar Integrantes</button>
                </div>
            </div>
            
            <div id="integrantes-container" style="border: 2px solid blue; padding: 10px;">
                <h5>Contenedor de Integrantes (dentro del form)</h5>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success">Enviar Formulario</button>
                <button type="button" id="debug" class="btn btn-info">Debug Campos</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            function actualizarTotal() {
                let total = $("input[name='cant_integVenta[]']").length;
                console.log("Total integrantes:", total);
            }

            $("#agregar").click(function() {
                var inputCantidad = $("#cant_integVenta");
                var cantidadValor = parseInt(inputCantidad.val());

                if (!cantidadValor || cantidadValor <= 0) {
                    alert("Por favor, ingresa una cantidad válida de integrantes.");
                    return;
                }

                for (var i = 0; i < cantidadValor; i++) {
                    var integranteDiv = $("<div>").addClass("formulario-dinamico");
                    
                    // Header del integrante
                    var headerDiv = $("<div>").addClass("d-flex justify-content-between align-items-center mb-3");
                    headerDiv.append($("<h6>").addClass("mb-0").text("Integrante"));
                    
                    // Botón eliminar
                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger btn-sm")
                        .text("Eliminar")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            actualizarTotal();
                        });

                    // Campo hidden para cantidad
                    var cantidadInput = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "cant_integVenta[]")
                        .addClass("form-control smaller-input")
                        .val(1);

                    // Primera fila de campos
                    var primeraFila = $("<div>").addClass("form-row-custom");
                    
                    var generoGroup = $("<div>").addClass("form-group-dinamico");
                    generoGroup.append($("<label>").text("* Identidad Género"));
                    var generoSelect = $("<select>")
                        .attr("name", "gen_integVenta[]")
                        .addClass("form-select smaller-input")
                        .append('<option value="">Seleccione</option>')
                        .append('<option value="F">Femenino</option>')
                        .append('<option value="M">Masculino</option>')
                        .append('<option value="O">Otro</option>');
                    generoGroup.append(generoSelect);

                    var rangoGroup = $("<div>").addClass("form-group-dinamico");
                    rangoGroup.append($("<label>").text("* Rango Edad"));
                    var rangoEdadSelect = $("<select>")
                        .attr("name", "rango_integVenta[]")
                        .addClass("form-select smaller-input")
                        .append('<option value="">Seleccione</option>')
                        .append('<option value="0 - 6">0 - 6</option>')
                        .append('<option value="7 - 12">7 - 12</option>')
                        .append('<option value="13 - 17">13 - 17</option>')
                        .append('<option value="18 - 28">18 - 28</option>')
                        .append('<option value="29 - 38">29 - 38</option>')
                        .append('<option value="39 - 45">39 - 45</option>')
                        .append('<option value="46 - 64">46 - 64</option>')
                        .append('<option value="Mayor o igual a 65">Mayor o igual a 65</option>');
                    rangoGroup.append(rangoEdadSelect);

                    primeraFila.append(generoGroup, rangoGroup);

                    // Ensamblar el integrante completo
                    integranteDiv.append(headerDiv, eliminarBtn, cantidadInput, primeraFila);
                    $("#integrantes-container").append(integranteDiv);
                    
                    console.log("Integrante agregado. Total de campos gen_integVenta:", $("select[name='gen_integVenta[]']").length);
                    console.log("Total de campos rango_integVenta:", $("select[name='rango_integVenta[]']").length);
                }

                actualizarTotal();
            });

            // Debug para verificar envío del formulario
            $("form").on("submit", function(e) {
                console.log("=== FORMULARIO ENVIÁNDOSE ===");
                console.log("Campos gen_integVenta encontrados:", $("select[name='gen_integVenta[]']").length);
                console.log("Campos rango_integVenta encontrados:", $("select[name='rango_integVenta[]']").length);
                
                // Mostrar valores seleccionados
                $("select[name='gen_integVenta[]']").each(function(index) {
                    console.log("gen_integVenta[" + index + "]:", $(this).val());
                });
                $("select[name='rango_integVenta[]']").each(function(index) {
                    console.log("rango_integVenta[" + index + "]:", $(this).val());
                });
                
                // Verificar si los campos están dentro del formulario
                console.log("¿Campos dentro del form?", $(this).find("select[name='gen_integVenta[]']").length);
            });

            // Botón de debug
            $("#debug").click(function() {
                console.log("=== DEBUG MANUAL ===");
                console.log("Campos gen_integVenta:", $("select[name='gen_integVenta[]']").length);
                console.log("Campos rango_integVenta:", $("select[name='rango_integVenta[]']").length);
                console.log("Contenedor integrantes:", $("#integrantes-container").children().length);
                
                // Verificar si están dentro del form
                console.log("Dentro del form:", $("form").find("select[name='gen_integVenta[]']").length);
            });
        });
    </script>
</body>
</html>
