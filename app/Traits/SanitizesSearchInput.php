<?php

namespace App\Traits;

trait SanitizesSearchInput
{
    /**
     * Escapa caracteres especiales de LIKE para prevenir búsquedas maliciosas
     *
     * @param string|null $value
     * @return string
     */
    protected function sanitizeForLike(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // Escapar caracteres especiales de SQL LIKE
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            trim($value)
        );
    }

    /**
     * Prepara un valor para búsqueda LIKE con comodines
     *
     * @param string|null $value
     * @param string $mode 'contains' | 'starts' | 'ends'
     * @return string
     */
    protected function prepareLikeSearch(?string $value, string $mode = 'contains'): string
    {
        $sanitized = $this->sanitizeForLike($value);

        return match ($mode) {
            'starts' => $sanitized . '%',
            'ends' => '%' . $sanitized,
            default => '%' . $sanitized . '%', // contains
        };
    }
}
