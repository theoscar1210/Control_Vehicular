<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--keep=7 : Días de backups locales a mantener}
                            {--skip-drive : No subir a Google Drive}';

    protected $description = 'Generar backup de la base de datos y opcionalmente subirlo a Google Drive';

    public function handle(): int
    {
        $this->info("===========================================");
        $this->info(" BACKUP DE BASE DE DATOS");
        $this->info(" " . now()->format('d/m/Y H:i:s'));
        $this->info("===========================================");
        $this->newLine();

        try {
            // 1. Generar SQL dump
            $this->info("1. Exportando base de datos...");
            $sqlContent = $this->generarDump();
            $this->info("   SQL generado: " . $this->formatBytes(strlen($sqlContent)));

            // 2. Comprimir con gzip
            $this->info("2. Comprimiendo...");
            $compressed = gzencode($sqlContent, 9);
            $this->info("   Comprimido: " . $this->formatBytes(strlen($compressed)));

            // 3. Guardar localmente
            $fileName = 'backup_' . now()->format('Y-m-d_His') . '.sql.gz';
            $backupDir = storage_path('app/backups');

            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filePath = $backupDir . '/' . $fileName;
            file_put_contents($filePath, $compressed);
            $this->info("3. Guardado en: storage/app/backups/{$fileName}");

            // 4. Subir a Google Drive (si está configurado)
            if (!$this->option('skip-drive')) {
                $this->subirADrive($filePath, $fileName);
            } else {
                $this->info("4. Google Drive omitido (--skip-drive)");
            }

            // 5. Limpiar backups antiguos
            $eliminados = $this->limpiarBackupsAntiguos($backupDir);
            $this->info("5. Limpieza: {$eliminados} backup(s) antiguo(s) eliminado(s)");

            // Resumen
            $this->newLine();
            $this->info("===========================================");
            $this->info(" BACKUP COMPLETADO EXITOSAMENTE");
            $this->info("===========================================");

            Log::info("Backup de base de datos creado: {$fileName} (" . $this->formatBytes(strlen($compressed)) . ")");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error durante el backup: " . $e->getMessage());
            Log::error("Error en BackupDatabase: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Genera el dump SQL completo usando PDO.
     */
    private function generarDump(): string
    {
        $pdo = DB::connection()->getPdo();
        $sql = "-- Backup generado por Control Vehicular\n";
        $sql .= "-- Fecha: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Base de datos: " . config('database.connections.mysql.database') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

        // Obtener todas las tablas
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        $this->info("   Tablas encontradas: " . count($tables));

        foreach ($tables as $table) {
            $this->line("   → Exportando: {$table}");

            // Estructura de la tabla
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "-- -----------------------------------------------\n";
            $sql .= "-- Tabla: {$table}\n";
            $sql .= "-- -----------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createStmt['Create Table'] . ";\n\n";

            // Datos de la tabla en lotes
            $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();

            if ($count > 0) {
                $batchSize = 500;
                $offset = 0;

                while ($offset < $count) {
                    $stmt = $pdo->query("SELECT * FROM `{$table}` LIMIT {$batchSize} OFFSET {$offset}");
                    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    if (empty($rows)) {
                        break;
                    }

                    $columns = array_keys($rows[0]);
                    $columnList = '`' . implode('`, `', $columns) . '`';

                    $values = [];
                    foreach ($rows as $row) {
                        $escapedValues = array_map(function ($value) use ($pdo) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return $pdo->quote($value);
                        }, array_values($row));

                        $values[] = '(' . implode(', ', $escapedValues) . ')';
                    }

                    $sql .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                    $sql .= implode(",\n", $values) . ";\n\n";

                    $offset += $batchSize;
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return $sql;
    }

    /**
     * Sube el backup a Google Drive.
     */
    private function subirADrive(string $filePath, string $fileName): void
    {
        $driveService = app(GoogleDriveService::class);

        if (!$driveService->isConfigured()) {
            $this->warn("4. Google Drive no configurado, se omite la subida.");
            return;
        }

        $this->info("4. Subiendo a Google Drive...");

        try {
            $backupFolderId = $driveService->findOrCreateFolder('Backups');
            $result = $driveService->uploadFromPath($filePath, $fileName, $backupFolderId);
            $this->info("   Subido: {$result['url']}");
            Log::info("Backup subido a Google Drive: {$result['file_id']}");
        } catch (\Exception $e) {
            $this->warn("   Error al subir a Drive: " . $e->getMessage());
            Log::warning("No se pudo subir backup a Google Drive", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Elimina backups locales más antiguos que los días configurados.
     */
    private function limpiarBackupsAntiguos(string $backupDir): int
    {
        $keep = (int) $this->option('keep');
        $fechaLimite = Carbon::now()->subDays($keep)->timestamp;
        $eliminados = 0;

        $archivos = glob($backupDir . '/backup_*.sql.gz');

        foreach ($archivos as $archivo) {
            if (filemtime($archivo) < $fechaLimite) {
                unlink($archivo);
                $eliminados++;
            }
        }

        return $eliminados;
    }

    /**
     * Formatea bytes a unidad legible.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
