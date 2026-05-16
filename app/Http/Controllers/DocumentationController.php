<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        $canManage = Auth::user()->isAdmin() || Auth::user()->isExpert();

        return view('documentation.index', compact('sections', 'documents', 'canManage'));
    }

    /**
     * Скачивание документа.
     * Используется response()->download() с реальным путём через Storage::path(),
     * чтобы избежать проблем с контрактом Filesystem::download().
     */
    public function download(Document $document)
    {
        $filePath = Storage::disk('public')->path($document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Файл не найден.');
        }

        return response()->download(
            $filePath,
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

        $filePath = Storage::disk('public')->path($document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Файл не найден.');
        }

        return response()->file(
            $filePath,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $document->filename . '"',
            ]
        );
    }

    /**
     * Загрузка нового документа (admin, expert).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'section' => ['required', Rule::in(['bank', 'regulations', 'schedules'])],
            'file'    => 'required|file|mimes:pdf,docx,xlsx|max:51200',
        ], [
            'title.required'   => 'Укажите название документа.',
            'section.required' => 'Выберите раздел.',
            'section.in'       => 'Недопустимый раздел.',
            'file.required'    => 'Выберите файл для загрузки.',
            'file.mimes'       => 'Поддерживаются только файлы PDF, DOCX, XLSX.',
            'file.max'         => 'Максимальный размер файла — 50 МБ.',
        ]);

        $uploadedFile = $request->file('file');

        // Сохраняем в storage/app/public/documents/
        $storedPath = $uploadedFile->store('documents', 'public');

        Document::create([
            'title'     => $validated['title'],
            'section'   => $validated['section'],
            'filename'  => $uploadedFile->getClientOriginalName(),
            'file_path' => $storedPath,
            'mime_type' => $uploadedFile->getMimeType(),
            'file_size' => $uploadedFile->getSize(),
        ]);

        return redirect()
            ->route('documentation.index')
            ->with('success', 'Документ «' . $validated['title'] . '» успешно загружен.');
    }

    /**
     * Удаление документа (admin, expert).
     */
    public function destroy(Request $request, Document $document)
    {
        // Удаляем физический файл
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $title = $document->title;
        $document->delete();

        return redirect()
            ->route('documentation.index')
            ->with('success', 'Документ «' . $title . '» удалён.');
    }
}
