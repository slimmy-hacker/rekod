<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\Location;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Opportunity;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{



    public function companies(Request $request)
    {

        if ($request->ajax()) {
            $data = Company::with(['county', 'town'])->orderBy('name', 'ASC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('county', fn($row) => $row->county->name ?? '-')
                ->editColumn('town', fn($row) => $row->town->name ?? '-')
                
                ->addColumn('action', function ($row) {
                    return '
                        
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }


        $counties = Location::where('level','1')
                                ->whereNotNull('latitude')
                                ->whereNotNull('longitude')
                             ->select('id','name','code')
                            ->get();
        $sub_counties = Location::where('level','2')
                                ->whereNotNull('latitude')
                                ->whereNotNull('longitude')
                                ->select('id','name','code','parent_code')
                                ->get();
      $towns = Location::where('level', '3')
                ->select('id', 'name', 'code', 'parent_code')
                ->orderBy('name', 'ASC')
                ->get();
        return view('companies', compact('counties',  'sub_counties','towns'));
    }


    public function storeCompany(Request $request)
    {


        $validated = $request->validate([
            'name'    => 'required|string|max:255|unique:companies,name',
            'email'   => 'required|string|max:255|unique:users,email',
            'alias'   => 'required|string|max:50|unique:companies,alias',
            'contact' => 'required|string|max:50|unique:companies,contact',
            'parent_company'    => 'nullable|string|max:255',
            'address'   => 'required|string|max:255',
            'county_id'    => 'required|exists:locations,id',
            'town_id'    => 'required|exists:locations,id',
            'latitude'    => 'nullable|string|max:255',
            'longitude'   => 'nullable|string',
            'street'    => 'nullable|string|max:255',
            'building'   => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {
            $user = User::updateOrCreate(
                ['email' =>Str::lower($validated['email'])],
                [
                    'name' => $validated['name'],
                    'phone_number' => $validated['contact'],
                    'password' => Hash::make(config('app.default_password')),
                    'role' => 'company',
                ]
            );
            $validated['user_id'] = $user->id;
            Company::create($validated); 
            DB::Commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Company Created successfully',
            ]);

        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Company Creation Failed',
                'error' => $e->getMessage(),
            ]);
        }

    }


    public function portal()
    {
        return view('company.portal');
    }



    public function students()
    {
        
        $companyId = Auth::id();  

        
        $students = Student::whereHas('placements', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->get();

        return view('company.students', compact('students'));
    }



public function opportunities()
{
    
    $opportunities = Opportunity::orderBy('created_at', 'desc')->get();

    
    return view('company.opportunities', compact('opportunities'));
   
}

public function createOpportunity()
{
    return view('company.create-opportunity');
}
public function storeOpportunity(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location' => 'required|string',
        'deadline' => 'required|date',
    ]);

    Opportunity::create([
        'industry_id' => auth()->id(), 
        'title' => $request->title,
        'description' => $request->description,
        'location' => $request->location,
        'deadline' => $request->deadline,
    ]);

   return response()->json([
        'status' => 'success',
        'message' => 'Opportunity created successfully.',
        'data' => $opportunity,
    ]);
}

  
    public function documents()
    {
        return view('company.documents');
    }

   
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

    return redirect()->route('opportunities.destroy')
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
