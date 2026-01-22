<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // [ADDED] Needed to identify the user

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
        // [FIX] Only fetch notes belonging to the logged-in user
        // We check 'user_id' matches Auth::id()
        $this->notesList = DB::table('notes')
            ->where('user_id', Auth::id()) 
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function viewNotes($id)
    {
        try {
            // [FIX] Ensure the user owns this note before viewing
            $note = DB::table('notes')
                ->where('id', $id)
                ->where('user_id', Auth::id()) 
                ->first();

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
            // [FIX] Use dynamic Auth ID instead of hardcoded '2'
            'user_id' => Auth::id(), 
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->afterSave('Note created');
    }

    public function update()
    {
        $this->validate();

        if ($this->noteId) {
            try {
                // [FIX] Ensure user owns the note before updating
                DB::table('notes')
                    ->where('id', $this->noteId)
                    ->where('user_id', Auth::id()) 
                    ->update([
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
        // [FIX] Ensure user owns the note before deleting
        DB::table('notes')
            ->where('id', $id)
            ->where('user_id', Auth::id()) 
            ->delete();

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

    public function openNotes()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function cancelEdit() {
        if ($this->noteId) {
            $this->isEditing = false;
        } else {
            $this->showModal = false;
            $this->resetForm();
        }
    }

    public function closeNotes()
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