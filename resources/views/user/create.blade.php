@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-11 m-auto">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Please check the form</h5>
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="card-title text-navy">
                            <a href="{{ route('user.index') }}" class="mr-3">
                                <i class="fa-solid fa-circle-arrow-left fs-5 text-navy" title="Back to Users"></i>
                            </a>
                            Add a new user
                        </h2>
                    </div>

                    {{ Form::open(['url' => route('user.store'), 'method' => 'POST', 'class' => 'form-horizontal', 'files' => true]) }}
                        <div class="card-body pb-0">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="EmployeeID" class="form-label col-md-3">Employee:</label>
                                        <div class="col-md-8">
                                            <select name="EmployeeID" id="EmployeeID" class="form-select">
                                                <option value="">No employee linked</option>
                                                @foreach ($Employees as $Employee)
                                                    <option value="{{ $Employee->id }}" @selected(old('EmployeeID') == $Employee->id)>
                                                        {{ $Employee->Name ?? 'Employee #'.$Employee->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="name" class="form-label col-md-3">Name:</label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="email" class="form-label col-md-3">Email:</label>
                                        <div class="col-md-8">
                                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="Role" class="form-label col-md-3">Role:</label>
                                        <div class="col-md-8">
                                            <select name="Role" id="Role" class="form-select">
                                                <option value="Staff" @selected(old('Role', 'Staff') === 'Staff')>Staff</option>
                                                <option value="Cashier" @selected(old('Role') === 'Cashier')>Cashier</option>
                                                <option value="Manager" @selected(old('Role') === 'Manager')>Manager</option>
                                                <option value="Admin" @selected(old('Role') === 'Admin')>Admin</option>
                                                <option value="SuperAdmin" @selected(old('Role') === 'SuperAdmin')>Super Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="password" class="form-label col-md-3">Password:</label>
                                        <div class="col-md-8">
                                            <input type="password" name="password" id="password" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="password_confirmation" class="form-label col-md-3">Confirm:</label>
                                        <div class="col-md-8">
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="Status" class="form-label col-md-3">Status:</label>
                                        <div class="col-md-8">
                                            <select name="Status" id="Status" class="form-select">
                                                <option value="1" @selected(old('Status', '1') === '1')>Active</option>
                                                <option value="0" @selected(old('Status') === '0')>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="Photo" class="form-label col-md-3">Photo:</label>
                                        <div class="col-md-8">
                                            <input type="file" name="Photo" id="Photo" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('user.index') }}" class="btn btn-light text-capitalize">Cancel</a>
                            <input type="submit" name="submit" class="btn bg-navy float-right w-25 text-capitalize" value="Add User">
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
