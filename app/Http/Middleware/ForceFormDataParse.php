<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Log; // Elimina esta línea
use Symfony\Component\HttpFoundation\FileBag;
use Illuminate\Http\UploadedFile;

class ForceFormDataParse
{
    public function handle(Request $request, Closure $next): Response
    {
        // Log::info('--- ForceFormDataParse middleware está en ejecución ---'); // Eliminar
        // Log::info('Method recibido en middleware: ' . $request->method()); // Eliminar
        // Log::info('Content-Type header: ' . $request->header('Content-Type')); // Eliminar

        $rawContent = @file_get_contents('php://input');
        // Log::info('Raw php://input en middleware (primeros 500 chars): ' . substr($rawContent, 0, 500)); // Eliminar

        if (($request->isMethod('PATCH') || ($request->isMethod('POST') && $request->has('_method')))
            && Str::startsWith($request->header('Content-Type'), 'multipart/form-data')
        ) {

            // Log::info('Middleware: Condición de procesamiento manual CUMPLIDA. Intentando procesar.'); // Eliminar

            if (!empty($request->all()) || !empty($request->files->all())) {
                // Log::info('Middleware: Request ya tiene datos. No se necesita parseo manual.'); // Eliminar
                return $next($request);
            }

            // Log::info('Middleware: Request vacío. Procediendo a parsear rawContent.'); // Eliminar

            preg_match('/boundary=(.*)$/', $request->header('Content-Type'), $matches);
            $boundary = $matches[1] ?? null;

            if ($boundary) {
                $parts = explode('--' . $boundary, $rawContent);
                $parsedData = [];
                $uploadedFiles = [];

                foreach ($parts as $part) {
                    if (empty(trim($part)) || Str::endsWith(trim($part), '--')) {
                        continue;
                    }

                    list($headers, $body) = explode("\r\n\r\n", $part, 2);
                    $headers = explode("\r\n", $headers);
                    $body = rtrim($body, "\r\n");

                    $name = null;
                    $filename = null;
                    $contentType = null;

                    foreach ($headers as $header) {
                        if (Str::startsWith($header, 'Content-Disposition:')) {
                            preg_match('/name="([^"]+)"/', $header, $nameMatches);
                            $name = $nameMatches[1] ?? null;
                            preg_match('/filename="([^"]+)"/', $header, $filenameMatches);
                            $filename = $filenameMatches[1] ?? null;
                        } elseif (Str::startsWith($header, 'Content-Type:')) {
                            $contentType = trim(Str::after($header, 'Content-Type:'));
                        }
                    }

                    if ($name) {
                        if ($filename) {
                            $tmpFilePath = tempnam(sys_get_temp_dir(), 'laravel_upload_patch');
                            file_put_contents($tmpFilePath, $body);

                            $uploadedFile = new UploadedFile(
                                $tmpFilePath,
                                $filename,
                                $contentType,
                                null,
                                true
                            );

                            $uploadedFiles[$name] = $uploadedFile;
                            // Log::info("Middleware: Instancia UploadedFile creada para {$name}: {$tmpFilePath}"); // Eliminar
                        } else {
                            $parsedData[$name] = $body;
                            // Log::info("Middleware: Campo de texto parseado manualmente: {$name} = {$body}"); // Eliminar
                        }
                    }
                }

                $request->request->add($parsedData);

                // No es necesario manipular $_FILES directamente aquí ya que UploadedFile se construye con path.
                // Log::info('Middleware: $_FILES después de la manipulación: ' . var_export($_FILES, true)); // Eliminar

                $request->files = new FileBag($uploadedFiles);
                // Log::info('Middleware: Request files collection re-initialized with new FileBag from UploadedFile instances.'); // Eliminar

                if ($request->has('_method')) {
                    $request->setMethod(strtoupper($request->input('_method')));
                }
            }

            // Log::info('Middleware: Request original después de la manipulación (parseo manual): ' . var_export($request->all(), true)); // Eliminar
            // Log::info('Middleware: Archivos en Request original (parseo manual): ' . var_export($request->files->all(), true)); // Eliminar

        } else {
            // Log::info('Middleware: Condición de procesamiento manual NO CUMPLIDA. Request no es PATCH/POST con _method y multipart/form-data.'); // Eliminar
        }
        // Log::info('--- Fin ForceFormDataParse middleware ---'); // Eliminar

        return $next($request);
    }
}
