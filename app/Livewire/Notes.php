<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Notes extends Component
{
    public $notesList = [];

    public $showModal = false;
    public $isEditing = false;
    public $noteId = null;
    public $title = '';
    public $content = '';

    protected $rules = [
        'title' => 'required|string',
        'content' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadNotes();
    }

    protected function loadNotes()
    {
        $this->notesList = DB::table('notes')->orderBy('created_at', 'desc')->get();
    }

    public function viewNotes($id)
    {
        try {
            $note = DB::table('notes')->where('id', $id)->first();
            if (! $note) {
                return;
            }
            $this->noteId = $note->id;
            $this->title = $note->title;
            $this->content = $note->notes;
            $this->isEditing = false;
            $this->showModal = true;
        } catch (\Throwable $th) {
            // handle/log if needed
        }
    }

    public function edit()
    {
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        DB::table('notes')->insert([
            'title' => $this->title,
            'notes' => $this->content,
            'user_id' => 2
        ]);

        $this->afterSave('Note created');
    }

    public function update()
    {
        $this->validate();

        if ($this->noteId) {
            try {
                DB::table('notes')->where('id', $this->noteId)->update([
                    'title' => $this->title,
                    'notes' => $this->content,
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $th) {
                // handle/log if needed
            }
        }

        $this->afterSave('Note updated');
    }

    public function delete($id)
    {
        DB::table('notes')->where('id', $id)->delete();
        $this->afterSave('Note deleted');
    }

    protected function afterSave($message = null)
    {
        $this->showModal = false;
        $this->resetForm();
        $this->loadNotes();

        if ($message) {
        $this->dispatch('browser:notes:flash', ['message' => $message]);
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->noteId = null;
        $this->title = '';
        $this->content = '';
        $this->isEditing = false;
    }

    public function render()
    {
        return view('livewire.notes', ['notes' => $this->notesList]);
    }
}
