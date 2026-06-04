@extends('layouts.app')
@section('content')
<div class="container-fluid py-5 ">
    <div class="row">
        <div class="col-md-12 m-auto">
            @if (Session::get('Success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    {{Session::get('Success')}}
                </div>
            @endif
            @if (Session::get('Destroy'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icone fas fa-exclamation-triangle"></i> Deleted !</h5>
                    {{Session::get('Destroy')}}
                </div>
            @endif
            @if (Session::get('DestroyAll'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icone fas fa-exclamation-triangle"></i> Deleted !</h5>
                    {{Session::get('DestroyAll')}}
                </div>
            @endif
            
            <div class="card">
                <div class="card-header bg-defult">
                    <div class="card-title">
                        <h2 class="card-title">
                            User List
                        </h2>
                    </div>
                    <a href="{{ route('user.create') }}" class="btn btn-sm bg-navy float-right text-capitalize ml-2" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Add User">
                        <i class="fa-solid fa-circle-plus mr-2"></i>
                        Add User
                    </a>
                    <a class="btn btn-sm bg-navy float-right text-capitalize" href="/room/trash"><i class="fa-solid fa-recycle mr-2"></i>View Trash</a>
                    
                    <button class="btn btn-sm bg-maroon float-right text-capitalize mr-3" id="DeleteAllBtn">
                        <i class="fa-solid fa-trash-can mr-2"></i>
                        Delete All
                    </button>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-borderless" id="UserList">
                        <thead>
                            <tr class="border-bottom">
                                <th>Employee</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Photo</th>
                                <th>Status</th>
                                <th>LastLogin</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>

                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                </div>
            </div>
            <div class="modal fade show" id="AssignRoleModal" role="dialog">
                <div class="modal-dialog modal-xl ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Assign Role</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {{ Form::open(array('url' => '/user/assign/role','method' => 'POST','class'=>'form-horizontal', 'files' => true, 'id' => 'AssignRoleForm')) }}
                                <input type="hidden" id="AssignRoleUserID" name="UserID">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="Type" class="form-label col-md-3">Roles</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <select name="role_id" class="form-select">
                                                    <option value="">Select Role</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <input type="submit" name="submit" value="Assign Role" id="submitBtn" class="btn bg-navy float-right w-25 text-capitalize">
                                        <button type="button" id="formResetBtn" class="btn btn-warning ">Reset</button>
                                    </div>
                                </div>
                            {{ Form::close()}}   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/custom-js/user.js') }}"></script>
@endsection
