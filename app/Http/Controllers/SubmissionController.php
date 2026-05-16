<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function store(Request $request, Module $module)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip,mp4,mov,avi|max:204800', // 200 MB
            'comment' => 'nullable|string',
        ]);

        // Сохраняем файл
        $path = $request->file('file')->store('submissions', 'public');
        $fileUrl = Storage::url($path);

        Submission::create([
            'user_id' => Auth::id(),
            'module_id' => $module->id,
            'file_url' => $fileUrl,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Задание отправлено на проверку.');
    }

    public function index()
    {
        $submissions = Auth::user()->submissions()->with('module.competence')->latest()->get();
        return view('submissions.index', compact('submissions'));
    }

    /**
     * Скачивание файла задания через контроллер (не зависит от symlink).
     */
    public function download(Submission $submission)
    {
        // Извлекаем относительный путь из file_url (например, "/storage/submissions/xxx.zip" → "submissions/xxx.zip")
        $relativePath = preg_replace('#^/storage/#', '', $submission->file_url);

        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404, 'Файл не найден.');
        }

        return Storage::disk('public')->download($relativePath);
    }
}