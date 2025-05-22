<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use Illuminate\Contracts\View\View;

class SemesterManager extends Component
{
    public function render(): View
    {
        return view('livewire.admin.settings.semester-manager');
    }
}
