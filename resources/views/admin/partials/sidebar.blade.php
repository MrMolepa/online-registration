<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
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
</div>

