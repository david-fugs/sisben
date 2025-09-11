$(document).ready(function () {
  $("#doc_encVenta").on("blur", function () {
    let documento = $(this).val();
    function actualizarTotal() {
      let total = 0;
      $("input[name='cant_integVenta[]']").each(function () {
        let valor = parseInt($(this).val()) || 0;
        total += valor;
      });
      $("#total_integrantes").val(total);

      // Actualizar también el campo cant_integVenta con la cantidad total
      $("#cant_integVenta").val($("input[name='cant_integVenta[]']").length);
    }
    $.ajax({
      url: "verificarIntegranteEncuesta.php",
      type: "POST",
      data: { documento: documento },
      dataType: "json", // Asegura que se parsea como objeto
      success: function (response) {
        if (response.status === "existe_integrante") {
          response.data.forEach(function (integrante, index) {
            console.log(`Integrante ${index + 1}:`, integrante);
            // Crear contenedor con estilo especial para solo lectura
            var integranteDiv = $("<div>")
              .addClass("formulario-dinamico")
              .css({
                "border": "2px solid #17a2b8",
                "background-color": "#f8f9fa"
              })
              .attr("data-readonly", "true");

            function createFormGroup(name, labelText, inputElement) {
              var group = $("<div>").addClass("form-group-dinamico");
              var label = $("<label>").attr("for", name).text(labelText);
              group.append(label, inputElement);
              return group;
            }
            var cantidadInput = $("<input>")
              .attr("type", "hidden")
              .attr("name", "")  // No incluir en el envío del formulario
              .addClass("form-control smaller-input")
              .val(1)
              .attr("readonly", true);

            var generoSelect = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "Identidad de Género (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .addClass("form-control smaller-input")
                .prop("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Identidad Genero</option>')
                .append(
                  '<option value="F"' +
                    (integrante.gen_integVenta === "F" ? " selected" : "") +
                    ">F</option>"
                )
                .append(
                  '<option value="M"' +
                    (integrante.gen_integVenta === "M" ? " selected" : "") +
                    ">M</option>"
                )
                .append(
                  '<option value="O"' +
                    (integrante.gen_integVenta === "O" ? " selected" : "") +
                    ">Otro</option>"
                )
            );

            var rangoEdadSelect = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "Rango de edad (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .addClass("form-control smaller-input")
                .prop("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Rango Edad</option>')
                .append(
                  '<option value="0 - 6"' +
                    (integrante.rango_integVenta == "1" ? " selected" : "") +
                    ">0 - 5</option>"
                )
                .append(
                  '<option value="7 - 12"' +
                    (integrante.rango_integVenta == "2" ? " selected" : "") +
                    ">6 - 12</option>"
                )
                .append(
                  '<option value="13 - 17"' +
                    (integrante.rango_integVenta == "3" ? " selected" : "") +
                    ">13 - 17</option>"
                )
                .append(
                  '<option value="18 - 28"' +
                    (integrante.rango_integVenta == "4" ? " selected" : "") +
                    ">18 - 28</option>"
                )
                .append(
                  '<option value="29 - 45"' +
                    (integrante.rango_integVenta == "5" ? " selected" : "") +
                    ">29 - 45</option>"
                )
                .append(
                  '<option value="46 - 64"' +
                    (integrante.rango_integVenta == "6" ? " selected" : "") +
                    ">46 - 64</option>"
                )
                .append(
                  '<option value="Mayor o igual a 65"' +
                    (integrante.rango_integVenta == "7" ? " selected" : "") +
                    ">Mayor o igual a 65</option>"
                )
            );

            // Aquí debes hacer lo mismo para: OrientacionSexual, condicionDiscapacidad, etc.
            // Por ejemplo:
             var OrientacionSexual =
                                    createFormGroup(
                                        "",  // No incluir name para evitar procesamiento
                                        "Orientación Sexual (Solo lectura)",
                                        $("<select>")
                                        .attr("name", "")  // No incluir en el envío del formulario
                                        .addClass("form-control smaller-input")
                                        .prop("disabled", true)  // Campo deshabilitado
                                        .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                                        .append('<option value="">Orientacion Sexual</option>')
                                        .append('<option value="Asexual"' + (integrante.orientacionSexual === 'Asexual' ? ' selected' : '') + '>Asexual</option>')
                                        .append('<option value="Bisexual"' + (integrante.orientacionSexual === 'Bisexual' ? ' selected' : '') + '>Bisexual</option>')
                                        .append('<option value="Heterosexual"' + (integrante.orientacionSexual === 'Heterosexual' ? ' selected' : '') + '>Heterosexual</option>')
                                        .append('<option value="Homosexual"' + (integrante.orientacionSexual === 'Homosexual' ? ' selected' : '') + '>Homosexual</option>')
                                        .append('<option value="Otro"' + (integrante.orientacionSexual === 'Otro' ? ' selected' : '') + '>Otro</option>')
                                    );

            var condicionDiscapacidad = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "Condición de Discapacidad (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .attr("id", "condicionDiscapacidadReadonly")
                .addClass("form-control smaller-input")
                .attr("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Condicion Discapacidad</option>')
                .append(
                  '<option value="Si"' +
                    (integrante.condicionDiscapacidad == "Si"
                      ? " selected"
                      : "") +
                    ">Si</option>"
                )
                .append(
                  '<option value="No"' +
                    (integrante.condicionDiscapacidad == "No"
                      ? " selected"
                      : "") +
                    ">No</option>"
                )
            );

            var discapacidadSelect = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "Tipo de Discapacidad (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .attr("id", "tipoDiscapacidadReadonly")
                .addClass("form-control smaller-input tipo-discapacidad")
                .attr("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Tipo Discapacidad</option>')
                .append(
                  '<option value="Auditiva"' +
                    (integrante.tipoDiscapacidad == "Auditiva"
                      ? " selected"
                      : "") +
                    ">Auditiva</option>"
                )
                .append(
                  '<option value="Física"' +
                    (integrante.tipoDiscapacidad == "Física"
                      ? " selected"
                      : "") +
                    ">Física</option>"
                )
                .append(
                  '<option value="Intelectual"' +
                    (integrante.tipoDiscapacidad == "Intelectual"
                      ? " selected"
                      : "") +
                    ">Intelectual</option>"
                )
                .append(
                  '<option value="Múltiple"' +
                    (integrante.tipoDiscapacidad == "Múltiple"
                      ? " selected"
                      : "") +
                    ">Múltiple</option>"
                )
                .append(
                  '<option value="Psicosocial"' +
                    (integrante.tipoDiscapacidad == "Psicosocial"
                      ? " selected"
                      : "") +
                    ">Psicosocial</option>"
                )
                .append(
                  '<option value="Sordoceguera"' +
                    (integrante.tipoDiscapacidad == "Sordoceguera"
                      ? " selected"
                      : "") +
                    ">Sordoceguera</option>"
                )
                .append(
                  '<option value="Visual"' +
                    (integrante.tipoDiscapacidad == "Visual"
                      ? " selected"
                      : "") +
                    ">Visual</option>"
                )
            );

            discapacidadSelect.attr("id", "grupoDiscapacidadReadonly");

            // Crear los demás campos con los datos de integrante
            var GrupoEtnico = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "Grupo Étnico (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .addClass("form-control smaller-input")
                .attr("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Indigena"' +
                    (integrante.grupoEtnico === "Indigena" ? " selected" : "") +
                    ">Indígena</option>"
                )
                .append(
                  '<option value="Negro(a) / Mulato(a) / Afrocolombiano(a)"' +
                    (integrante.grupoEtnico ===
                    "Negro(a) / Mulato(a) / Afrocolombiano(a)"
                      ? " selected"
                      : "") +
                    ">Negro(a) / Mulato(a) / Afrocolombiano(a)</option>"
                )
                .append(
                  '<option value="Raizal"' +
                    (integrante.grupoEtnico === "Raizal" ? " selected" : "") +
                    ">Raizal</option>"
                )
                .append(
                  '<option value="Palenquero de San Basilio"' +
                    (integrante.grupoEtnico === "Palenquero de San Basilio"
                      ? " selected"
                      : "") +
                    ">Palenquero de San Basilio</option>"
                )
                .append(
                  '<option value="Mestizo"' +
                    (integrante.grupoEtnico === "Mestizo" ? " selected" : "") +
                    ">Mestizo</option>"
                )
                .append(
                  '<option value="Gitano (rom)"' +
                    (integrante.grupoEtnico === "Gitano (rom)"
                      ? " selected"
                      : "") +
                    ">Gitano (rom)</option>"
                )
                .append(
                  '<option value="Ninguno"' +
                    (integrante.grupoEtnico === "Ninguno" ? " selected" : "") +
                    ">Ninguno</option>"
                )
            );
            var victima = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "¿Es víctima? (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .addClass("form-control smaller-input")
                .attr("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Si"' +
                    (integrante.victima === "Si" ? " selected" : "") +
                    ">Sí</option>"
                )
                .append(
                  '<option value="No"' +
                    (integrante.victima === "No" ? " selected" : "") +
                    ">No</option>"
                )
            );

            var mujerGestante = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "¿Mujer gestante? (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .addClass("form-control smaller-input")
                .attr("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Si"' +
                    (integrante.mujerGestante === "Si" ? " selected" : "") +
                    ">Sí</option>"
                )
                .append(
                  '<option value="No"' +
                    (integrante.mujerGestante === "No" ? " selected" : "") +
                    ">No</option>"
                )
            );

            var cabezaFamilia = createFormGroup(
              "",  // No incluir name para evitar procesamiento
              "¿Cabeza de familia? (Solo lectura)",
              $("<select>")
                .attr("name", "")  // No incluir en el envío del formulario
                .addClass("form-control smaller-input")
                .attr("disabled", true)  // Campo deshabilitado
                .css("background-color", "#f8f9fa")  // Color de fondo para indicar solo lectura
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Si"' +
                    (integrante.cabezaFamilia === "Si" ? " selected" : "") +
                    ">Sí</option>"
                )
                .append(
                  '<option value="No"' +
                    (integrante.cabezaFamilia === "No" ? " selected" : "") +
                    ">No</option>"
                )
            );

            var experienciaMigratoria = createFormGroup(
              "experienciaMigratoria[]",
              "¿Tiene experiencia migratoria?",
              $("<select>")
                .attr("name", "experienciaMigratoria[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Si"' +
                    (integrante.experienciaMigratoria === "Si"
                      ? " selected"
                      : "") +
                    ">Sí</option>"
                )
                .append(
                  '<option value="No"' +
                    (integrante.experienciaMigratoria === "No"
                      ? " selected"
                      : "") +
                    ">No</option>"
                )
            );

            var seguridadSalud = createFormGroup(
              "seguridadSalud[]",
              "Seguridad en salud",
              $("<select>")
                .attr("name", "seguridadSalud[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Regimen Contributivo"' +
                    (integrante.seguridadSalud === "Regimen Contributivo"
                      ? " selected"
                      : "") +
                    ">Régimen Contributivo</option>"
                )
                .append(
                  '<option value="Regimen Subsidiado"' +
                    (integrante.seguridadSalud === "Regimen Subsidiado"
                      ? " selected"
                      : "") +
                    ">Régimen Subsidiado</option>"
                )
                .append(
                  '<option value="Poblacion Vinculada"' +
                    (integrante.seguridadSalud === "Poblacion Vinculada"
                      ? " selected"
                      : "") +
                    ">Población Vinculada</option>"
                )
            );
            var nivelEducativo = createFormGroup(
              "nivelEducativo[]",
              "Nivel educativo",
              $("<select>")
                .attr("name", "nivelEducativo[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Ninguno"' +
                    (integrante.nivelEducativo == "Ninguno"
                      ? " selected"
                      : "") +
                    ">Ninguno</option>"
                )
                .append(
                  '<option value="Preescolar"' +
                    (integrante.nivelEducativo == "Preescolar"
                      ? " selected"
                      : "") +
                    ">Preescolar</option>"
                )
                .append(
                  '<option value="Primaria"' +
                    (integrante.nivelEducativo == "Primaria"
                      ? " selected"
                      : "") +
                    ">Primaria</option>"
                )
                .append(
                  '<option value="Secundaria"' +
                    (integrante.nivelEducativo == "Secundaria"
                      ? " selected"
                      : "") +
                    ">Secundaria</option>"
                )
                .append(
                  '<option value="Media Academica o Clasica"' +
                    (integrante.nivelEducativo === "Media Academica o Clasica"
                      ? " selected"
                      : "") +
                    ">Media Académica o Clásica</option>"
                )
                .append(
                  '<option value="Media Tecnica"' +
                    (integrante.nivelEducativo == "Media Tecnica"
                      ? " selected"
                      : "") +
                    ">Media Técnica</option>"
                )
                .append(
                  '<option value="Normalista"' +
                    (integrante.nivelEducativo == "Normalista"
                      ? " selected"
                      : "") +
                    ">Normalista</option>"
                )
                .append(
                  '<option value="Universitario"' +
                    (integrante.nivelEducativo == "Universitario"
                      ? " selected"
                      : "") +
                    ">Universitario</option>"
                )
                .append(
                  '<option value="Tecnica Profesional"' +
                    (integrante.nivelEducativo == "Tecnica Profesional"
                      ? " selected"
                      : "") +
                    ">Técnica Profesional</option>"
                )
                .append(
                  '<option value="Tecnologica"' +
                    (integrante.nivelEducativo == "Tecnologica"
                      ? " selected"
                      : "") +
                    ">Tecnológica</option>"
                )
                .append(
                  '<option value="Profesional"' +
                    (integrante.nivelEducativo == "Profesional"
                      ? " selected"
                      : "") +
                    ">Profesional</option>"
                )
                .append(
                  '<option value="Especializacion"' +
                    (integrante.nivelEducativo == "Especializacion"
                      ? " selected"
                      : "") +
                    ">Especialización</option>"
                )
            );

            var condicionOcupacion = createFormGroup(
              "condicionOcupacion[]",
              "Condición de ocupación",
              $("<select>")
                .attr("name", "condicionOcupacion[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Ama de casa"' +
                    (integrante.condicionOcupacion == "Ama de casa"
                      ? " selected"
                      : "") +
                    ">Ama de casa</option>"
                )
                .append(
                  '<option value="Buscando Empleo"' +
                    (integrante.condicionOcupacion == "Buscando empleo"
                      ? " selected"
                      : "") +
                    ">Buscando Empleo</option>"
                )
                .append(
                  '<option value="Desempleado(a)"' +
                    (integrante.condicionOcupacion == "Desempleado(a)"
                      ? " selected"
                      : "") +
                    ">Desempleado(a)</option>"
                )
                .append(
                  '<option value="Empleado(a)"' +
                    (integrante.condicionOcupacion == "Empleado(a)"
                      ? " selected"
                      : "") +
                    ">Empleado(a)</option>"
                )
                .append(
                  '<option value="Independiente"' +
                    (integrante.condicionOcupacion == "Independiente"
                      ? " selected"
                      : "") +
                    ">Independiente</option>"
                )
                .append(
                  '<option value="Estudiante"' +
                    (integrante.condicionOcupacion == "Estudiante"
                      ? " selected"
                      : "") +
                    ">Estudiante</option>"
                )
                .append(
                  '<option value="Pensionado(a)"' +
                    (integrante.condicionOcupacion == "Pensionado(a)"
                      ? " selected"
                      : "") +
                    ">Pensionado(a)</option>"
                )
                .append(
                  '<option value="Ninguno"' +
                    (integrante.condicionOcupacion == "Ninguno"
                      ? " selected"
                      : "") +
                    ">Ninguno</option>"
                )
            );
            console.log("condicionOcupacion:", integrante.condicionOcupacion);

            var eliminarBtn = $("<button>")
              .attr("type", "button")
              .addClass("btn btn-danger btn-sm")
              .text("Eliminar")
              .on("click", function () {
                integranteDiv.remove();
                actualizarTotal();
              });

            // Agrupamos todos los campos
            integranteDiv.append(
              cantidadInput,
              generoSelect,
              rangoEdadSelect,
              OrientacionSexual,
              condicionDiscapacidad,
              discapacidadSelect,
              GrupoEtnico,
              victima,
              mujerGestante,
              cabezaFamilia,
              experienciaMigratoria,
              seguridadSalud,
              nivelEducativo,
              condicionOcupacion,
              eliminarBtn
            );
            // Agregar al contenedor
            $("#integrantes-container").append(integranteDiv);

            // Mostrar u ocultar discapacidad según valor
            if (integrante.condicionDiscapacidad === "Si") {
              $("#grupoDiscapacidad").show();
            } else {
              $("#grupoDiscapacidad").hide();
              $("#tipoDiscapacidad").val("");
            }
          });
          // Obtener la cantidad total de integrantes
          const total = response.data.length;

          // Asignar el valor a los dos campos
          $("#total_integrantes").val(total);
          $("#cant_integVenta").val(total);
          // Escuchador por si cambia el valor de discapacidad
          $("#condicionDiscapacidad").on("change", function () {
            const valor = $(this).val();
            if (valor === "Si") {
              $("#grupoDiscapacidad").show();
            } else {
              $("#grupoDiscapacidad").hide();
              $("#tipoDiscapacidad").val("");
            }
          });
        } else {
          //  alert("El documento no es válido.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la solicitud AJAX:", error);
      },
    });
  });
});
