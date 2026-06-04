<?php

namespace App\Livewire\Tips;

use Livewire\Component;
use App\Models\Tip;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class CreateTip extends Component
{
    #[Validate('required|min:5|max:100')]
    public string $title = '';

    #[Validate('required')]
    public string $category = 'eloquent';

    #[Validate('required')]
    public string $difficulty = 'easy';

    #[Validate('required|min:20')]
    public string $why = '';

    #[Validate('required|min:5')]
    public string $junior_label = '';

    #[Validate('required|min:10')]
    public string $junior_code = '';

    #[Validate('required|min:5')]
    public string $senior_label = '';

    #[Validate('required|min:10')]
    public string $senior_code = '';

    public function save()
    {
        $this->validate();

        Tip::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'why' => $this->why,
            'junior_label' => $this->junior_label,
            'junior_code' => $this->junior_code,
            'senior_label' => $this->senior_label,
            'senior_code' => $this->senior_code,
            'is_approved' => Auth::user()->reputation >= 500, // Auto-approve for experts
        ]);

        session()->flash('success', 'Ton tip a été soumis avec succès ! ' . (Auth::user()->reputation < 500 ? 'Il sera visible après modération.' : ''));

        return redirect()->to(route('tips.index'));
    }

    public function render()
    {
        return view('livewire.tips.create-tip')
            ->layout('layouts.app', ['title' => 'Proposer un Tip — Knowravel']);
    }
}
