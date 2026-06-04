<aside class="main-sidebar sidebar-light-primary elevation-4 dashbroad__sidebar__bg">
    <div class="sidebar custom-sidebar pt-3">
        <nav class="mt-0">
            <ul class="nav nav-pills nav-sidebar flex-column hotelio-sidebar__nav" data-widget="treeview" role="menu" data-accordion="false">
                @foreach (\App\Support\NavigationBuilder::forUser(auth()->user(), request()) as $item)
                    @include('layouts.sidebar-item', ['item' => $item, 'level' => 0])
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
