$(document).ready(function () {
  $("#doc_encVenta").on("blur", function () {
    let documento = $(this).val();

    $.ajax({
      url: "verificarIntegranteEncuesta.php",
      type: "POST",
      data: { documento: documento },
      dataType: "json", // Asegura que se parsea como objeto
      success: function (response) {
        if (response.status === "existe_integrante") {
          console.log("Integrantes encontrados:");
          response.data.forEach(function (integrante, index) {
            console.log(`Integrante ${index + 1}:`, integrante);
            var integranteDiv = $("<div>").addClass("formulario-dinamico");

            function createFormGroup(name, labelText, inputElement) {
              var group = $("<div>").addClass("form-group-dinamico");
              var label = $("<label>").attr("for", name).text(labelText);
              group.append(label, inputElement);
              return group;
            }
            var cantidadInput = $("<input>")
              .attr("type", "hidden")
              .attr("name", "cant_integVenta[]")
              .addClass("form-control smaller-input")
              .val(1)
              .attr("readonly", true);

            var generoSelect = createFormGroup(
              "gen_integVenta[]",
              "Identidad de Género",
              $("<select>")
                .attr("name", "gen_integVenta[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="F"' +
                    (integrante.gen_integVenta === "F" ? " selected" : "") +
                    ">Femenino</option>"
                )
                .append(
                  '<option value="M"' +
                    (integrante.gen_integVenta === "M" ? " selected" : "") +
                    ">Masculino</option>"
                )
                .append(
                  '<option value="O"' +
                    (integrante.gen_integVenta === "O" ? " selected" : "") +
                    ">Otro</option>"
                )
            );

            var rangoEdadSelect = createFormGroup(
              "rango_integVenta[]",
              "Rango de edad",
              $("<select>")
                .attr("name", "rango_integVenta[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="0 - 6"' +
                    (integrante.rango_integVenta == "1" ? " selected" : "") +
                    ">0 - 6</option>"
                )
                .append(
                  '<option value="7 - 12"' +
                    (integrante.rango_integVenta == "2" ? " selected" : "") +
                    ">7 - 12</option>"
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
            var OrientacionSexual = createFormGroup(
              "orientacionSexual[]",
              "Orientación Sexual",
              $("<input>")
                .attr("type", "text")
                .attr("name", "orientacionSexual[]")
                .addClass("form-control smaller-input")
                .val(integrante.orientacionSexual || "")
            );
            console.log("la condicion es: ", response.data.condicionDiscapacidad);
            var condicionDiscapacidad = createFormGroup(
              "condicionDiscapacidad[]",
              "Condición de Discapacidad",
              $("<select>")
                .attr("name", "condicionDiscapacidad[]")
                .attr("id", "condicionDiscapacidad")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Si"' +
                    (response.data.condicionDiscapacidad == "Si"
                      ? " selected"
                      : "") +
                    ">Sí</option>"
                )
                .append(
                  '<option value="No"' +
                    (response.data.condicionDiscapacidad == "No"
                      ? " selected"
                      : "") +
                    ">No</option>"
                )
            );

            var discapacidadSelect = createFormGroup(
              "tipoDiscapacidad[]",
              "Tipo de Discapacidad",
              $("<select>")
                .attr("name", "tipoDiscapacidad[]")
                .attr("id", "tipoDiscapacidad")
                .addClass("form-control smaller-input tipo-discapacidad")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Auditiva"' +
                    (response.data.tipoDiscapacidad == "Auditiva"
                      ? " selected"
                      : "") +
                    ">Auditiva</option>"
                )
                .append(
                  '<option value="Física"' +
                    (response.data.tipoDiscapacidad == "Física"
                      ? " selected"
                      : "") +
                    ">Física</option>"
                )
                .append(
                  '<option value="Intelectual"' +
                    (response.data.tipoDiscapacidad == "Intelectual"
                      ? " selected"
                      : "") +
                    ">Intelectual</option>"
                )
                .append(
                  '<option value="Múltiple"' +
                    (response.data.tipoDiscapacidad == "Múltiple"
                      ? " selected"
                      : "") +
                    ">Múltiple</option>"
                )
                .append(
                  '<option value="Psicosocial"' +
                    (response.data.tipoDiscapacidad == "Psicosocial"
                      ? " selected"
                      : "") +
                    ">Psicosocial</option>"
                )
                .append(
                  '<option value="Sordoceguera"' +
                    (response.data.tipoDiscapacidad == "Sordoceguera"
                      ? " selected"
                      : "") +
                    ">Sordoceguera</option>"
                )
                .append(
                  '<option value="Visual"' +
                    (response.data.tipoDiscapacidad == "Visual"
                      ? " selected"
                      : "") +
                    ">Visual</option>"
                )
            );

            discapacidadSelect.attr("id", "grupoDiscapacidad");

            // Crear los demás campos con los datos de response.data
            var GrupoEtnico = createFormGroup(
              "grupoEtnico[]",
              "Grupo Étnico",
              $("<select>")
                .attr("name", "grupoEtnico[]")
                .addClass("form-control smaller-input")
                .append('<option value="">Seleccione...</option>')
                .append(
                  '<option value="Indigena"' +
                    (response.data.grupoEtnico === "Indigena"
                      ? " selected"
                      : "") +
                    ">Indígena</option>"
                )
                .append(
                  '<option value="Negro(a) / Mulato(a) / Afrocolombiano(a)"' +
                    (response.data.grupoEtnico ===
                    "Negro(a) / Mulato(a) / Afrocolombiano(a)"
                      ? " selected"
                      : "") +
                    ">Negro(a) / Mulato(a) / Afrocolombiano(a)</option>"
                )
                .append(
                  '<option value="Raizal"' +
                    (response.data.grupoEtnico === "Raizal"
                      ? " selected"
                      : "") +
                    ">Raizal</option>"
                )
                .append(
                  '<option value="Palenquero de San Basilio"' +
                    (response.data.grupoEtnico === "Palenquero de San Basilio"
                      ? " selected"
                      : "") +
                    ">Palenquero de San Basilio</option>"
                )
                .append(
                  '<option value="Mestizo"' +
                    (response.data.grupoEtnico === "Mestizo"
                      ? " selected"
                      : "") +
                    ">Mestizo</option>"
                )
                .append(
                  '<option value="Gitano (rom)"' +
                    (response.data.grupoEtnico === "Gitano (rom)"
                      ? " selected"
                      : "") +
                    ">Gitano (rom)</option>"
                )
                .append(
                  '<option value="Ninguno"' +
                    (response.data.grupoEtnico === "Ninguno"
                      ? " selected"
                      : "") +
                    ">Ninguno</option>"
                )
            );

            // ...repite para los demás campos, igual que hiciste con `genero` y `rango`

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
