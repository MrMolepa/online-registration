<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\ActivityLogStatus;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $activities = Activity::query()->whereYear('created_at','=',date('Y'));

            return DataTables::eloquent($activities)
                ->editColumn('created_at', function ($row) {
                    return   $row->created_at;
                })
                ->editColumn('subject_type', function ($row) {
                    return    $row->subject_type;
                })
                ->editColumn('causer_id', function ($row) {
                    $causer = $row->causer_id;

                    if (is_null($causer)) {
                        $causer = "Candidate";
                    } else {

                        if (isset($row->causer->center_name)) {
                            $causer = $row->causer->center_name;
                        } else {
                            $causer = isset($row->causer->email)?$row->causer->email:$row->causer;
                        }
                    }
                    return     $causer;
                })
                ->editColumn('description', function ($row) {
                    return    $row->description;
                })
                ->editColumn('properties', function ($row) {
                    $result = $row->properties;
                    return   "<pre>  $result</pre>";
                })
                ->rawColumns(['created_at', 'subject_type', 'causer_id', 'description', 'properties'])
                ->toJson();
            // dd($activities);
            //   dd($activities);

        }

        return view('admin.logs.logs');
    }

    private function setEnvironmentValue($environmentName, $configKey, $newValue)
    {

        file_put_contents(App::environmentFilePath(), str_replace(
            $environmentName . '=' .  env($environmentName),
            $environmentName . '=' . $newValue,
            file_get_contents(App::environmentFilePath())

        ));
        Config::set($configKey, env($environmentName));

        Artisan::call("config:cache");

        // Reload the cached config

    }


    public function setActitiesLogs(Request $request)
    {
        if ($request->status == 0) {
            $this->setEnvironmentValue("ACTIVITY_LOGGER_ENABLED", 'activitylog.enabled', 'false');
        } else {
            $this->setEnvironmentValue("ACTIVITY_LOGGER_ENABLED", 'activitylog.enabled', 'false');
        }
        return response()->json(['status' => env('ACTIVITY_LOGGER_ENABLED'), 'check' => $request->status]);
    }
}
