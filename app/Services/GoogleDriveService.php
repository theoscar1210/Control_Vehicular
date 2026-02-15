<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected ?Drive $service = null;
    protected ?string $rootFolderId;

    public function __construct()
    {
        $this->rootFolderId = config('google.drive.folder_id');
    }

    /**
     * Inicializa el cliente de Google Drive con Service Account.
     */
    protected function getService(): Drive
    {
        if ($this->service) {
            return $this->service;
        }

        $credentialsPath = config('google.drive.credentials_path');
        $fullPath = base_path($credentialsPath);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Archivo de credenciales de Google Drive no encontrado: {$credentialsPath}");
        }

        $client = new Client();
        $client->setAuthConfig($fullPath);
        $client->addScope(Drive::DRIVE);

        // Domain-Wide Delegation: impersonar usuario con quota de almacenamiento
        $impersonateEmail = config('google.drive.impersonate_email');
        if ($impersonateEmail) {
            $client->setSubject($impersonateEmail);
        }

        $this->service = new Drive($client);

        return $this->service;
    }

    /**
     * Verifica si el servicio está configurado correctamente.
     */
    public function isConfigured(): bool
    {
        $credentialsPath = config('google.drive.credentials_path');

        return $this->rootFolderId
            && $credentialsPath
            && file_exists(base_path($credentialsPath));
    }

    /**
     * Busca una carpeta por nombre dentro de un folder padre.
     * Si no existe, la crea.
     */
    public function findOrCreateFolder(string $name, ?string $parentId = null): string
    {
        $service = $this->getService();
        $parentId = $parentId ?? $this->rootFolderId;

        // Buscar carpeta existente
        $query = sprintf(
            "name = '%s' and '%s' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            addcslashes($name, "'"),
            $parentId
        );

        $results = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
            'pageSize' => 1,
        ]);

        if (count($results->getFiles()) > 0) {
            return $results->getFiles()[0]->getId();
        }

        // Crear carpeta
        $folder = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId],
        ]);

        $created = $service->files->create($folder, [
            'fields' => 'id',
        ]);

        return $created->getId();
    }

    /**
     * Sube un archivo a Google Drive.
     *
     * @param UploadedFile $file Archivo del request
     * @param string $type 'vehiculo' o 'conductor'
     * @param string $identifier Placa del vehículo o identificación del conductor
     * @param string $documentType Tipo de documento (SOAT, TECNOMECANICA, LICENCIA, etc.)
     * @return array{file_id: string, url: string} ID y URL del archivo en Drive
     */
    public function upload(UploadedFile $file, string $type, string $identifier, string $documentType): array
    {
        $service = $this->getService();

        // Crear estructura de carpetas: Control_Vehicular/Vehiculos/ABC123/
        $typeFolderName = $type === 'vehiculo' ? 'Vehiculos' : 'Conductores';
        $typeFolderId = $this->findOrCreateFolder($typeFolderName);
        $entityFolderId = $this->findOrCreateFolder(strtoupper($identifier), $typeFolderId);

        // Nombre descriptivo: SOAT_2026-02-15.pdf
        $extension = $file->getClientOriginalExtension();
        $fileName = strtoupper($documentType) . '_' . now()->format('Y-m-d') . '.' . $extension;

        // Subir archivo
        $driveFile = new DriveFile([
            'name' => $fileName,
            'parents' => [$entityFolderId],
        ]);

        $uploaded = $service->files->create($driveFile, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink',
        ]);

        // Hacer el archivo accesible con enlace
        $service->permissions->create($uploaded->getId(), new \Google\Service\Drive\Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]));

        return [
            'file_id' => $uploaded->getId(),
            'url' => $uploaded->getWebViewLink(),
        ];
    }

    /**
     * Sube un archivo desde una ruta local a Google Drive.
     */
    public function uploadFromPath(string $localPath, string $driveFileName, string $folderId): array
    {
        $service = $this->getService();

        $driveFile = new DriveFile([
            'name' => $driveFileName,
            'parents' => [$folderId],
        ]);

        $uploaded = $service->files->create($driveFile, [
            'data' => file_get_contents($localPath),
            'mimeType' => mime_content_type($localPath) ?: 'application/octet-stream',
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink',
        ]);

        $service->permissions->create($uploaded->getId(), new \Google\Service\Drive\Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]));

        return [
            'file_id' => $uploaded->getId(),
            'url' => $uploaded->getWebViewLink(),
        ];
    }

    /**
     * Elimina un archivo de Google Drive.
     */
    public function deleteFile(string $fileId): bool
    {
        try {
            $service = $this->getService();
            $service->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            Log::warning('No se pudo eliminar archivo de Google Drive', [
                'file_id' => $fileId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
