<li class="list-group-item" data-id="{{ $menu->id }}">
    <div class="d-flex align-items-center justify-content-between">
        <span class="handle" style="cursor: grab;">☰</span>
        <span>{{ $menu->name }}</span>
        
    </div>
    

    @if ($menu->children && $menu->children->count())
        <ul class="list-group mt-2">
            @foreach ($menu->children as $child)
                @include('admin.partials.menu_item', ['menu' => $child])
            @endforeach
        </ul>
    @endif
    
</li>