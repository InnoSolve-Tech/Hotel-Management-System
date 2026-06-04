@php
    use Illuminate\Support\Str;

    $selectedPermissions = array_values(array_unique($selectedPermissions ?? []));
    $grantablePermissions = $grantablePermissions ?? [];
    $isLocked = $isLocked ?? false;
    $editorId = $editorId ?? 'role-editor-' . uniqid();
    $actionMeta = [
        'view' => ['title' => 'View', 'hint' => 'Read access'],
        'create' => ['title' => 'Create', 'hint' => 'Add records'],
        'edit' => ['title' => 'Edit', 'hint' => 'Update records'],
        'delete' => ['title' => 'Delete', 'hint' => 'Remove records'],
    ];
@endphp

<div class="hotelio-role-editor" id="{{ $editorId }}" data-role-editor>
    <div class="hotelio-role-editor__toolbar">
        <label class="hotelio-role-editor__search mb-0">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input
                type="search"
                class="form-control"
                placeholder="Search modules or permissions"
                data-permission-search
            >
        </label>

        <div class="hotelio-role-editor__summary">
            <span class="hotelio-role-editor__count" data-selected-count></span>
            <span class="hotelio-role-editor__count hotelio-role-editor__count--muted">
                {{ count($grantablePermissions) }} available
            </span>
        </div>

        @unless ($isLocked)
            <div class="hotelio-role-editor__actions">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-select-view>View only</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-select-all>Full access</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-clear-all>Clear</button>
            </div>
        @endunless
    </div>

    <div class="hotelio-permission-grid hotelio-permission-grid--friendly" data-permission-grid>
        @foreach ($permissionGroups as $group)
            @php
                $groupPermissionNames = array_column($group['permissions'], 'name');
                $groupSelectedCount = count(array_intersect($selectedPermissions, $groupPermissionNames));
                $groupGrantableCount = count(array_intersect($grantablePermissions, $groupPermissionNames));
                $searchText = strtolower($group['label'] . ' ' . implode(' ', array_column($group['permissions'], 'label')));
            @endphp

            <section
                class="hotelio-permission-card hotelio-permission-card--friendly"
                data-permission-group
                data-search-text="{{ $searchText }}"
            >
                <div class="hotelio-permission-card__header">
                    <div>
                        <h3>{{ $group['label'] }}</h3>
                        <p class="mb-0">
                            {{ $groupGrantableCount }} available
                            · <span data-group-count>{{ $groupSelectedCount }}</span> selected
                        </p>
                    </div>

                    @unless ($isLocked)
                        <div class="hotelio-permission-card__actions">
                            <button type="button" class="btn btn-outline-secondary btn-xs" data-group-view>View</button>
                            <button type="button" class="btn btn-outline-secondary btn-xs" data-group-select>Full</button>
                            <button type="button" class="btn btn-outline-secondary btn-xs" data-group-clear>Clear</button>
                        </div>
                    @endunless
                </div>

                <div class="hotelio-permission-actions">
                    @foreach ($group['permissions'] as $permission)
                        @php
                            $permissionName = $permission['name'];
                            $isChecked = in_array($permissionName, $selectedPermissions, true);
                            $isDisabled = $isLocked || ! in_array($permissionName, $grantablePermissions, true);
                            $meta = $actionMeta[$permission['action']] ?? [
                                'title' => Str::headline($permission['action']),
                                'hint' => 'Permission',
                            ];
                        @endphp

                        <label class="hotelio-permission-row {{ $isChecked ? 'is-selected' : '' }} {{ $isDisabled ? 'is-disabled' : '' }}">
                            <span class="hotelio-permission-row__main">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permissionName }}"
                                    data-permission-checkbox
                                    data-permission-action="{{ $permission['action'] }}"
                                    @checked($isChecked)
                                    @disabled($isDisabled)
                                >
                                <span class="hotelio-permission-row__title">{{ $meta['title'] }}</span>
                            </span>
                            <span class="hotelio-permission-row__hint">{{ $meta['hint'] }}</span>
                        </label>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div class="hotelio-role-editor__empty d-none" data-permission-empty>
        <i class="fas fa-search"></i>
        <span>No modules match this search yet.</span>
    </div>
</div>
