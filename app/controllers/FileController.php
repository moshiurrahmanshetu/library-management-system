<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * File controller.
 *
 * Serves uploaded files from the storage/uploads directory.
 * Prevents directory traversal and only allows image content types.
 */
class FileController extends Controller
{
    /**
     * Allowed MIME types for served uploads.
     *
     * @var array
     */
    private array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /**
     * Serve an uploaded file by relative path.
     *
     * @param string $path
     * @return void
     */
    public function serve(string $path): void
    {
        $baseDir = realpath(ROOT_PATH . '/storage/uploads');

        if ($baseDir === false) {
            $this->notFound();
        }

        $filePath = realpath($baseDir . '/' . ltrim($path, '/'));

        if ($filePath === false || !str_starts_with($filePath, $baseDir)) {
            $this->notFound();
        }

        if (!is_file($filePath) || !is_readable($filePath)) {
            $this->notFound();
        }

        $mimeType = mime_content_type($filePath);

        if (!in_array($mimeType, $this->allowedMimes, true)) {
            $this->notFound();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=86400');

        readfile($filePath);
        exit;
    }

    /**
     * Return a 404 response.
     *
     * @return void
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo 'File not found.';
        exit;
    }
}
