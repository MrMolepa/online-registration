<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientMail;
use App\Models\CenterCandidate;
use App\Models\OneTimeServiceItemSale;
use App\Models\OneTimeServicesItem;
use App\Models\ServiceAttribute;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use ZipStream\ZipStream;
use ZipStream\OperationMode;



class ServiceSaleContolller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'desc')
            ->distinct()
            ->get()->pluck('year');

        if ($request->ajax()) {
            $paid_services = DB::table('one_time_services_item_sale')
                ->select(
                    'clients.first_name',
                    'clients.last_name',
                    'clients.email',
                    'clients.phone',
                    'clients.national_identity',
                    'one_time_services_item.name',
                    'one_time_service_payment_histories.reference_no',
                    'one_time_services_item_sale.id',
                    'one_time_services_item_sale.one_time_services_id',
                    'one_time_services_item_sale.price',
                    'one_time_services_item_sale.financial_year',
                    'one_time_services_item_sale.requirements',
                    'one_time_services_item_sale.reference_number',
                    'one_time_services_item_sale.is_checked',
                    'one_time_services_item_sale.created_at',
                )
                ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
                ->join('one_time_service_payment_histories', 'one_time_services_item_sale.client_id', '=', 'one_time_service_payment_histories.client_id')
                ->join('one_time_services_item', function ($join) {
                    $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id');
                    $join->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
                });

            if (isset($request->service)) {
                $paid_services = $paid_services->where('one_time_services_item.id', $request->service);
            }
            if (isset($request->year)) {
                $paid_services = $paid_services->where('one_time_services_item.financial_year', $request->year);
            }
            $paid_services = $paid_services->orderBy('one_time_services_item_sale.is_checked', 'ASC')
                ->orderBy('one_time_services_item_sale.created_at', 'DESC')
                ->get();
            if ($request->has('service_items')) {
                $oneTimeServicesItems = OneTimeServicesItem::where('financial_year', '=', $request->year)->get();
                return response()->json(['serviceItem' =>    $oneTimeServicesItems]);
            }
            return DataTables::of($paid_services)
                ->setRowId('id')
                ->editColumn('first_name', function ($model) {
                    return   $model->first_name;
                })
                ->addColumn('last_name', function ($model) {
                    return $model->last_name;
                })
                ->addColumn('email', function ($model) {
                    return $model->email;
                })
                ->addColumn('phone', function ($model) {
                    return $model->phone;
                })
                ->addColumn('national_identity', function ($model) {
                    return $model->national_identity;
                })
                ->addColumn('name', function ($model) {
                    return $model->name;
                })
                ->addColumn('price', function ($model) {
                    return $model->price;
                })
                ->addColumn('created_at', function ($model) {
                    return $model->created_at;
                })
                ->editColumn('requirements', function ($model) {
                    return  json_decode($model->requirements, true);
                })
                ->addColumn('service_attributes', function ($model) {
                    $whereData = array(array('one_time_service_id', '=', "$model->one_time_services_id"));
                    $serviceAttributes = ServiceAttribute::where($whereData)->get();
                    return    $serviceAttributes;
                })
                ->addColumn('status', function ($model) {
                    $output = "";
                    switch ($model->is_checked) {
                        case '1':
                            $output .= '<span class="invalid-status"></span>';
                            break;
                        case '2':
                            $output .= '<span class="not-checked-status"></span>';
                            break;
                        case '3':
                            $output .= '<span class="valid-status"></span>';
                            break;
                        default:
                            # code...
                            break;
                    }
                    return $output;
                })


                ->editColumn('actions', function ($model) {
                    $actions = '<div class="btn-group actions">';
                    $actions .= " <a href='javascript:void(0)' data-url='" . route('admin.service-sales.editcomments', $model->id) . "'
                                        data-toggle='tooltip' title='Edit Service' class='btn btn-sm btn-info btn-edit-comment'> <i
                                            class='fas fa-check-square'></i>
                                    </a>";
                    $actions .= "<a href='javascript:void(0)'
                                    data-toggle='tooltip' title='Check Service'  data-url='" . route('admin.service-sales.edit', $model->id) . "'  class='btn btn-sm btn-primary  btn-edit-check'>
                                    <i class='fas fa-edit'></i>
                                </a>";
                    $actions   .=  "<a href='javascript:void(0)' title='Delete Service' class='btn-delete btn-sm btn btn-danger' data-url='" . route('admin.service-sales.destroy', $model->id) . "'  type='button' rel='tooltip' title='Delete'>
                                <i class='far fa-trash-alt'></i>
                        </a>";
                    $actions .= '</div>';
                    return    $actions;
                })
                ->rawColumns(['status', 'first_name', 'last_name', 'email', 'phone', 'national_identity', 'requirements', 'actions'])
                ->make(true);
        }

        $services = OneTimeServicesItem::where('financial_year', $years[0])
            ->get();
        $serviceAttributes = ServiceAttribute::groupBy('one_time_service_id')
            ->where('frontend_type', '=', 'file')->get();

        return view('admin.services.sales', compact('years', 'serviceAttributes', 'services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }








    public function exportFiles(Request $request)
    {
        $start   = $request->query('start_date');
        $end     = $request->query('end_date');
        $fileField = $request->file_name; // DB column storing file path

        $query = DB::table('v_one_time_services_item_sale');



        // Only apply filter if both dates are non-empty strings
        if (!empty($start) && !empty($end)) {
            $start = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
            $end   = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        ini_set('memory_limit', '-1');
        set_time_limit(-1);

        // Filename for download
        $zipFileName = $start && $end
            ? "{$fileField}_{$start}_to_{$end}.zip"
            : "$fileField" . now()->format('Ymd_His') . '.zip';

        return response()->streamDownload(function () use ($query, $fileField, $zipFileName) {
            // Initialize ZipStream for version 2.x
            $zip = new ZipStream(
                outputName: $zipFileName,
                sendHttpHeaders: false
            );

            static $added = [];

            // Process records in chunks
            $query->orderBy('id')->chunk(100, function ($records) use ($zip, $fileField, &$added) {
                foreach ($records as $record) {
                    if (!isset($record->{$fileField})) {
                        Log::warning("File field '$fileField' not found in record ID: " . ($record->id ?? 'unknown'));
                        continue;
                    }

                    $path = $record->{$fileField};
                    if (!$path) {
                        Log::warning("Empty file path for record ID: " . ($record->id ?? 'unknown'));
                        continue;
                    }

                    Log::debug("Raw database path: $path");
                    $relativePath = preg_replace('#/{2,}#', '/', ltrim(str_replace('storage/', 'app/public/', $path), '/'));
                    $fullPath = storage_path($relativePath); // Try storage/public/
                    Log::debug("Trying full path (public): $fullPath");

                    if (!file_exists($fullPath) || !is_readable($fullPath)) {

                        Log::debug("Fallback full path (app/public): $fullPath");

                        if (!file_exists($fullPath) || !is_readable($fullPath)) {
                            Log::warning("File not found or not readable: $fullPath");
                            continue;
                        }
                    }

                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'bin';
                    $title = $record->first_name . " " . $record->last_name . "_" . $record->item_name  ?? $record->id;
                    $fileName = $title . '-' . $record->id . '.' . $extension;

                    $i = 1;
                    while (in_array($fileName, $added)) {
                        $fileName = $title . '-' . $record->id . '-' . $i++ . '.' . $extension;
                    }
                    $added[] = $fileName;

                    Log::info("Adding file to ZIP: $fileName from $fullPath");
                    $zip->addFileFromPath($fileName, $fullPath);
                }
            });

            $zip->finish();
        }, $zipFileName, [
            'Content-Type'  => 'application/octet-stream',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    public function exportFilesa(Request $request)
    {
        $start   = $request->query('start_date');
        $end     = $request->query('end_date');
        $fileField = $request->file_name; // DB column storing file path

        $query = DB::table('v_one_time_services_item_sale');



        // Only apply filter if both dates are non-empty strings
        if (!empty($start) && !empty($end)) {
            $start = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
            $end   = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        // Filename for download
        $zipFileName = $start && $end
            ? "exported_files_{$start}_to_{$end}.zip"
            : 'exported_files_' . now()->format('Ymd_His') . '.zip';

        return response()->streamDownload(function () use ($query, $fileField, $zipFileName) {
            // Initialize ZipStream for version 2.x
            $zip = new ZipStream(
                outputName: $zipFileName,
                sendHttpHeaders: false // Let Laravel handle headers
            );

            static $added = [];

            // Process records in chunks to handle large data
            $query->orderBy('id')->chunk(100, function ($records) use ($zip, $fileField, &$added) {
                foreach ($records as $record) {
                    if (!isset($record->{$fileField})) {
                        Log::warning("File field '$fileField' not found in record ID: " . $record->id);
                        continue;
                    }


                    $path = ltrim(str_replace('storage/', 'public/', $record->{$fileField}), '/');
                    if (!$path) {
                        Log::warning("Empty file path for record ID: " . $record->id);
                        continue;
                    }

                    $fullPath = storage_path('app/' . $path);
                    if (!file_exists($fullPath)) {
                        Log::warning("File not found or not readable: $fullPath");
                        continue;
                    }


                    $fullPath = storage_path('/' . ltrim($path, '/'));
                    if (!file_exists($fullPath)) {
                        Log::warning("File not found: $fullPath");
                        continue;
                    }

                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $title = $record->reference_no ?? $record->id;
                    $fileName = $title . '-' . $record->id . '.' . $extension;

                    // Ensure unique file names
                    $i = 1;
                    while (in_array($fileName, $added)) {
                        $fileName = $title . '-' . $record->id . '-' . $i++ . '.' . $extension;
                    }
                    $added[] = $fileName;

                    $zip->addFileFromPath($fileName, $fullPath);
                }
            });

            $zip->finish();
        }, $zipFileName, [
            'Content-Type'  => 'application/octet-stream',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $itemSale = DB::table('one_time_services_item_sale')
            ->select(
                'clients.first_name',
                'clients.last_name',
                'clients.email',
                'clients.phone',
                'clients.national_identity',
                'one_time_services_item.name',
                'one_time_service_payment_histories.reference_no',
                'one_time_services_item_sale.id',
                'one_time_services_item_sale.one_time_services_id',
                'one_time_services_item_sale.price',
                'one_time_services_item_sale.financial_year',
                'one_time_services_item_sale.requirements',
                'one_time_services_item_sale.reference_number',
                'one_time_services_item_sale.is_checked'
            )
            ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
            ->join('one_time_service_payment_histories', 'one_time_services_item_sale.client_id', '=', 'one_time_service_payment_histories.client_id')
            ->join('one_time_services_item', function ($join) {
                $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id');
                $join->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
            })->where('one_time_services_item_sale.id', '=', $id)
            ->first();

        $requirements = collect(json_decode($itemSale->requirements, true));
        $serviceAttributes = ServiceAttribute::where('one_time_service_id', '=', "$itemSale->one_time_services_id")->get();
        $serviceRequirements = collect([]);
        $serviceAttributes =  $serviceAttributes->map(function ($item) use ($requirements, $serviceRequirements) {
            $collect = collect($item->toArray())
                ->only(['code', 'name', 'frontend_type', 'placeholder']);
            foreach ($requirements as $key => $value) {
                if ($item->code == $key) {
                    $collect->put('value', $value);
                }
            }
            $serviceRequirements = $serviceRequirements->push((object)$collect);
        });
        $html = "";

        // dd($serviceRequirements);

        // filterable($name, $code, $placeholder, $frontend_type, $value)
        foreach ($serviceRequirements as $key => $serviceAttribute) {
            $value =   $serviceAttribute->has('value') ? $serviceAttribute['value'] : "";
            $html .= $this->filterable(
                $serviceAttribute['name'],
                $serviceAttribute['code'],
                $serviceAttribute['placeholder'],
                $serviceAttribute['frontend_type'],
                $value
            );
        }
        $url = route('admin.service-sales.update', $id);
        return response()->json(['itemsale' => $itemSale, "test" => $serviceRequirements, 'serviceAttributes' => $html, 'url' => $url]);
    }

    private function filterable($name, $code, $placeholder, $frontend_type, $value)
    {
        switch ($code) {
            case 'candidate_no':
                $input = "<div class='form-group col-md-6'>
                            <label for='$code'>
                                $name
                            </label>
                            <input id='' type='$frontend_type'
                                name='$code' value='$value' placeholder='$placeholder'>
                          </div>";
                return $input;
            case 'exam_series':
                $exams_series = array(
                    "June" => "May/June",
                    "Novemver" => "October/November",
                );
                $exams_seriesHTML = '';
                foreach ($exams_series as $key => $exams_serie) {
                    if ($value == $key) {
                        $exams_seriesHTML .= "<option selected value='$key'>$exams_serie</option>";
                    } else {
                        $exams_seriesHTML .= "<option  value='$key'>$exams_serie $value</option>";
                    }
                }
                $input = "<div class='form-group col-md-6'>
                            <label for='$code'>
                                $name
                            </label>
                            <select id='$code' name='$code'  class='form-control'>
                                <option value=''>Please select</option>
                                $exams_seriesHTML
                            </select>
                         </div>";
                return $input;
                break;
            case 'year':
                $startingYear = date('Y');
                $endingYear = $startingYear - 84;
                $yearHTML = '';
                $years = range($startingYear, $endingYear);
                foreach ($years as $year) {
                    if ($value == $year) {
                        $yearHTML .= "<option selected value='$year'>$year </option>";
                    } else {
                        $yearHTML .= "<option  value='$year'>$year $value </option>";
                    }
                }
                $input = "<div class='form-group col-md-6'>
                              <label for='$code'>
                                $name
                            </label>
                            <select id='year' name='$code' class='form-control' autocomplete='$code'>
                                <option value='' >Please select</option>
                                $yearHTML
                            </select>
                         </div>";
                return $input;
                break;
            case 'center':

                $input = "<div class='form-group col-md-12'>
                            <label for='$code'>
                            $name
                             </label>
                            <select class='form-control' id='livesearch-all-centers' name='$code'>
                                <option value='' selected>Please select</option>
                            </select>
                            <input type='hidden' id='selected-center'  name='selected-center' value='$value'>
                         </div>";
                return $input;
                break;
            default:

                $valuenew = $frontend_type == "date" ?  date("Y-m-d", strtotime($value)) : $value;
                $input = "<div class='form-group col-md-6'>
                                <label for='$code'>
                                    $name
                                </label>
                                <input type='$frontend_type' class='form-control' name='$code' value='$valuenew' id='$code'>
                            </div>";
                return $input;
                break;
        }
    }

    public function editComments($id)
    {
        $itemSale = DB::table('one_time_services_item_sale')
            ->select(
                'clients.first_name',
                'clients.last_name',
                'clients.email',
                'clients.phone',
                'clients.national_identity',
                'one_time_services_item.name',
                'one_time_service_payment_histories.reference_no',
                'one_time_services_item_sale.id',
                'one_time_services_item_sale.one_time_services_id',
                'one_time_services_item_sale.price',
                'one_time_services_item_sale.financial_year',
                'one_time_services_item_sale.requirements',
                'one_time_services_item_sale.comments',
                'one_time_services_item_sale.reference_number',
                'one_time_services_item_sale.is_checked'
            )
            ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
            ->join('one_time_service_payment_histories', 'one_time_services_item_sale.client_id', '=', 'one_time_service_payment_histories.client_id')

            ->join('one_time_services_item', function ($join) {
                $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id');
                $join->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
            })->where('one_time_services_item_sale.id', '=', $id)
            ->first();

        $url = route('admin.service-sales.updatecomments', $id);
        return response()->json(['itemsale' => $itemSale, 'url' => $url]);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'national_identity' => 'required|max:255',
            'phone' => 'required|max:255',
            'email' => 'required|max:255|email'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $requirementsArray = array();
        $itemSale = DB::table('one_time_services_item_sale')
            ->select(
                'clients.first_name',
                'clients.last_name',
                'clients.email',
                'clients.phone',
                'clients.national_identity',
                'one_time_services_item.name',
                'one_time_service_payment_histories.reference_no',
                'one_time_services_item_sale.id',
                'one_time_services_item_sale.one_time_services_id',
                'one_time_services_item_sale.price',
                'one_time_services_item_sale.financial_year',
                'one_time_services_item_sale.requirements',
                'one_time_services_item_sale.reference_number',
                'one_time_services_item_sale.is_checked'
            )
            ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
            ->join('one_time_service_payment_histories', 'one_time_services_item_sale.client_id', '=', 'one_time_service_payment_histories.client_id')
            ->join('one_time_services_item', function ($join) {
                $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id');
                $join->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
            })->where('one_time_services_item_sale.id', '=', $id)
            ->first();

        $itemSalesRequirements = json_decode($itemSale->requirements, true);
        $files = $request->allFiles();
        $reference_number = 'ECoL-' . time();
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                if ($request->has("$key")) {
                    $extension =   $request->file("$key")->getClientOriginalExtension();
                    if (array_key_exists("$key", $itemSalesRequirements)) {
                        if (strtolower($extension) == "pdf") {
                            if (File::exists($itemSalesRequirements[$key])) {
                                File::delete($itemSalesRequirements[$key]);
                            } else {
                                $fileName =  'Service' . '-' . time() . '-' . $request->file("$key")->getClientOriginalName();
                                $filePath = $request->file("$key")->storeAs('services/' .  $reference_number, $fileName, 'public');
                                $requirementsArray[$key] =  "/storage/$filePath";
                            }
                        } else {
                            if (File::exists($itemSalesRequirements[$key])) {
                                File::delete($itemSalesRequirements[$key]);
                            } else {
                                $fileName =  'Service' . '-' . time() . '-' . $request->file("$key")->getClientOriginalName();
                                $filePath = $request->file("$key")->storeAs('services/' .  $reference_number, $fileName, 'public');

                                $requirementsArray[$key] =  "/storage/$filePath";
                            }
                        }
                    } else {
                        if (strtolower($extension) == "pdf") {
                            $fileName =  'Service' . '-' . time() . '-' . $request->file("$key")->getClientOriginalName();
                            $filePath = $request->file("$key")->storeAs('services/' .  $reference_number, $fileName, 'public');
                            $requirementsArray[$key] =  "/storage/$filePath";
                        } else {
                            $fileName =  'Service' . '-' . time() . '-' . $request->file("$key")->getClientOriginalName();
                            $filePath = $request->file("$key")->storeAs('services/' .  $reference_number, $fileName, 'public');

                            $requirementsArray[$key] =  "/storage/$filePath";
                        }
                    }
                }
            }
        }
        $whereData = array(array('one_time_service_id', '=', "$itemSale->one_time_services_id"), array('frontend_type', '!=', 'file'));
        $serviceAttributes = ServiceAttribute::where($whereData)->pluck('code');
        $data = [];
        $validation_rules = [];
        $validation_messages = [];
        foreach ($serviceAttributes as   $serviceAttribute) {
            $data[$serviceAttribute] = $request->get("$serviceAttribute");
            $validation_rules[$serviceAttribute][] = 'required';
            if ($request->get("$serviceAttribute") == "date-of-birth") {
                $requirementsArray[$serviceAttribute] = date("Y-m-d H:i:s", strtotime($request->get("$serviceAttribute")));
            } else {
                $requirementsArray[$serviceAttribute] = $request->get("$serviceAttribute");
            }
        }
        $validator = null;
        $validator = Validator::make($data, $validation_rules, $validation_messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $requirementsArraykeys = array_keys($requirementsArray);
        $itemSalesRequirementkeys = array_keys($itemSalesRequirements);
        $not_update_array = array_diff($itemSalesRequirementkeys, $requirementsArraykeys);
        foreach ($not_update_array as $key => $value) {
            $requirementsArray[$value] = $itemSalesRequirements[$value];
        }
        DB::table('one_time_services_item_sale')
            ->where('id', $id)
            ->update(['requirements' => json_encode($requirementsArray)]);
        return response()->json(['success' => "Successfully updated the records"]);
    }


    public function updateComments(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'national_identity' => 'required|max:255',
            'phone' => 'required|max:255',
            'email' => 'required|max:255|email',
            'is_checked' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $client = DB::table('one_time_services_item_sale')
            ->select(
                'clients.first_name',
                'clients.last_name',
                'clients.email',
                'clients.phone',
                'clients.national_identity',
                'one_time_services_item.name',
                'one_time_service_payment_histories.reference_no',
                'one_time_services_item_sale.price'
            )
            ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
            ->join('one_time_service_payment_histories', 'one_time_services_item_sale.client_id', '=', 'one_time_service_payment_histories.client_id')

            ->join('one_time_services_item', function ($join) {
                $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id');
                $join->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
            })->where('one_time_services_item_sale.id', '=', $id)
            ->first();

        $email = $request->email;

        DB::table('one_time_services_item_sale')
            ->where('id', $id)
            ->update(['comments' => $request->comments, 'is_checked' => $request->is_checked]);
        // Send Email To client
        if ($request->has('send_email')) {
            $validator = Validator::make($request->all(), [
                'comments' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
            Mail::to($email)
                ->send(new ClientMail($client, $request->comments));
        }

        return response()->json(['success' => "Successfully updated the records"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = 'DELETE one_time_services_item_sale,clients,one_time_service_payment_histories
             FROM one_time_services_item_sale
            INNER JOIN clients ON clients.id =one_time_services_item_sale.client_id
            INNER JOIN one_time_service_payment_histories ON one_time_service_payment_histories.client_id =one_time_services_item_sale.client_id
            WHERE one_time_services_item_sale.id = ?';
        DB::delete($query, array($id));
        return response()->json(['success' => "Successfully deleted the records"]);
    }
}
