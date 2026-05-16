<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title',
        'section',
        'filename',
        'file_path',
        'mime_type',
        'file_size',
    ];

    /**
     * Возвращает человекочитаемый размер файла.
     */
    public function humanSize(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' МБ';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' КБ';
        }

        return $bytes . ' Б';
    }

    /**
     * Является ли документ PDF-файлом.
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Возвращает расширение файла в нижнем регистре.
     */
    public function extension(): string
    {
        return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    /**
     * Возвращает название раздела на русском.
     */
    public function sectionLabel(): string
    {
        return match ($this->section) {
            'bank'         => 'Банк заданий',
            'regulations'  => 'Положения',
            'schedules'    => 'Расписания',
            default        => $this->section,
        };
    }
}
