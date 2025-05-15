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
                    (integrante.rango_integVenta === "0 - 6"
                      ? " selected"
                      : "") +
                    ">0 - 6</option>"
                )
                .append(
                  '<option value="7 - 12"' +
                    (integrante.rango_integVenta === "7 - 12"
                      ? " selected"
                      : "") +
                    ">7 - 12</option>"
                )
                .append(
                  '<option value="13 - 17"' +
                    (integrante.rango_integVenta === "13 - 17"
                      ? " selected"
                      : "") +
                    ">13 - 17</option>"
                )
                .append(
                  '<option value="18 - 28"' +
                    (integrante.rango_integVenta === "18 - 28"
                      ? " selected"
                      : "") +
                    ">18 - 28</option>"
                )
                .append(
                  '<option value="29 - 45"' +
                    (integrante.rango_integVenta === "29 - 45"
                      ? " selected"
                      : "") +
                    ">29 - 45</option>"
                )
                .append(
                  '<option value="46 - 64"' +
                    (integrante.rango_integVenta === "46 - 64"
                      ? " selected"
                      : "") +
                    ">46 - 64</option>"
                )
                .append(
                  '<option value="Mayor o igual a 65"' +
                    (integrante.rango_integVenta === "Mayor o igual a 65"
                      ? " selected"
                      : "") +
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
              // ...los demás campos que generes aquí...
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
