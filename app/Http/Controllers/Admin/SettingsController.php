<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index');
    }

    public function manageCourses(): View
    {
        return view('admin.settings.general.manage-courses');
    }

    public function manageSemesters(): View
    {
        return view('admin.settings.general.manage-semesters');
    }
}
