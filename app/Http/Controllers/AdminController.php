<?php
namespace App\Http\Controllers;
use App\Models\Budget;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function portal() {
        return view('admin.portal');
    }

    public function students() {
        return view('students');
    }

    public function supervisors() {
        return view('supervisors');
    }

    public function industry() {
        return view('industry');
    }

    public function attachments() {
        return view('attachments');
    }

    public function budgets()
{
    $budgets = Budget::all();
    return view('admin.budgets', compact('budgets'));
}

    public function storeBudget(Request $request)
    {
        // validate + save budget data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        
        return redirect()->route('admin.budgets')
            ->with('success', 'Budget saved successfully!');
    }

public function showBudget($id)
{
    // Find the budget record
    $budget = Budget::findOrFail($id);

    // Parse student list file if exists
    $studentList = [];
    if ($budget->student_list_file && Storage::exists('public/' . $budget->student_list_file)) {
        $path = storage_path('app/public/' . $budget->student_list_file);
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $studentList[] = $row;
            }
            fclose($handle);
        }
    }

    // Pass to blade
    return view('admin.budget-details', compact('budget', 'studentList'));
}


    public function reports() {
        return view('admin.reports');
    }

    public function settings() {
        return view('admin.settings');
    }
}
