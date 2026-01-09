<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $menus = Menu::with('parent')
                ->select(['id', 'name', 'route', 'icon', 'order', 'parent_id', 'guard_name', 'is_active'])
                ->withCount('children');
            //datatables processing
            return DataTables::of($menus)
                ->addColumn('parent_name', function($menu) {
                    return $menu->parent ? $menu->parent->name : '-';
                })
                ->addColumn('status', function($menu) {
                    return $menu->is_active ? 'Active' : 'Inactive';
                })
                ->addColumn('actions', function($menu) {
                    return '
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.menus.edit', $menu->id) . '">Edit</button>
                        <button class="btn btn-warning btn-sm permissions-btn" data-url="' . route('admin.menu-permissions.menu', $menu->id) . '" data-name="' . e($menu->name) . '">Permissions</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.menus.destroy', $menu->id) . '">Delete</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }


        return view('admin.menus.index');
    }

    public function store(Request $request)
    {

        $guards = array_keys(config('auth.guards'));
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:menus,id',
            'guard_name' => ['string', Rule::in($guards)],
            'is_active' => 'nullable|boolean',
        ]);

        $menu = Menu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu created successfully',
            'menu' => $menu

        ]);
    }

    //show method to get menu details for editing
    public function edit(Menu $menu)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.menus.update', $menu->id),
            'menu' => $menu->load('parent')
        ]);
    }


    public function update(Request $request, Menu $menu)
    {
        $guards = array_keys(config('auth.guards'));
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:menus,id|not_in:' . $menu->id,
            'guard_name' => ['string', Rule::in($guards)],
            'is_active' => 'nullable|boolean',
        ]);

        $menu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu updated successfully',
            'menu' => $menu->fresh('parent')
        ]);
    }

    public function destroy(Menu $menu)
    {
        // Check if menu has children
        if ($menu->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete menu with child items'
            ]);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully'
        ]);
    }

    public function getGuards()
    {
        $guards = array_keys(Config('auth.guards'));
        return response()->json([
            'success' => true,
            'guards' => $guards,
        ]);
    }


    // Get menu options for dropdowns
    public function getMenuOptions()
    {
        $menus = Menu::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'menus' => $menus
        ]);
    }

    // Get menu tree for drag and drop
    public function getMenuTree()
    {
        $menus = Menu::whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get()
            ->map(function($menu) {
                return $this->formatMenuForTree($menu);
            });

        return response()->json([
            'success' => true,
            'menus' => $menus
        ]);
    }

    // Format menu for tree view
    private function formatMenuForTree($menu)
    {
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'route' => $menu->route,
            'icon' => $menu->icon,
            'order' => $menu->order,
            'parent_id' => $menu->parent_id,
            'guard_name' => $menu->guard_name,
            'is_active' => $menu->is_active,
            'edit_url' => route('admin.menus.edit', $menu->id),
            'delete_url' => route('admin.menus.destroy', $menu->id),
            'permissions_url' => route('admin.menu-permissions.menu', $menu->id),
            'children' => $menu->children->map(function($child) {
                return $this->formatMenuForTree($child);
            })
        ];
    }

    // Reorder menus via drag and drop
    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $item) {
            Menu::where('id', $item['id'])->update([
                'parent_id' => $item['parent_id'],
                'order' => $item['order']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu order updated successfully'
        ]);
    }
}
