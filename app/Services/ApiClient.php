<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class ApiClient
{
    protected $baseUrl;

    public function __construct($baseUrl)
    {		
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function get($endpoint)
    {
		Log::info('>>>ApiClient.get...');
		
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true); // Incluye encabezados en la respuesta
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);  // Tiempo límite para evitar bloqueos

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        if ($response === false) {
            throw new \Exception('Error al realizar la solicitud cURL.');
        }

        // Separar encabezados y cuerpo
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        // Verificar el código HTTP
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \Exception("Error HTTP {$httpCode}: " . $this->parseHeaders($headers));
        }

        // Decodificar JSON
        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }

    protected function parseHeaders($headerString)
    {
        $headers = [];
        $lines = explode("\r\n", $headerString);
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }
        return $headers;
    }
}
