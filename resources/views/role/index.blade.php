@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 hotelio-role-page">
        <div class="row">
            <div class="col-lg-12">
                @if (Session::get('Success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <h5><i class="icon fas fa-check"></i> Success</h5>
                        {{ Session::get('Success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Please review the form</h5>
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="hotelio-page-hero card mb-4">
                    <div class="card-body">
                        <div class="hotelio-page-hero__content">
                            <div>
                                <span class="hotelio-dashboard__eyebrow">Administration</span>
                                <h2 class="card-title mb-2">Roles & Permissions</h2>
                                <p class="text-muted mb-0">
                                    Create roles faster, search permissions easily, and manage access without guessing.
                                </p>
                            </div>
                            <div class="hotelio-page-hero__meta">
                                <div class="hotelio-page-hero__stat">
                                    <strong>{{ $roles->count() }}</strong>
                                    <span>roles</span>
                                </div>
                                <div class="hotelio-page-hero__stat">
                                    <strong>{{ count($grantablePermissions) }}</strong>
                                    <span>permissions you can grant</span>
                                </div>
                                <div class="hotelio-page-hero__stat">
                                    <strong>{{ $roles->sum('users_count') }}</strong>
                                    <span>assigned users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 hotelio-role-card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3 class="card-title mb-1">Create a new role</h3>
                            <p class="text-muted small mb-0">Start from scratch or copy an existing role and fine-tune it.</p>
                        </div>
                        <span class="text-muted small">Only Admin and SuperAdmin can manage this page.</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('role.store') }}">
                            @csrf

                            <div class="hotelio-role-builder">
                                <div class="hotelio-role-builder__sidebar">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Role name</label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            class="form-control"
                                            value="{{ old('name') }}"
                                            placeholder="Night Auditor"
                                            required
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label for="role-template" class="form-label">Start from</label>
                                        <select
                                            id="role-template"
                                            class="form-control"
                                            data-role-template-select
                                            data-template-target="#create-role-editor"
                                        >
                                            <option value="">Start from scratch</option>
                                            @foreach ($roles as $templateRole)
                                                <option
                                                    value="{{ $templateRole->id }}"
                                                    data-template-permissions='@json($templateRole->permissions->pluck("name")->values())'
                                                >
                                                    {{ $templateRole->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-2">
                                            Selecting a template pre-fills the permission matrix below.
                                        </small>
                                    </div>

                                    <div class="hotelio-role-tip">
                                        <h4 class="mb-2">Helpful tip</h4>
                                        <p class="mb-0">
                                            Start with a template if the role is close to an existing one, then use search and quick actions to fine-tune only what differs.
                                        </p>
                                    </div>

                                    <div class="hotelio-role-builder__footer">
                                        <button type="submit" class="btn bg-navy">Create Role</button>
                                    </div>
                                </div>

                                <div class="hotelio-role-builder__editor">
                                    <label class="form-label">Permissions</label>
                                    @include('role.partials.permission-editor', [
                                        'editorId' => 'create-role-editor',
                                        'permissionGroups' => $permissionGroups,
                                        'selectedPermissions' => old('permissions', []),
                                        'grantablePermissions' => $grantablePermissions,
                                        'isLocked' => false,
                                    ])
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h3 class="card-title mb-1">Existing roles</h3>
                        <p class="text-muted small mb-0">Review built-in roles and update any custom access profiles.</p>
                    </div>
                </div>

                <div class="row">
                    @foreach ($roles as $role)
                        @php
                            $selectedPermissions = $role->permissions->pluck('name')->all();
                            $selectedCount = count($selectedPermissions);
                            $isLockedForAdmin = auth()->user()->hasRole('Admin') && $role->name === 'SuperAdmin';
                        @endphp

                        <div class="col-xl-6 mb-4">
                            <div class="card h-100 hotelio-role-card">
                                <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <h3 class="card-title mb-1">{{ $role->name }}</h3>
                                        <p class="text-muted small mb-0">
                                            {{ $role->users_count }} user(s)
                                            · {{ $selectedCount }} permission{{ $selectedCount === 1 ? '' : 's' }}
                                            @if ($role->is_system)
                                                <span class="hotelio-role-badge">System role</span>
                                            @else
                                                <span class="hotelio-role-badge hotelio-role-badge--muted">Custom role</span>
                                            @endif
                                        </p>
                                    </div>

                                    @if ($isLockedForAdmin)
                                        <span class="text-muted small">Locked for Admin</span>
                                    @endif
                                </div>

                                <div class="card-body">
                                    @if ($isLockedForAdmin)
                                        <div class="hotelio-role-tip hotelio-role-tip--warning mb-3">
                                            <h4 class="mb-2">Protected role</h4>
                                            <p class="mb-0">
                                                Admins can review the SuperAdmin permissions here, but only a SuperAdmin can change them.
                                            </p>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('role.update', $role) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label for="role-name-{{ $role->id }}" class="form-label">Role name</label>
                                            <input
                                                type="text"
                                                id="role-name-{{ $role->id }}"
                                                name="name"
                                                class="form-control"
                                                value="{{ old('name', $role->name) }}"
                                                @disabled($role->is_system || $isLockedForAdmin)
                                            >
                                            @if ($role->is_system && ! $isLockedForAdmin)
                                                <small class="text-muted d-block mt-2">System role names stay fixed in this pass.</small>
                                            @endif
                                        </div>

                                        @include('role.partials.permission-editor', [
                                            'editorId' => 'role-editor-' . $role->id,
                                            'permissionGroups' => $permissionGroups,
                                            'selectedPermissions' => $selectedPermissions,
                                            'grantablePermissions' => $grantablePermissions,
                                            'isLocked' => $isLockedForAdmin,
                                        ])

                                        @unless ($isLockedForAdmin)
                                            <div class="card-footer px-0 pb-0">
                                                <button type="submit" class="btn bg-navy">Save Changes</button>
                                            </div>
                                        @endunless
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-role-editor]').forEach((editor) => {
            const checkboxes = Array.from(editor.querySelectorAll('[data-permission-checkbox]'));
            const groups = Array.from(editor.querySelectorAll('[data-permission-group]'));
            const searchInput = editor.querySelector('[data-permission-search]');
            const selectedCount = editor.querySelector('[data-selected-count]');
            const emptyState = editor.querySelector('[data-permission-empty]');

            const refresh = () => {
                const totalSelected = checkboxes.filter((checkbox) => checkbox.checked).length;

                if (selectedCount) {
                    selectedCount.textContent = `${totalSelected} selected`;
                }

                checkboxes.forEach((checkbox) => {
                    checkbox.closest('.hotelio-permission-row')?.classList.toggle('is-selected', checkbox.checked);
                });

                groups.forEach((group) => {
                    const groupCheckboxes = Array.from(group.querySelectorAll('[data-permission-checkbox]'));
                    const groupSelected = groupCheckboxes.filter((checkbox) => checkbox.checked).length;
                    const groupCount = group.querySelector('[data-group-count]');

                    if (groupCount) {
                        groupCount.textContent = groupSelected;
                    }
                });
            };

            const setChecked = (boxes, checked) => {
                boxes.forEach((checkbox) => {
                    if (!checkbox.disabled) {
                        checkbox.checked = checked;
                    }
                });

                refresh();
            };

            const setViewOnly = (boxes) => {
                boxes.forEach((checkbox) => {
                    if (!checkbox.disabled) {
                        checkbox.checked = checkbox.dataset.permissionAction === 'view';
                    }
                });

                refresh();
            };

            const refreshSearchState = () => {
                const visibleGroups = groups.filter((group) => !group.hidden).length;

                if (emptyState) {
                    emptyState.classList.toggle('d-none', visibleGroups > 0);
                }
            };

            editor.querySelector('[data-select-view]')?.addEventListener('click', () => setViewOnly(checkboxes));
            editor.querySelector('[data-select-all]')?.addEventListener('click', () => setChecked(checkboxes, true));
            editor.querySelector('[data-clear-all]')?.addEventListener('click', () => setChecked(checkboxes, false));

            groups.forEach((group) => {
                const groupCheckboxes = Array.from(group.querySelectorAll('[data-permission-checkbox]'));

                group.querySelector('[data-group-view]')?.addEventListener('click', () => setViewOnly(groupCheckboxes));
                group.querySelector('[data-group-select]')?.addEventListener('click', () => setChecked(groupCheckboxes, true));
                group.querySelector('[data-group-clear]')?.addEventListener('click', () => setChecked(groupCheckboxes, false));
            });

            searchInput?.addEventListener('input', () => {
                const term = searchInput.value.trim().toLowerCase();

                groups.forEach((group) => {
                    const haystack = group.dataset.searchText ?? '';
                    group.hidden = term !== '' && !haystack.includes(term);
                });

                refreshSearchState();
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', refresh);
            });

            editor.__refreshRoleEditor = refresh;
            refresh();
            refreshSearchState();
        });

        document.querySelectorAll('[data-role-template-select]').forEach((select) => {
            select.addEventListener('change', () => {
                const editor = document.querySelector(select.dataset.templateTarget);

                if (!editor) {
                    return;
                }

                const checkboxes = Array.from(editor.querySelectorAll('[data-permission-checkbox]'));
                const selectedOption = select.selectedOptions[0];
                const permissions = selectedOption?.dataset.templatePermissions
                    ? JSON.parse(selectedOption.dataset.templatePermissions)
                    : [];

                checkboxes.forEach((checkbox) => {
                    if (!checkbox.disabled) {
                        checkbox.checked = permissions.includes(checkbox.value);
                    }
                });

                editor.__refreshRoleEditor?.();
            });
        });
    </script>
@endpush
