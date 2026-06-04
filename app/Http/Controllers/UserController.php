<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $Users = User::select('users.*','users.EmployeeID as Employee')
        // ->get();
        if (request()->ajax() || request('grid') == 'ag') {
            return Datatables::of(User::with('role')->get())->addColumn('action','layouts.user_action')->make(true);
        }
        return view('user.index', [
            'roles' => Role::query()->orderByDesc('is_system')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Employees = Employee::all();
        return view('user.create', [
            'Employees' => $Employees,
            'roles' => Role::query()->orderByDesc('is_system')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'EmployeeID' => ['nullable', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'Status' => ['nullable', 'boolean'],
            'Photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('Photo')) {
            $file = $request->file('Photo');
            $fileName = uniqid('user_', true) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
            $validated['Photo'] = $fileName;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['Status'] = $request->boolean('Status', true);
        $validated['role_id'] = $validated['role_id'] ?? Role::query()->firstWhere('name', 'Staff')?->id;

        User::create($validated);

        return redirect()->route('user.index')->with('Success', 'User added successfully!');
    }

    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'UserID' => ['required', 'exists:users,id'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        return User::query()
            ->findOrFail($validated['UserID'])
            ->update(['role_id' => $validated['role_id']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('user.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return back();
    }
   /**
     * Delete all table list
    */
    public function destroyAll()
    {
        User::withTrashed()->delete();
        return back();
    }
}
