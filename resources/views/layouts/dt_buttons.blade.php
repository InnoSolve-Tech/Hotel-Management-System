@php
    $module = \App\Support\PermissionRegistry::resolveModuleFromPath(request()->path());
    $canView = $module ? auth()->user()->hasPermission(\App\Support\PermissionRegistry::permissionName($module, 'view')) : true;
    $canEdit = $module ? auth()->user()->hasPermission(\App\Support\PermissionRegistry::permissionName($module, 'edit')) : false;
    $canDelete = $module ? auth()->user()->hasPermission(\App\Support\PermissionRegistry::permissionName($module, 'delete')) : false;
@endphp
<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if($canView)
    <a class="dropdown-item" href="#" id="ViewBtn" data-id="{{ $id }}"><i class="fa fa-eye"></i> View</a>
    @endif

    @if($canEdit)
    <a class="dropdown-item" href="#" id="EditBtn" data-id="{{ $id }}"><i class="fa fa-pencil"></i> Edit</a>
    @endif

    @if($canDelete)
    <a class="dropdown-item" href="#" id="DeleteBtn" data-id="{{ $id }}"><i class="fa fa-trash"></i> Delete</a>
    @endif
  </div>
</div>

