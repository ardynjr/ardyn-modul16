<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
//use App\Http\Controllers\Str;
use Illuminate\Support\Str;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index()
{


    $pageTitle = 'Employee List';

    // ELOQUENT
    $employees = Employee::all();
   // dd($employees);

    return view('employee.index', [
        'pageTitle' => $pageTitle,
        'employees' => $employees
    ]);
}




    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $pageTitle = 'Create Employee';

    // ELOQUENT
    $positions = Position::all();

    return view('employee.create', compact('pageTitle', 'positions'));
}

public function store(Request $request)
{
    $messages = [
        'required' => ':Attribute harus diisi.',
        'email' => 'Isi :attribute dengan format yang benar',
        'numeric' => 'Isi :attribute dengan angka'
    ];

    $validator = Validator::make($request->all(), [
        'firstName' => 'required',
        'lastName' => 'required',
        'email' => 'required|email',
        'age' => 'required|numeric',
    ], $messages);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();

    }




    // Get File
    $file = $request->file('cv');

    if ($file != null) {
        $originalFilename = $file->getClientOriginalName();
        $encryptedFilename = $file->hashName();

        // Store File
        $file->store('public/files');
    }





    // ELOQUENT
    $employee = New Employee;
    $employee->firstname = $request->firstName;
    $employee->lastname = $request->lastName;
    $employee->email = $request->email;
    $employee->age = $request->age;
    $employee->position_id = $request->position;

    if ($file != null) {
        $employee->original_filename = $originalFilename;
        $employee->encrypted_filename = $encryptedFilename;
    }


    $employee->save();

    return redirect()->route('employees.index');
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
{


    $pageTitle = 'Employee Detail';

    // ELOQUENT
    $employee = Employee::find($id);

    return view('employee.show', compact('pageTitle', 'employee'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
{
    $pageTitle = 'Edit Employee';

    // ELOQUENT
    $positions = Position::all();
    $employee = Employee::find($id);

    return view('employee.edit', compact('pageTitle', 'positions', 'employee'));
}

public function update(Request $request, string $id)
{
    $messages = [
        'required' => ':Attribute harus diisi.',
        'email' => 'Isi :attribute dengan format yang benar',
        'numeric' => 'Isi :attribute dengan angka',
    ];

    $validator = Validator::make($request->all(), [
        'firstName' => 'required',
        'lastName' => 'required',
        'email' => 'required|email',
        'age' => 'required|numeric',
        'cv' => 'nullable|file|mimes:pdf|max:2048', // Validasi file CV
    ], $messages);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Temukan employee berdasarkan ID
    $employee = Employee::find($id);
    $employee->firstname = $request->firstName;
    $employee->lastname = $request->lastName;
    $employee->email = $request->email;
    $employee->age = $request->age;
    $employee->position_id = $request->position;

    // Simpan file CV sebagai binary data di kolom 'cv'
    if ($request->hasFile('cv')) {
        $file = $request->file('cv');
        $binaryData = file_get_contents($file->getRealPath()); // Ambil konten file
        $employee->cv = $binaryData; // Simpan data binary di database
    }

    $employee->save();

    return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
}



public function destroy(string $id)
{
    // ELOQUENT
    Employee::find($id)->delete();

    return redirect()->route('employees.index');
}


public function downloadFile($employeeId)
{
    $employee = Employee::find($employeeId);
    $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
    $downloadFilename = str::lower($employee->firstname.'_'.$employee->lastname.'_cv.pdf');

    if(Storage::exists($encryptedFilename)) {
        return Storage::download($encryptedFilename, $downloadFilename);
    }
}



public function downloadCv(string $id)
{
    $employee = Employee::find($id);

    if ($employee && $employee->cv) {
        $cvContent = $employee->cv; // Ambil data binary
        $filename = Str::slug($employee->firstname . '_' . $employee->lastname) . '_cv.pdf';

        return response($cvContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    return redirect()->route('employees.index')->with('error', 'CV not found.');
}



}
