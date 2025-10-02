<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\Opportunity;


use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function portal()
    {
        return view('company.portal');
    }



    public function students()
    {
        // Get logged-in company
        $companyId = Auth::id();  // if companies log in through users table

        // Get students linked to this company through placements
        $students = Student::whereHas('placements', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->get();

        return view('company.students', compact('students'));
    }



public function opportunities()
{
    // fetch data (use get() or paginate())
    $opportunities = Opportunity::orderBy('created_at', 'desc')->get();

    // return view and pass the variable
    return view('company.opportunities', compact('opportunities'));
    // OR: return view('opportunities', compact('opportunities')); depending on your view path
}
// Show form to create opportunity
public function createOpportunity()
{
    return view('company.create-opportunity');
}
public function storeOpportunity(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location' => 'nullable|string',
        'deadline' => 'nullable|date',
    ]);

    Opportunity::create([
        'industry_id' => auth()->id(), // logged in company/industry
        'title' => $request->title,
        'description' => $request->description,
        'location' => $request->location,
        'deadline' => $request->deadline,
    ]);

    return back()->with('success', 'Opportunity created successfully.');
}

    // 📌 Documents
    public function documents()
    {
        return view('company.documents');
    }

    // 📌 Reports
    public function reports()
    {
        return view('company.reports');
    }
    public function index()
{
    $opportunities = Opportunity::where('industry_id', auth()->id())->get();

    return view('company.opportunities', compact('opportunities'));
}
public function destroy($id)
{
    $opportunity = Opportunity::findOrFail($id);
    $opportunity->delete();

    return redirect()->route('company.opportunities')
                     ->with('success', 'Opportunity deleted successfully.');
}


public function applications(Opportunity $opportunity)
{
    if ($opportunity->industry_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    $applications = $opportunity->applications;
    return view('company.opportunities.applications', compact('opportunity', 'applications'));
}



}
