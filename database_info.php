<?php

use Illuminate\Support\Facades\DB;

// Conectar y mostrar información de la base de datos
echo "=== INFORMACIÓN DE LA BASE DE DATOS PostgreSQL ===" . PHP_EOL;
echo "Base de datos: " . env('DB_DATABASE') . PHP_EOL;
echo "Usuario: " . env('DB_USERNAME') . PHP_EOL;
echo "Host: " . env('DB_HOST') . ":" . env('DB_PORT') . PHP_EOL;
echo PHP_EOL;

try {
    // Obtener versión de PostgreSQL
    $version = DB::select('SELECT version()')[0]->version;
    echo "Versión PostgreSQL: " . $version . PHP_EOL;
    echo PHP_EOL;
    
    // Obtener todas las tablas
    echo "=== TABLAS EN LA BASE DE DATOS ===" . PHP_EOL;
    $tables = DB::select("
        SELECT tablename, schemaname 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename
    ");
    
    if (empty($tables)) {
        echo "No se encontraron tablas." . PHP_EOL;
    } else {
        foreach ($tables as $table) {
            echo "📄 " . $table->tablename . PHP_EOL;
            
            // Contar registros en cada tabla
            try {
                $count = DB::table($table->tablename)->count();
                echo "   └── Registros: " . $count . PHP_EOL;
            } catch (Exception $e) {
                echo "   └── Error al contar registros" . PHP_EOL;
            }
        }
    }
    
    echo PHP_EOL;
    echo "=== ESTRUCTURA DE TABLAS PRINCIPALES ===" . PHP_EOL;
    
    // Mostrar estructura de la tabla usuarios
    echo PHP_EOL . "📋 Tabla: users" . PHP_EOL;
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns 
        WHERE table_name = 'users' AND table_schema = 'public'
        ORDER BY ordinal_position
    ");
    
    foreach ($columns as $column) {
        $nullable = $column->is_nullable === 'YES' ? 'NULL' : 'NOT NULL';
        echo "  • " . $column->column_name . " (" . $column->data_type . ") " . $nullable . PHP_EOL;
    }
    
    // Mostrar algunos usuarios si existen
    $userCount = DB::table('users')->count();
    if ($userCount > 0) {
        echo PHP_EOL . "👥 Usuarios registrados: " . $userCount . PHP_EOL;
        $users = DB::table('users')->select('name', 'email', 'provider', 'created_at')->limit(5)->get();
        foreach ($users as $user) {
            $provider = $user->provider ?? 'tradicional';
            echo "  • " . $user->name . " (" . $user->email . ") - " . $provider . PHP_EOL;
        }
        if ($userCount > 5) {
            echo "  ... y " . ($userCount - 5) . " más" . PHP_EOL;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "✅ Consulta completada." . PHP_EOL;