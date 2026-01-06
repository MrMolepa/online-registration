<nav class="navbar-default navbar-side" role="navigation">
    <div class="sidebar-collapse">
         <nav>
            <ul class="nav">

                {{-- Dynamic Menu Items --}}
                @if(isset($dynamicMenus) && $dynamicMenus->isNotEmpty())
                    @foreach($dynamicMenus as $menu)
                        <li>
                            @if($menu->children && $menu->children->isNotEmpty())
                                {{-- Parent Menu with Children --}}
                                <a href="#{{ getMenuCollapseId($menu) }}"
                                   data-toggle="collapse"
                                   class="{{ isMenuActive($menu) ? '' : 'collapsed' }}">
                                    <i class="{{ $menu->icon ?: 'lnr lnr-menu' }}"></i>
                                    <span>{{ $menu->name }}</span>
                                    <i class="icon-submenu lnr lnr-chevron-left"></i>
                                </a>
                                <div id="{{ getMenuCollapseId($menu) }}"
                                     class="collapse {{ isMenuActive($menu) ? 'in' : '' }}">
                                    <ul class="nav">
                                        @foreach($menu->children as $child)
                                            <li>
                                                <a href="{{ $child->route ? route($child->route) : '#' }}"
                                                   class="{{ isActiveRoute($child->route) ? 'active' : '' }}">
                                                    @if($child->icon)
                                                        <i class="{{ $child->icon }}"></i>
                                                    @endif
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                {{-- Single Menu Item --}}
                                <a href="{{ $menu->route ? route($menu->route) : '#' }}"
                                   class="{{ isActiveRoute($menu->route) ? 'active' : '' }}">
                                    <i class="{{ $menu->icon ?: 'lnr lnr-menu' }}"></i>
                                    <span>{{ $menu->name }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif


            </ul>
        </nav>


    </div>
</nav> 




 {{-- <nav class="navbar-default navbar-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="main-menu">

            @forelse($dynamicMenus as $menu)

                <li>
                    <a href="{{ $menu->route_name ? route($menu->route_name) : '#' }}"
                       class="{{ request()->routeIs($menu->route_name) ? 'active-menu' : '' }}">

                        {{-- Icon --}}
                        {{-- @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endif

                        {{ $menu->name }}
                    </a>
                </li>

            @empty
                <li class="text-muted px-3">
                    No menus available
                </li>
            @endforelse

        </ul>
    </div>
</nav>  --}}
{{-- school/partials/sidebar.blade.php


<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <ul class="nav">

                {{-- Dynamic Menu Items --}}



            </ul>
        </nav>
    </div>
</div>
