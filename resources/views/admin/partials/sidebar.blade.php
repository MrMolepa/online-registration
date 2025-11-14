@inject('menuService', 'App\Services\MenuService')
<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
       
        <nav>
            <ul class="nav">
                {!! $menuService->renderMenu('admin') !!}
            </ul>
        </nav>
 
    </div>
</div>


