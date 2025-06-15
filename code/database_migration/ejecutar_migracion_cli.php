<?php
/**
 * MIGRACIÓN INDEPENDENCIA - VERSIÓN LÍNEA DE COMANDOS
 * 
 * Ejecuta la migración completa desde terminal
 */

include("../../conexion.php");

try {
    echo "=== INICIANDO MIGRACIÓN DE INDEPENDENCIA ===\n\n";
    
    // Iniciar transacción
    $mysqli->autocommit(FALSE);
    
    echo "1. Agregando columnas a tabla movimientos...\n";
    
    // Lista de columnas a agregar
    $columnas_agregar = [
        "nom_encVenta VARCHAR(255) DEFAULT NULL",
        "fec_reg_encVenta DATE DEFAULT NULL",
        "tipo_documento VARCHAR(50) DEFAULT NULL", 
        "departamento_expedicion VARCHAR(100) DEFAULT NULL",
        "ciudad_expedicion VARCHAR(100) DEFAULT NULL",
        "fecha_expedicion DATE DEFAULT NULL",
        "dir_encVenta VARCHAR(500) DEFAULT NULL",
        "zona_encVenta VARCHAR(50) DEFAULT NULL",
        "id_com INT DEFAULT NULL",
        "id_bar INT DEFAULT NULL", 
        "otro_bar_ver_encVenta VARCHAR(255) DEFAULT NULL",
        "integra_encVenta INT DEFAULT NULL",
        "num_ficha_encVenta VARCHAR(100) DEFAULT NULL",
        "sisben_nocturno VARCHAR(10) DEFAULT NULL",
        "estado_ficha INT DEFAULT 1",
        "fecha_alta_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP",
        "fecha_edit_movimiento DATETIME DEFAULT NULL"
    ];
    
    $columnas_agregadas = 0;
    foreach ($columnas_agregar as $columna) {
        $nombre_columna = explode(' ', $columna)[0];
        
        // Verificar si la columna ya existe
        $sql_check = "SHOW COLUMNS FROM movimientos LIKE '$nombre_columna'";
        $result_check = $mysqli->query($sql_check);
        
        if ($result_check->num_rows == 0) {
            $sql_add = "ALTER TABLE movimientos ADD COLUMN $columna";
            if ($mysqli->query($sql_add)) {
                echo "   ✅ Agregada: $nombre_columna\n";
                $columnas_agregadas++;
            } else {
                throw new Exception("Error agregando $nombre_columna: " . $mysqli->error);
            }
        } else {
            echo "   ➡️  Ya existe: $nombre_columna\n";
        }
    }
    
    echo "\n2. Creando tabla integmovimientos_independiente...\n";
    
    // Verificar si la tabla ya existe
    $sql_check_table = "SHOW TABLES LIKE 'integmovimientos_independiente'";
    $result_table = $mysqli->query($sql_check_table);
    
    if ($result_table->num_rows == 0) {
        $sql_create_table = "CREATE TABLE integmovimientos_independiente (
            id_integmov_indep INT AUTO_INCREMENT PRIMARY KEY,
            id_movimiento INT,
            doc_encVenta VARCHAR(20),
            cant_integVenta INT DEFAULT 1,
            gen_integVenta VARCHAR(10),
            rango_integVenta VARCHAR(50),
            orientacionSexual VARCHAR(100),
            condicionDiscapacidad VARCHAR(10),
            tipoDiscapacidad VARCHAR(100),
            grupoEtnico VARCHAR(100),
            victima VARCHAR(10),
            mujerGestante VARCHAR(10),
            cabezaFamilia VARCHAR(10),
            experienciaMigratoria VARCHAR(10),
            seguridadSalud VARCHAR(100),
            nivelEducativo VARCHAR(100),
            condicionOcupacion VARCHAR(100),
            estado_integVenta INT DEFAULT 1,
            fecha_alta_integVenta DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_edit_integVenta DATETIME DEFAULT NULL,
            id_usu INT,
            FOREIGN KEY (id_movimiento) REFERENCES movimientos(id_movimiento) ON DELETE CASCADE,
            INDEX idx_doc_encVenta (doc_encVenta),
            INDEX idx_id_movimiento (id_movimiento)
        )";
        
        if ($mysqli->query($sql_create_table)) {
            echo "   ✅ Tabla integmovimientos_independiente creada exitosamente\n";
        } else {
            throw new Exception("Error creando tabla: " . $mysqli->error);
        }
    } else {
        echo "   ➡️  Tabla integmovimientos_independiente ya existe\n";
    }
    
    echo "\n3. Migrando datos de encventanilla a movimientos...\n";
    
    // Obtener todos los registros de encventanilla
    $sql_select = "SELECT * FROM encventanilla ORDER BY id_encVenta";
    $result_select = $mysqli->query($sql_select);
    
    $registros_migrados = 0;
    $registros_actualizados = 0;
    
    while ($row = $result_select->fetch_assoc()) {
        $doc_encVenta = $mysqli->real_escape_string($row['doc_encVenta']);
        
        // Verificar si ya existe en movimientos
        $sql_check_mov = "SELECT id_movimiento FROM movimientos WHERE doc_encVenta = '$doc_encVenta' LIMIT 1";
        $result_check_mov = $mysqli->query($sql_check_mov);
        
        if ($result_check_mov->num_rows > 0) {
            // Actualizar registro existente
            $mov_row = $result_check_mov->fetch_assoc();
            $id_movimiento = $mov_row['id_movimiento'];
            
            $sql_update = "UPDATE movimientos SET 
                nom_encVenta = '" . $mysqli->real_escape_string($row['nom_encVenta']) . "',
                fec_reg_encVenta = '" . $mysqli->real_escape_string($row['fec_reg_encVenta']) . "',
                tipo_documento = '" . $mysqli->real_escape_string($row['tipo_documento']) . "',
                departamento_expedicion = '" . $mysqli->real_escape_string($row['departamento_expedicion']) . "',
                ciudad_expedicion = '" . $mysqli->real_escape_string($row['ciudad_expedicion']) . "',
                fecha_expedicion = '" . $mysqli->real_escape_string($row['fecha_expedicion']) . "',
                dir_encVenta = '" . $mysqli->real_escape_string($row['dir_encVenta']) . "',
                zona_encVenta = '" . $mysqli->real_escape_string($row['zona_encVenta']) . "',
                id_com = " . (int)$row['id_com'] . ",
                id_bar = " . (int)$row['id_bar'] . ",
                otro_bar_ver_encVenta = '" . $mysqli->real_escape_string($row['otro_bar_ver_encVenta']) . "',
                integra_encVenta = " . (int)$row['integra_encVenta'] . ",
                num_ficha_encVenta = '" . $mysqli->real_escape_string($row['num_ficha_encVenta']) . "',
                sisben_nocturno = '" . $mysqli->real_escape_string($row['sisben_nocturno']) . "',
                estado_ficha = " . (int)$row['estado_ficha'] . "
                WHERE id_movimiento = $id_movimiento";
            
            if ($mysqli->query($sql_update)) {
                $registros_actualizados++;
            }
            
        } else {
            // Crear nuevo registro
            $sql_insert = "INSERT INTO movimientos (
                doc_encVenta, nom_encVenta, fec_reg_encVenta, tipo_documento,
                departamento_expedicion, ciudad_expedicion, fecha_expedicion,
                dir_encVenta, zona_encVenta, id_com, id_bar, otro_bar_ver_encVenta,
                integra_encVenta, num_ficha_encVenta, sisben_nocturno, estado_ficha,
                tipo_movimiento, fecha_movimiento, observacion, id_usu
            ) VALUES (
                '" . $mysqli->real_escape_string($row['doc_encVenta']) . "',
                '" . $mysqli->real_escape_string($row['nom_encVenta']) . "',
                '" . $mysqli->real_escape_string($row['fec_reg_encVenta']) . "',
                '" . $mysqli->real_escape_string($row['tipo_documento']) . "',
                '" . $mysqli->real_escape_string($row['departamento_expedicion']) . "',
                '" . $mysqli->real_escape_string($row['ciudad_expedicion']) . "',
                '" . $mysqli->real_escape_string($row['fecha_expedicion']) . "',
                '" . $mysqli->real_escape_string($row['dir_encVenta']) . "',
                '" . $mysqli->real_escape_string($row['zona_encVenta']) . "',
                " . (int)$row['id_com'] . ",
                " . (int)$row['id_bar'] . ",
                '" . $mysqli->real_escape_string($row['otro_bar_ver_encVenta']) . "',
                " . (int)$row['integra_encVenta'] . ",
                '" . $mysqli->real_escape_string($row['num_ficha_encVenta']) . "',
                '" . $mysqli->real_escape_string($row['sisben_nocturno']) . "',
                " . (int)$row['estado_ficha'] . ",
                'MIGRACIÓN_INICIAL',
                NOW(),
                'Registro migrado desde encventanilla',
                " . (int)$row['id_usu'] . "
            )";
            
            if ($mysqli->query($sql_insert)) {
                $registros_migrados++;
            }
        }
    }
    
    echo "   ✅ Registros migrados: $registros_migrados\n";
    echo "   ✅ Registros actualizados: $registros_actualizados\n";
    
    echo "\n4. Migrando integrantes...\n";
      // Migrar integrantes existentes
    $sql_integrantes = "SELECT * FROM integVentanilla ORDER BY id_integVenta";
    $result_integrantes = $mysqli->query($sql_integrantes);
    
    $integrantes_migrados = 0;
    
    while ($integrante = $result_integrantes->fetch_assoc()) {
        // Buscar el id_movimiento correspondiente usando id_encVenta
        $id_encVenta = (int)$integrante['id_encVenta'];
        
        // Primero obtener el documento de la encuesta
        $sql_get_doc = "SELECT doc_encVenta FROM encventanilla WHERE id_encVenta = $id_encVenta";
        $result_get_doc = $mysqli->query($sql_get_doc);
        
        if ($result_get_doc->num_rows > 0) {
            $doc_data = $result_get_doc->fetch_assoc();
            $doc_encVenta = $mysqli->real_escape_string($doc_data['doc_encVenta']);
            
            // Buscar el id_movimiento correspondiente
            $sql_find_mov = "SELECT id_movimiento FROM movimientos WHERE doc_encVenta = '$doc_encVenta' LIMIT 1";
            $result_find_mov = $mysqli->query($sql_find_mov);
            
            if ($result_find_mov->num_rows > 0) {
                $mov_data = $result_find_mov->fetch_assoc();
                $id_movimiento = $mov_data['id_movimiento'];
                
                $sql_insert_integrante = "INSERT INTO integmovimientos_independiente (
                    id_movimiento, doc_encVenta, cant_integVenta, gen_integVenta, rango_integVenta,
                    orientacionSexual, condicionDiscapacidad, tipoDiscapacidad, grupoEtnico,
                    victima, mujerGestante, cabezaFamilia, experienciaMigratoria,
                    seguridadSalud, nivelEducativo, condicionOcupacion, id_usu
                ) VALUES (
                    $id_movimiento,
                    '$doc_encVenta',
                    " . (int)$integrante['cant_integVenta'] . ",
                    '" . $mysqli->real_escape_string($integrante['gen_integVenta']) . "',
                    '" . $mysqli->real_escape_string($integrante['rango_integVenta']) . "',
                    '" . $mysqli->real_escape_string($integrante['orientacionSexual']) . "',
                    '" . $mysqli->real_escape_string($integrante['condicionDiscapacidad']) . "',
                    '" . $mysqli->real_escape_string($integrante['tipoDiscapacidad']) . "',
                    '" . $mysqli->real_escape_string($integrante['grupoEtnico']) . "',
                    '" . $mysqli->real_escape_string($integrante['victima']) . "',
                    '" . $mysqli->real_escape_string($integrante['mujerGestante']) . "',
                    '" . $mysqli->real_escape_string($integrante['cabezaFamilia']) . "',
                    '" . $mysqli->real_escape_string($integrante['experienciaMigratoria']) . "',
                    '" . $mysqli->real_escape_string($integrante['seguridadSalud']) . "',
                    '" . $mysqli->real_escape_string($integrante['nivelEducativo']) . "',
                    '" . $mysqli->real_escape_string($integrante['condicionOcupacion']) . "',
                    " . (int)$integrante['id_usu'] . "
                )";
                
                if ($mysqli->query($sql_insert_integrante)) {
                    $integrantes_migrados++;
                }
            }
        }
    }
    
    echo "   ✅ Integrantes migrados: $integrantes_migrados\n";
    
    // Confirmar transacción
    $mysqli->commit();
    $mysqli->autocommit(TRUE);
    
    echo "\n=== MIGRACIÓN COMPLETADA EXITOSAMENTE ===\n";
    echo "✅ Estructura actualizada\n";
    echo "✅ Datos migrados: $registros_migrados nuevos, $registros_actualizados actualizados\n";
    echo "✅ Integrantes migrados: $integrantes_migrados\n";
    echo "\n🎉 EL SISTEMA AHORA ES COMPLETAMENTE INDEPENDIENTE\n";
    
} catch (Exception $e) {
    // Rollback en caso de error
    $mysqli->rollback();
    $mysqli->autocommit(TRUE);
    
    echo "\n❌ ERROR EN LA MIGRACIÓN: " . $e->getMessage() . "\n";
    echo "Se revirtieron todos los cambios.\n";
}
?>
