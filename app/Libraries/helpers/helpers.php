<?php

use App\Models\Candidate;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\FeeStracture;
use App\Models\Invitation;
use App\Models\Publication;
use App\Models\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

function is_opened($level, $session)
{
    $financial_year = date('Y') . '-' . (date('Y') + 1);
    if ($level == "JC") {
        $level = "jc-private";
    } else {
        $level = "lgcse-private";
    }
    $open = FeeStracture::where('candidate_type', '=',  $level)
        ->where('financial_year', '=', $financial_year)->first();
    $publication =  is_publised($level, $session);
    return isset($open) && isset($publication) ? true : false;
}


function is_activate($level)
{
    $open_level = DB::table('levels')
        ->where('level', '=', $level)
        ->where('is_active', '=', 1)
        ->first();
    return isset($open_level) ? true : false;
}

function is_publised($level, $session)
{
    $publication = Publication::where('level', '=', $level)
        ->where('session', '=', $session)
        ->where('publish', '=', 1)->first();
    return isset($publication) ? true : false;
}

function is_paid($candidate_no, $national_id, $level, $session, $financial_year)
{



    $candidate = DB::table('center_candidate')
        ->where('candidate_no', '=', $candidate_no)
        ->where('national_id', '=', $national_id)
        ->where('level', '=', $level)
        ->where('session', '=', $session)
        ->where('financial_year', '=', $financial_year)
        ->first();


    $invoices = DB::table('fee_candidate_histories')
        ->where('fee_candidate_histories.candidate_id', '=', $candidate->id)
        ->where('fee_candidate_histories.status', '=', 1)
        ->first();

    $approval_sponsored = DB::table('request_action')
        ->select(['requests.request_data_id'])
        ->join('requests', 'requests.id', '=', 'request_action.request_id')
        ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
        ->join('actions', 'actions.id', '=', 'request_action.action_id')
        ->join('processes', 'processes.id', '=', 'actions.process')
        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
        ->where('requests.request_data_id', '=', $candidate->id)
        ->where('request_action.is_complete', '=', 1)
        ->where('action_types.status', '=', '1')
        ->groupBy('request_action.request_id')
        ->having(DB::raw("count(request_action.request_id)"), '>', 1)
        ->first();
    return isset($invoices) ||  isset($approval_sponsored)  ? true : false;
}







function is_paid_sponsored($candidate_id)
{
    $center_candidate = DB::table('center_candidate')
        ->where('id', '=', $candidate_id)
        ->first();
    $approval_sponsored = DB::table('request_action')
        ->select(['requests.request_data_id'])
        ->join('requests', 'requests.id', '=', 'request_action.request_id')
        ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
        ->join('actions', 'actions.id', '=', 'request_action.action_id')
        ->join('processes', 'processes.id', '=', 'actions.process')
        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
        ->where('requests.request_data', '=', CenterCandidate::class)
        ->where('requests.request_data_id', '=',  $candidate_id)
        ->where('request_action.is_complete', '=', 1)
        ->where('action_types.name', '=', 'Approve')
        ->groupBy('request_action.request_id')
        ->having(DB::raw("count(request_action.request_id)"), '>', 1)
        ->first();



    $invoices = DB::table('fee_candidate_histories')
        ->where('fee_candidate_histories.candidate_id', '=', $candidate_id)
        ->where('fee_candidate_histories.status', '=', 1)
        ->first();

    if (isset($approval_sponsored) || isset($invoices)) {
        if (isset($approval_sponsored)) {
            return (object) [
                'sponsors' =>  $center_candidate->sponser,
                'label' => 'Approved',
                'color' => '#ffc107',
                'status' => true,
            ];
        }
        if (isset($invoices)) {
            return (object) [
                'sponsors' =>  $center_candidate->sponser,
                'label' => 'paid',
                'color' => '#28a745',
                'status' => true,
            ];
        }
    }
    return (object) [
        'sponsors' =>  $center_candidate->sponser,
        'label' => 'unpaid',
        'color' => '#dc3545',
        'status' => false,
    ];
}


function getNextCandidateNumber()
{
    $length = 7;
    $year = (date('m') <= 12) ? date('Y') : (date('Y') + 1);
    $prefix = substr($year, 2, 4);
    // ensure there is a record for the current financial year
    DB::statement("INSERT INTO candidate_sequences (financial_year) VALUES ({$year}) ON DUPLICATE KEY UPDATE financial_year = financial_year, id=LAST_INSERT_ID(id)");
    $lastInsertId = DB::getPDO()->lastInsertId();
    // automatically increment the count AND get the value
    DB::statement("UPDATE candidate_sequences SET current = LAST_INSERT_ID(current) + 1 WHERE id = {$lastInsertId}");
    $current = DB::getPDO()->lastInsertId();
    return sprintf('%s%0' . $length . 'd', $prefix, intval($current) + 1);
}

// function  getMarkers()
// {

//     $center = Center::with('subjects')->where('center_no', '=', auth()->user()->center_no)->first();
//     $centerSessions = json_decode($center->sessions, true);

//     $date = date('Y-m-d');
//     $session = Session::where('financial_closing_date', '>=',  $date)
//         ->whereIn('session', $centerSessions)->first();
//     $center_no = $center->center_no;
//     $invitation = Invitation::where('session', $session->session)
//         ->where('financial_year', $session->financial_year)
//         ->where('center_no', $center_no);
// }











function getPendingInvitations()
{
    // Get the center of the authenticated user
    $center = Center::with('subjects')
        ->where('center_no', auth()->user()->center_no)
        ->first();


    // Decode the sessions JSON to array
    $centerSessions = json_decode($center->sessions, true);
    // Current date
    $date = date('Y-m-d');

    // Find the first active session for this center
    $session = Session::where('financial_closing_date', '>=', $date)
        ->whereIn('session', $centerSessions)
        ->first();


    // Query invitations for this center and session
    $invitationsQuery = Invitation::where('session', $session->session)
        ->where('financial_year', $session->financial_year)
        ->where('center_no', $center->center_no);

    $totalInvitations = $invitationsQuery->count();
    $pendingInvitations = (clone $invitationsQuery)
        ->where('status', 'complete')
        ->count();


    return (object)[
        'total' => $totalInvitations,
        'pending' => $pendingInvitations,
    ];;
}


function initials($full_names)
{
    preg_match('/(?:\w+\. )?(\w+).*?(\w+)(?: \w+\.)?$/', $full_names, $result);
    return strtoupper($result[1][0] . $result[2][0]);
}




function banks()
{
    return [
        'standard' => [
            'label'   => 'Standard Lesotho Bank',
            'pattern' => '/^\d{10}$/',
        ],
        'nedbank' => [
            'label'   => 'Nedbank Lesotho',
            'pattern' => '/^\d{12}$/',
        ],
        'fnb' => [
            'label'   => 'First National Bank (FNB) Lesotho',
            'pattern' => '/^\d{9,12}$/',
        ],
        'postbank' => [
            'label'   => 'Lesotho PostBank',
            'pattern' => '/^\d{6,16}$/',
        ],
        'generic' => [
            'label'   => 'Other / Generic',
            'pattern' => '/^\d{6,16}$/',
        ],
    ];
}

function getFormattedNumber(
    $value,
    $locale = 'en_US',
    $style = NumberFormatter::DECIMAL,
    $precision = 2,
    $groupingUsed = true,
    $currencyCode = 'USD'
) {
    $formatter = new NumberFormatter($locale, $style);
    $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
    $formatter->setAttribute(NumberFormatter::GROUPING_USED, $groupingUsed);
    if ($style == NumberFormatter::CURRENCY) {
        $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currencyCode);
    }

    return $formatter->format($value);
}

function getEnumValues($table, $column)
{
    $types = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE FIELD='$column'"))[0]->Type;
    preg_match("/^enum\(\'(.*)\'\)$/", $types, $matches);
    $enum = explode("','", $matches[1]);
    return      $enum;
}



function grCodeGenerator($candidate_no, $iputText)
{
    require_once(__DIR__ . '/../fpdf/phpqrcode/qrlib.php');
    $data = "";
    foreach ($iputText as $key => $value) {
        $header = ucfirst(str_replace('_', ' ', $key));
        if (!in_array($key, ['sponser', 'type'])) {
            $data .= "$header : $value\n";
        }
    }
    QRcode::png($data, "$candidate_no.png");
    $image = base64_encode(file_get_contents("$candidate_no.png"));
    return  $image;
}



function insertOrUpdate($table, array $rows)
{

    $first = reset($rows);
    $columns = implode(
        ',',
        array_map(function ($value) {
            return "$value";
        }, array_keys($first))
    );
    $values = implode(
        ',',
        array_map(function ($row) {
            return '(' . implode(
                ',',
                array_map(function ($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $row)
            ) . ')';
        }, $rows)
    );
    $updates = implode(
        ',',
        array_map(function ($value) {
            return "$value = VALUES($value)";
        }, array_keys($first))
    );
    $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";
    return DB::statement($sql);
}


function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


function generateToken()
{
    return md5(rand(1, 10) . microtime());
}

function px2mm($px, $dpi = 96)
{
    return $px * 25.4 / $dpi;
}

function mm2px($mm, $dpi = 96)
{
    return $mm * $dpi / 25.4;
}


/**
 * Check if current route matches menu route
 */
if (!function_exists('isActiveRoute')) {
    function isActiveRoute($route)
    {
        if (!$route) {
            return false;
        }
        
        try {
            // Check if current route name matches
            if (Route::currentRouteName() == $route) {
                return true;
            }
            
            // Check if current URL contains the route
            return request()->is($route . '*');
        } catch (\Exception $e) {
            return false;
        }
    }
}

/**
 * Check if menu or its children are active
 */
if (!function_exists('isMenuActive')) {
    function isMenuActive($menu)
    {
        // Check if current menu is active
        if (isActiveRoute($menu->route)) {
            return true;
        }
        
        // Check if any children are active
        if ($menu->children && $menu->children->isNotEmpty()) {
            foreach ($menu->children as $child) {
                if (isActiveRoute($child->route)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

/**
 * Generate unique collapse ID for menu
 */
if (!function_exists('getMenuCollapseId')) {
    function getMenuCollapseId($menu)
    {
        return 'menu-' . $menu->id . '-collapse';
    }
}


/**
 * Check if current route matches menu route
 */
if (!function_exists('isActiveRoute')) {
    function isActiveRoute($route)
    {
        if (!$route) {
            return false;
        }
        
        try {
            // Check if current route name matches
            if (Route::currentRouteName() == $route) {
                return true;
            }
            
            // Check if current URL contains the route
            return request()->is($route . '*');
        } catch (\Exception $e) {
            return false;
        }
    }
}

/**
 * Check if menu or its children are active
 */
if (!function_exists('isMenuActive')) {
    function isMenuActive($menu)
    {
        // Check if current menu is active
        if (isActiveRoute($menu->route)) {
            return true;
        }
        
        // Check if any children are active
        if ($menu->children && $menu->children->isNotEmpty()) {
            foreach ($menu->children as $child) {
                if (isActiveRoute($child->route)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

/**
 * Generate unique collapse ID for menu
 */
if (!function_exists('getMenuCollapseId')) {
    function getMenuCollapseId($menu)
    {
        return 'menu-' . $menu->id . '-collapse';
    }
}

/**
 * Check if user can access a specific menu
 */
if (!function_exists('userCanAccessMenu')) {
    function userCanAccessMenu($menuId, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }
        
        if (!$user) {
            return false;
        }
        
        $menu = \App\Models\Menu::find($menuId);
        
        if (!$menu) {
            return false;
        }
        
        $menuPermissions = $menu->permissions()->with(['role', 'permission'])->get();
        
        // If no permissions assigned, menu is accessible to all
        if ($menuPermissions->isEmpty()) {
            return true;
        }
        
        // Get user's roles
        $userRoles = $user->roles->pluck('id')->toArray();
        
        // Get user's permissions (both direct and through roles)
        $userPermissions = $user->permissions->pluck('id')->toArray();
        $rolePermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->pluck('id');
        })->toArray();
        
        $allUserPermissions = array_unique(array_merge($userPermissions, $rolePermissions));
        
        // Check if user has any of the required role + permission combinations
        foreach ($menuPermissions as $menuPermission) {
            $hasRole = in_array($menuPermission->role_id, $userRoles);
            $hasPermission = in_array($menuPermission->permission_id, $allUserPermissions);
            
            if ($hasRole && $hasPermission) {
                return true;
            }
        }
        
        return false;
    }
}

/**
 * Get accessible menus for current user
 */
if (!function_exists('getUserMenus')) {
    function getUserMenus($guardName = null)
    {
        if (!auth()->check()) {
            return collect([]);
        }
        
        if (!$guardName) {
            $guardName = auth()->getDefaultDriver();
        }
        
        $user = auth()->user();
        
        $menus = \App\Models\Menu::where('is_active', true)
            ->where('guard_name', $guardName)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($guardName) {
                $query->where('is_active', true)
                    ->where('guard_name', $guardName)
                    ->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
        
        return $menus->filter(function ($menu) use ($user) {
            return userCanAccessMenu($menu->id, $user);
        })->map(function ($menu) use ($user) {
            if ($menu->children->isNotEmpty()) {
                $filteredChildren = $menu->children->filter(function ($child) use ($user) {
                    return userCanAccessMenu($child->id, $user);
                });
                $menu->setRelation('children', $filteredChildren);
            }
            return $menu;
        })->filter(function ($menu) {
            if ($menu->children->isNotEmpty()) {
                return $menu->children->count() > 0;
            }
            return true;
        });
    }
}
