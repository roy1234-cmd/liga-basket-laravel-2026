<?php

namespace App\Services;

use RuntimeException;

/** Guarda la información sin base de datos y evita escrituras simultáneas. */
class LigaStore
{
    private function path(): string
    {
        $dir = storage_path('app/private');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('No se pudo crear la carpeta de datos.');
        }
        return $dir.'/liga.json';
    }

    public function read(): array { return $this->transaction(null); }
    public function update(callable $callback): array { return $this->transaction($callback); }

    private function transaction(?callable $callback): array
    {
        $handle = fopen($this->path(), 'c+');
        if (!$handle) throw new RuntimeException('No se pudo abrir el archivo de datos.');
        try {
            if (!flock($handle, $callback ? LOCK_EX : LOCK_SH)) throw new RuntimeException('No se pudo bloquear el archivo.');
            rewind($handle);
            $raw = stream_get_contents($handle);
            $state = $raw === '' ? ['equipos' => [], 'jugadores' => [], 'partidos' => []] : json_decode($raw, true);
            if (!is_array($state) || !isset($state['equipos'], $state['jugadores'], $state['partidos'])
                || !is_array($state['equipos']) || !is_array($state['jugadores']) || !is_array($state['partidos'])) {
                throw new RuntimeException('Archivo JSON inválido; recupera una copia de seguridad.');
            }
            if ($callback) {
                $callback($state);
                $json = json_encode($state, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                rewind($handle);
                if (!ftruncate($handle, 0) || fwrite($handle, $json) !== strlen($json)) {
                    throw new RuntimeException('No se pudo guardar el archivo JSON.');
                }
                fflush($handle);
            }
            return $state;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
