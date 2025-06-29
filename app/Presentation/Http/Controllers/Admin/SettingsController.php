<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Presentation\Http\Controllers\Controller;
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

    public function manageUsers(): View
    {
        return view('admin.settings.general.manage-users');
    }

    public function manageGlobalSettings(): View
    {
        return view('admin.settings.general.manage-global');
    }
}
