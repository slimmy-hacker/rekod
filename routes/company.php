<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

use App\Http\Controllers\OpportunityController;

Route::name('company.')->group(function () {
    Route::get('/portal', [CompanyController::class, 'portal'])->name('portal');
    Route::get('/students', [CompanyController::class, 'students'])->name('students');    
    Route::get('/documents', [CompanyController::class, 'documents'])->name('documents');




    Route::get('/reports', [CompanyController::class, 'reports'])->name('reports');

    
    
});
    


Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
    Route::get('/opportunities/create', [OpportunityController::class, 'create'])->name('opportunities.create');
    Route::post('/opportunities', [OpportunityController::class, 'store'])->name('opportunities.store');
    Route::get('/my-opportunities', [OpportunityController::class, 'myOpportunities'])->name('opportunities.my');
    Route::delete('/opportunities/{opportunity}', [OpportunityController::class, 'destroy'])->name('opportunities.destroy');