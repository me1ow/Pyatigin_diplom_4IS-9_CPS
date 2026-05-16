<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    /**
     * Список документов, сгруппированных по разделам.
     */
    public function index()
    {
        $sections = [
            'bank'        => 'Банк заданий',
            'regulations' => 'Положения',
            'schedules'   => 'Расписания',
        ];

        $documents = Document::orderBy('title')->get()->groupBy('section');

        return view('documentation.index', compact('sections', 'documents'));
    }

    /**
     * Скачивание документа.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Файл не найден.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->filename,
            ['Content-Type' => $document->mime_type]
        );
    }

    /**
     * Просмотр PDF-документа в браузере (inline).
     */
    public function view(Document $document)
    {
        if ($document->mime_type !== 'application/pdf') {
            abort(400, 'Inline-просмотр доступен только для PDF-файлов.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Файл не найден.');
        }

        return response()->file(
            Storage::disk('public')->path($document->file_path),
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $document->filename . '"',
            ]
        );
    }
}
