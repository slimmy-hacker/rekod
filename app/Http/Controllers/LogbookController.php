<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    /**
     * Show the user's logbook entries.
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Fetch all logbook entries for this user from the database.
        // We order them by creation date so the newest entries are at the top.
        $logbooks = Logbook::where('registration_number', $student->registration_number)
                    ->orderBy('created_at', 'desc')
                    ->get();


        // Pass the fetched data to the logbook view.
        // The `compact('logbooks')` is a shortcut for ['logbooks' => $logbooks].
        return view('logbooks.index', compact('logbooks'));
    }

    /**
     * Store a new logbook entry.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'week' => 'required|string|max:255',
            'activities' => 'required|string',
        ]);

        // Create a new Logbook entry
        Logbook::create([
            'user_id' => Auth::id(), // Associate the entry with the logged-in user
            'week' => $request->week,
            'activities' => $request->activities,
        ]);

        // Redirect back to the logbook page with a success message
        return redirect()->route('logbook.index')->with('success', 'Logbook entry added successfully!');
    }
}
