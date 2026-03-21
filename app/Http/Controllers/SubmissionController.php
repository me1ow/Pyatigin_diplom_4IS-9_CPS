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
        $submissions = Auth::user()->submissions()->with('module.course')->latest()->get();
        return view('submissions.index', compact('submissions'));
    }
}