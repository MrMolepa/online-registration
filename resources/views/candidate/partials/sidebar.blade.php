<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        {{-- Dynamic Menu Items --}}
        {{-- Dynamic Menu Items --}}
        @foreach($dynamicMenus as $menu)
            <li class="nav-item {{ isMenuActive($menu) ? 'active' : '' }}">
                @if($menu->children && $menu->children->isNotEmpty())
                    {{-- Parent Menu with Children --}}
                    <a class="nav-link" 
                       data-toggle="collapse" 
                       href="#{{ getMenuCollapseId($menu) }}" 
                       aria-expanded="{{ isMenuActive($menu) ? 'true' : 'false' }}" 
                       aria-controls="{{ getMenuCollapseId($menu) }}">
                        <i class="menu-icon {{ $menu->icon ?: 'mdi mdi-menu' }}"></i>
                        <span class="menu-title">{{ $menu->name }}</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse {{ isMenuActive($menu) ? 'show' : '' }}" 
                         id="{{ getMenuCollapseId($menu) }}">
                        <ul class="nav flex-column sub-menu">
                            @foreach($menu->children as $child)
                                <li class="nav-item">
                                    <a class="nav-link {{ isActiveRoute($child->route) ? 'active' : '' }}" 
                                       href="{{ $child->route ? route($child->route) : '#' }}">
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
                    <a class="nav-link {{ isActiveRoute($menu->route) ? 'active' : '' }}" 
                       href="{{ $menu->route ? route($menu->route) : '#' }}">
                        <i class="menu-icon {{ $menu->icon ?: 'mdi mdi-menu' }}"></i>
                        <span class="menu-title">{{ $menu->name }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>