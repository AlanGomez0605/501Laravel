<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostrar información completa de la base de datos PostgreSQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== INFORMACIÓN DE LA BASE DE DATOS PostgreSQL ===');
        $this->line('Base de datos: ' . env('DB_DATABASE'));
        $this->line('Usuario: ' . env('DB_USERNAME'));
        $this->line('Host: ' . env('DB_HOST') . ':' . env('DB_PORT'));
        $this->newLine();

        try {
            // Obtener versión de PostgreSQL
            $version = DB::select('SELECT version()')[0]->version;
            $this->info('Versión PostgreSQL: ' . $version);
            $this->newLine();
            
            // Obtener todas las tablas
            $this->info('=== TABLAS EN LA BASE DE DATOS ===');
            $tables = DB::select("
                SELECT tablename, schemaname 
                FROM pg_tables 
                WHERE schemaname = 'public' 
                ORDER BY tablename
            ");
            
            if (empty($tables)) {
                $this->warn('No se encontraron tablas.');
            } else {
                foreach ($tables as $table) {
                    // Contar registros en cada tabla
                    try {
                        $count = DB::table($table->tablename)->count();
                        $this->line("📄 {$table->tablename} ({$count} registros)");
                    } catch (\Exception $e) {
                        $this->line("📄 {$table->tablename} (error al contar)");
                    }
                }
            }
            
            $this->newLine();
            $this->info('=== ESTRUCTURA DE TABLA USERS ===');
            
            // Mostrar estructura de la tabla usuarios
            $columns = DB::select("
                SELECT column_name, data_type, is_nullable, column_default
                FROM information_schema.columns 
                WHERE table_name = 'users' AND table_schema = 'public'
                ORDER BY ordinal_position
            ");
            
            foreach ($columns as $column) {
                $nullable = $column->is_nullable === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $column->column_default ? " DEFAULT: {$column->column_default}" : '';
                $this->line("  • {$column->column_name} ({$column->data_type}) {$nullable}{$default}");
            }
            
            // Mostrar algunos usuarios si existen
            $userCount = DB::table('users')->count();
            if ($userCount > 0) {
                $this->newLine();
                $this->info("👥 Usuarios registrados: {$userCount}");
                $users = DB::table('users')->select('name', 'email', 'social_type', 'created_at')->limit(5)->get();
                foreach ($users as $user) {
                    $provider = $user->social_type ?? 'tradicional';
                    $date = $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : 'N/A';
                    $this->line("  • {$user->name} ({$user->email}) - {$provider} - {$date}");
                }
                if ($userCount > 5) {
                    $this->line("  ... y " . ($userCount - 5) . " usuarios más");
                }
            } else {
                $this->warn('No hay usuarios registrados aún.');
            }
            
            $this->newLine();
            $this->info('✅ Consulta completada exitosamente.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
