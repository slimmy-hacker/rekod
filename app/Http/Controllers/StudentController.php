<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function portal() {
        return view('student.portal');
    }
    public function showAttachmentForm()
    {
        return view('student.attachment-form');
    }

    public function storeAttachmentForm(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'student_phone' => 'required|string|max:20',
            'student_email' => 'required|email',

            'organization' => 'required|string|max:255',
            'date_commenced' => 'required|date',
            'date_finished' => 'required|date|after_or_equal:date_commenced',
            'town' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'building' => 'required|string|max:100',
            'supervisor_name' => 'required|string|max:255',
            'supervisor_phone' => 'required|string|max:20',
            'supervisor_email' => 'required|email',
        ]);


        return redirect()->back()->with('success', 'Attachment form submitted successfully!');
    }

    public function reports()
{
    return view('student.reports');
}
  public function logbook()
   {

    return view ('student.logbook');
   }

    // Returns the full counties array (code => name)
    private function getCounties(): array
    {
        return [
            '001' => 'Mombasa',
            '002' => 'Kwale',
            '003' => 'Kilifi',
            '004' => 'Tana River',
            '005' => 'Lamu',
            '006' => 'Taita-Taveta',
            '007' => 'Garissa',
            '008' => 'Wajir',
            '009' => 'Mandera',
            '010' => 'Marsabit',
            '011' => 'Isiolo',
            '012' => 'Meru',
            '013' => 'Tharaka-Nithi',
            '014' => 'Embu',
            '015' => 'Kitui',
            '016' => 'Machakos',
            '017' => 'Makueni',
            '018' => 'Nyandarua',
            '019' => 'Nyeri',
            '020' => 'Kirinyaga',
            '021' => "Murang'a",
            '022' => 'Kiambu',
            '023' => 'Turkana',
            '024' => 'West Pokot',
            '025' => 'Samburu',
            '026' => 'Trans Nzoia',
            '027' => 'Uasin Gishu',
            '028' => 'Elgeyo-Marakwet',
            '029' => 'Nandi',
            '030' => 'Baringo',
            '031' => 'Laikipia',
            '032' => 'Nakuru',
            '033' => 'Narok',
            '034' => 'Kajiado',
            '035' => 'Kericho',
            '036' => 'Bomet',
            '037' => 'Kakamega',
            '038' => 'Vihiga',
            '039' => 'Bungoma',
            '040' => 'Busia',
            '041' => 'Siaya',
            '042' => 'Kisumu',
            '043' => 'Homa Bay',
            '044' => 'Migori',
            '045' => 'Kisii',
            '046' => 'Nyamira',
            '047' => 'Nairobi',
        ];
    }

    public function companies()
    {
        $counties = $this->getCounties();
        return view('student.companies', compact('counties'));
    }

    public function storeCompany(Request $request)
    {
        $validCountyCodes = array_keys($this->getCounties());

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'shortform' => 'required|string|max:50',
            'branch'    => 'required|string|max:255',
            'address'   => 'required|string|max:255',
            'contact'   => 'required|string|max:50',
            'county'    => ['required', Rule::in($validCountyCodes)],
        ]);

        // Company::create($validated); // if you have a model

        return redirect()->route('student.companies')
            ->with('success', 'Company submitted successfully!');
    }

    // ...
}

 