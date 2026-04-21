<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;

#[Layout('layouts.app')]
class ModerationPanel extends Component
{
    public string $activeTab = 'overview';

    public function render()
    {
        return view('livewire.admin.moderation-panel')
            ->title('Admin — Moderation');
    }
}
