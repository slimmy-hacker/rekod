<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OpportunityController extends Controller
{
    // Display all opportunities (for students)
  


    public function index()
    {
        $user = Auth::user();

        // Determine if user is a company or student by role or portal session
        $isCompany = $user->role === 'company'; // adjust based on your auth logic

        if ($isCompany) {
            // Company: show only their own opportunities
            $opportunities = Opportunity::where('company_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Student: show all non-expired opportunities from all companies
            $opportunities = Opportunity::where('expiry_date', '>=', now())
                ->with('company')
                ->orderBy('expiry_date', 'asc')
                ->get();
        }

        return view('opportunities.index', compact('opportunities', 'isCompany'));
    }



    // Display company’s own opportunities
    public function myOpportunities()
    {
        $opportunities = Opportunity::where('company_id', Auth::id())->latest()->get();
        return view('opportunities.my-opportunities', compact('opportunities'));
    }

    // Show create form
    public function create()
    {
        return view('opportunities.create');
    }

    // Store new opportunity
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'expiry_days' => 'required|integer|min:1|max:90', // how long it should stay active
        ]);

        Opportunity::create([
            'company_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'expiry_date' => Carbon::now()->addDays((int) $request->expiry_days),
        ]);

        return redirect()->route('opportunities.my')->with('success', 'Opportunity posted successfully!');
    }

    // Delete an opportunity
    public function destroy(Opportunity $opportunity)
    {
        if ($opportunity->company_id !== Auth::id()) {
            abort(403);
        }

        $opportunity->delete();
        return back()->with('success', 'Opportunity deleted successfully.');
    }
}
