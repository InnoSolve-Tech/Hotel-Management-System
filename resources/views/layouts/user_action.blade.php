@php
    $canEditUser = auth()->user()->hasPermission('user.edit');
    $canDeleteUser = auth()->user()->hasPermission('user.delete');
@endphp
@if($canEditUser || $canDeleteUser)
<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if($canEditUser)
    <a class="dropdown-item" href="#" id="EditBtn" data-id="{{ $id }}"><i class="fa fa-pencil"></i> Edit</a>
    <a class="dropdown-item" href="#" id="AssignRoleBtn" data-id="{{ $id }}"><i class="fa fa-key"></i> Assign Role</a>
    @endif

    @if($canDeleteUser)
    <a class="dropdown-item" href="#" id="DeleteBtn" data-id="{{ $id }}"><i class="fa fa-trash"></i> Delete</a>
    @endif

  </div>
</div>
@else
<span class="text-muted small">No actions</span>
@endif
