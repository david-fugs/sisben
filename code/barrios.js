$(document).ready(function () {
    $("#id_barrios").select2({
      placeholder: "Seleccione barrio",
      minimumInputLength: 2,
      allowClear: true, //  esto permite borrar la selección
      ajax: {
        url: "../buscar_barrios.php",
        dataType: "json",
        delay: 250,
        data: function (params) {
          return {
            q: params.term,
          };
        },
        processResults: function (data) {
          return {
            results: data,
          };
        },
        cache: true,
      },
    });
  
    // Aquí va el evento para asignar la zona al select
    $('#id_barrios').on('select2:select', function (e) {
        var data = e.params.data;
        let zona = data.zona?.toUpperCase() || '';
      
        // Mapeo de zona para que coincida con los valores del select
        if (zona === 'URBANO') zona = 'URBANA';
        if (zona === 'RURAL') zona = 'RURAL';
      
        if (zona === 'URBANA' || zona === 'RURAL') {
          $('#zona_encVenta').val(zona);
        } else {
          $('#zona_encVenta').val(''); // limpia si no hay coincidencia
        }
      });
      
  
  $("#id_barrios").on("change", function () {
    const selectedValue = $(this).val();

    if (selectedValue == "1897") {
      $("#otro_barrio_container").show();
    } else {
      $("#otro_barrio_container").hide();
    }

    let id_barrio = $(this).val();

    if (id_barrio !== "") {
      $.ajax({
        url: "../comunaGet.php",
        type: "GET",
        data: {
          id_barrio: id_barrio,
        },
        success: function (response) {
          $("#id_comunas").html(response);
          $("#id_comunas").removeAttr("disabled");
        },
        error: function () {
          alert("Error al obtener las comunas.");
        },
      });
    } else {
      $("#id_comunas").html('<option value="">Seleccione comuna</option>');
    }
  });
});
