<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }
}