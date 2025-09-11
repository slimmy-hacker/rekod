<?php
namespace App\Http\Controllers;

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

    public function budgets() {
        return view('budgets');
    }

    public function reports() {
        return view('admin.reports');
    }

    public function settings() {
        return view('admin.settings');
    }
}
