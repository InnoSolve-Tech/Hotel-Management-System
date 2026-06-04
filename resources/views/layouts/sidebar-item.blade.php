@php
    $hasChildren = ! empty($item['children']);
@endphp

<li class="nav-item {{ $hasChildren && $item['open'] ? 'menu-open' : '' }} hotelio-sidebar__item hotelio-sidebar__item--level-{{ $level }}">
    <a
        href="{{ $item['path'] ?? '#' }}"
        class="nav-link {{ $item['active'] ? 'active' : '' }} {{ $hasChildren ? 'hotelio-sidebar__link--parent' : 'hotelio-sidebar__link--child' }}"
        @if ($hasChildren)
            aria-expanded="{{ $item['open'] ? 'true' : 'false' }}"
        @endif
    >
        <p class="mb-0">{{ $item['title'] }}</p>
        @if ($hasChildren)
            <span class="hotelio-sidebar__caret" aria-hidden="true"></span>
        @endif
    </a>

    @if ($hasChildren)
        <ul class="nav nav-treeview hotelio-sidebar__tree hotelio-sidebar__tree--level-{{ $level + 1 }}">
            @foreach ($item['children'] as $child)
                @include('layouts.sidebar-item', ['item' => $child, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
