<?php
/**
 * MIGRACIÓN CORRECTA - SOLO ENRIQUECER MOVIMIENTOS EXISTENTES
 * 
 * Este script:
 * 1. Mantiene los 119 movimientos existentes
 * 2. Los enriquece con información de encventanilla
 * 3. NO crea nuevos registros
 * 4. Migra integrantes solo para esos 119 movimientos
 */

include("../../conexion.php");

try {
    echo "=== MIGRACIÓN CORRECTA - ENRIQUECER MOVIMIENTOS EXISTENTES ===\n\n";
    
    // Iniciar transacción
    $mysqli->autocommit(FALSE);
    
    echo "1. Verificando estado actual...\n";
    
    // Contar movimientos actuales
    $sql_count_mov = "SELECT COUNT(*) as total FROM movimientos";
    $result_count_mov = $mysqli->query($sql_count_mov);
    $count_movimientos = $result_count_mov->fetch_assoc()['total'];
    
    echo "   Movimientos actuales: $count_movimientos\n";
    
    if ($count_movimientos != 128) {
        echo "   ⚠️  ADVERTENCIA: Se esperaban 128 movimientos, hay $count_movimientos\n";
        echo "   ¿Desea continuar? (Presione Ctrl+C para cancelar)\n";
        // Continuar automáticamente en script
    }
    
    echo "\n2. Agregando columnas a tabla movimientos...\n";
    
    // Lista de columnas a agregar (solo si no existen)
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
    
    echo "\n3. Creando tabla integmovimientos_independiente...\n";
    
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
    
    echo "\n4. Enriqueciendo movimientos existentes con datos de encventanilla...\n";
    
    // Obtener todos los movimientos existentes
    $sql_movimientos = "SELECT id_movimiento, doc_encVenta FROM movimientos ORDER BY id_movimiento";
    $result_movimientos = $mysqli->query($sql_movimientos);
    
    $movimientos_enriquecidos = 0;
    $movimientos_sin_encuesta = 0;
    
    while ($mov = $result_movimientos->fetch_assoc()) {
        $id_movimiento = $mov['id_movimiento'];
        $doc_encVenta = $mysqli->real_escape_string($mov['doc_encVenta']);
        
        // Buscar la información en encventanilla
        $sql_encuesta = "SELECT * FROM encventanilla WHERE doc_encVenta = '$doc_encVenta' LIMIT 1";
        $result_encuesta = $mysqli->query($sql_encuesta);
        
        if ($result_encuesta->num_rows > 0) {
            $encuesta = $result_encuesta->fetch_assoc();
            
            // Actualizar el movimiento con información de encventanilla
            $sql_update = "UPDATE movimientos SET 
                nom_encVenta = '" . $mysqli->real_escape_string($encuesta['nom_encVenta']) . "',
                fec_reg_encVenta = '" . $mysqli->real_escape_string($encuesta['fec_reg_encVenta']) . "',
                tipo_documento = '" . $mysqli->real_escape_string($encuesta['tipo_documento']) . "',
                departamento_expedicion = '" . $mysqli->real_escape_string($encuesta['departamento_expedicion']) . "',
                ciudad_expedicion = '" . $mysqli->real_escape_string($encuesta['ciudad_expedicion']) . "',
                fecha_expedicion = '" . $mysqli->real_escape_string($encuesta['fecha_expedicion']) . "',
                dir_encVenta = '" . $mysqli->real_escape_string($encuesta['dir_encVenta']) . "',
                zona_encVenta = '" . $mysqli->real_escape_string($encuesta['zona_encVenta']) . "',
                id_com = " . (int)$encuesta['id_com'] . ",
                id_bar = " . (int)$encuesta['id_bar'] . ",
                otro_bar_ver_encVenta = '" . $mysqli->real_escape_string($encuesta['otro_bar_ver_encVenta']) . "',
                integra_encVenta = " . (int)$encuesta['integra_encVenta'] . ",
                num_ficha_encVenta = '" . $mysqli->real_escape_string($encuesta['num_ficha_encVenta']) . "',
                sisben_nocturno = '" . $mysqli->real_escape_string($encuesta['sisben_nocturno']) . "',
                estado_ficha = " . (int)$encuesta['estado_ficha'] . "
                WHERE id_movimiento = $id_movimiento";
            
            if ($mysqli->query($sql_update)) {
                $movimientos_enriquecidos++;
                echo "   ✅ Movimiento $id_movimiento enriquecido (Doc: $doc_encVenta)\n";
            } else {
                echo "   ❌ Error enriqueciendo movimiento $id_movimiento: " . $mysqli->error . "\n";
            }
        } else {
            $movimientos_sin_encuesta++;
            echo "   ⚠️  Movimiento $id_movimiento sin encuesta (Doc: $doc_encVenta)\n";
        }
    }
    
    echo "\n5. Migrando integrantes para movimientos existentes...\n";
    
    // Migrar integrantes solo para los movimientos que tienen encuesta
    $sql_movimientos_con_encuesta = "SELECT m.id_movimiento, m.doc_encVenta, e.id_encVenta 
                                     FROM movimientos m 
                                     INNER JOIN encventanilla e ON m.doc_encVenta = e.doc_encVenta";
    $result_mov_encuesta = $mysqli->query($sql_movimientos_con_encuesta);
    
    $integrantes_migrados = 0;
    
    while ($mov_enc = $result_mov_encuesta->fetch_assoc()) {
        $id_movimiento = $mov_enc['id_movimiento'];
        $doc_encVenta = $mysqli->real_escape_string($mov_enc['doc_encVenta']);
        $id_encVenta = $mov_enc['id_encVenta'];
        
        // Buscar integrantes para esta encuesta
        $sql_integrantes = "SELECT * FROM integventanilla WHERE id_encVenta = $id_encVenta";
        $result_integrantes = $mysqli->query($sql_integrantes);
        
        while ($integrante = $result_integrantes->fetch_assoc()) {
            $sql_insert_integrante = "INSERT INTO integmovimientos_independiente (
                id_movimiento, doc_encVenta, cant_integMovIndep, gen_integMovIndep, rango_integMovIndep,
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
    
    // Confirmar transacción
    $mysqli->commit();
    $mysqli->autocommit(TRUE);
    
    // Verificar resultado final
    $sql_count_final = "SELECT COUNT(*) as total FROM movimientos";
    $result_count_final = $mysqli->query($sql_count_final);
    $count_final = $result_count_final->fetch_assoc()['total'];
    
    echo "\n=== MIGRACIÓN CORRECTA COMPLETADA ===\n";
    echo "✅ Movimientos mantenidos: $count_final\n";
    echo "✅ Movimientos enriquecidos: $movimientos_enriquecidos\n";
    echo "⚠️  Movimientos sin encuesta: $movimientos_sin_encuesta\n";
    echo "✅ Integrantes migrados: $integrantes_migrados\n";
    echo "\n🎉 MIGRACIÓN CORRECTA COMPLETADA - SOLO SE ENRIQUECIERON LOS MOVIMIENTOS EXISTENTES\n";
    
} catch (Exception $e) {
    // Rollback en caso de error
    $mysqli->rollback();
    $mysqli->autocommit(TRUE);
    
    echo "\n❌ ERROR EN LA MIGRACIÓN: " . $e->getMessage() . "\n";
    echo "Se revirtieron todos los cambios.\n";
}
?>
