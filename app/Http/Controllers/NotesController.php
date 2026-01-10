<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesController extends Controller
{
    function notesList() {
        $notes = DB::table('notes')->get();
        dd($notes);
    }

    function createNotes(Request $request) {

        $validation = $request->validate([
            'title' => 'required|',
            'password' => 'required',
        ]);
        try {
            DB::table('notes')->insert([
                'title' => $request->title,
                'notes' => $request->notes,
                'user_id' => 2
            ]);

            return redirect()->intended('/dashboard');
        } catch (\Throwable $th) {
            DD('SOMETHING WENT WRONG'. $th);
        }
    }
}
