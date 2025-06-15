<!DOCTYPE html>
<html>
<head>
    <title>Test Arrays</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Test de Arrays</h1>
    
    <form action="test_arrays_process.php" method="POST">
        <h3>Campos estáticos:</h3>
        <select name="gen_integVenta[]">
            <option value="">Seleccione</option>
            <option value="F">Femenino</option>
            <option value="M">Masculino</option>
        </select>
        
        <select name="rango_integVenta[]">
            <option value="">Seleccione</option>
            <option value="18 - 28">18 - 28</option>
            <option value="29 - 38">29 - 38</option>
        </select>
        
        <div id="container"></div>
        
        <button type="button" id="agregar">Agregar dinámico</button>
        <button type="submit">Enviar</button>
    </form>
    
    <script>
    $("#agregar").click(function() {
        var genero = $("<select>").attr("name", "gen_integVenta[]")
            .append('<option value="">Seleccione</option>')
            .append('<option value="F">Femenino</option>')
            .append('<option value="M">Masculino</option>');
            
        var rango = $("<select>").attr("name", "rango_integVenta[]")
            .append('<option value="">Seleccione</option>')
            .append('<option value="18 - 28">18 - 28</option>')
            .append('<option value="29 - 38">29 - 38</option>');
            
        var div = $("<div>").append(genero, " ", rango, "<br>");
        $("#container").append(div);
        
        console.log("Agregado. Total gen_integVenta:", $("select[name='gen_integVenta[]']").length);
    });
    
    $("form").on("submit", function() {
        console.log("Enviando. Total gen_integVenta:", $("select[name='gen_integVenta[]']").length);
        $("select[name='gen_integVenta[]']").each(function(i) {
            console.log("gen_integVenta[" + i + "]:", $(this).val());
        });
    });
    </script>
</body>
</html>
