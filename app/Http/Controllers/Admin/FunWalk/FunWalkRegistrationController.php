<?php

namespace App\Http\Controllers\Admin\FunWalk;

use App\Http\Controllers\Controller;
use App\Models\FunWalkRegistration;
use App\Models\FunWalkPayment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class FunWalkRegistrationController extends Controller
{
    /**
     * Display a listing of registrations with DataTable server-side processing
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $registrations = FunWalkRegistration::with(['funWalk', 'payments'])
                ->select(['id', 'fun_walk_id', 'first_name', 'last_name', 'date_of_birth', 'gender', 'email', 'phone', 'ticket_number','created_at', 'updated_at'])
                ->orderBy('created_at', 'desc');
            
            return DataTables::of($registrations)
                ->addColumn('full_name', function($registration) {
                    return $registration->full_name;
                })
                ->addColumn('fun_walk_title', function($registration) {
                    return $registration->funWalk ? $registration->funWalk->title : '-';
                })
                ->addColumn('gender_display', function($registration) {
                    $genderClass = $registration->gender === 'male' ? 'primary' : ($registration->gender === 'female' ? 'info' : 'warning');
                    return '<span class="label label-'.$genderClass.'">'.ucfirst($registration->gender).'</span>';
                })
                ->addColumn('actions', function($registration) {
                    $hasPayment = $registration->payments()->where('status', 'completed')->exists();
                    
                    $buttons = '
                        <button class="btn btn-info btn-sm view-btn" data-id="'.$registration->id.'" title="View Details">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm edit-btn" data-id="'.$registration->id.'" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>';
                    
                    if (!$hasPayment) {
                        $buttons .= '
                        <button class="btn btn-danger btn-sm delete-btn" data-id="'.$registration->id.'" data-ticket="'.$registration->ticket_number.'" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                    } else {
                        $buttons .= '
                        <button class="btn btn-secondary btn-sm" disabled title="Cannot delete - has payments">
                            <i class="fa fa-lock"></i>
                        </button>';
                    }
                    
                    return $buttons;
                })
                ->addColumn('qr', function($registration) {
                    if ($registration->qr_path) {
                        $url = asset($registration->qr_path);
                        return '<img src="' . $url . '" alt="QR" style="max-width:60px; height:auto;" />';
                    }
                    return '-';
                })
                ->editColumn('date_of_birth', function($registration) {
                    return $registration->date_of_birth ? $registration->date_of_birth->format('d M Y') : '-';
                })
                ->editColumn('created_at', function($registration) {
                    return $registration->created_at->format('d M Y H:i');
                })
                ->rawColumns(['gender_display', 'payment_status', 'actions', 'qr'])
                ->make(true);
        }

        return view('admin.fun-walk-registration.index');
    }

    /**
     * Display the specified registration (for view modal)
     */
    public function show($id)
    {
        try {
            $registration = FunWalkRegistration::with(['funWalk', 'payments'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $registration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);
        }
    }

    /**
     * Get registration for editing
     */
    public function edit($id)
    {
        try {
            $registration = FunWalkRegistration::with('funWalk')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $registration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);
        }
    }

    /**
     * Update the specified registration
     */
    public function update(Request $request, $id)
    {
        try {
            $registration = FunWalkRegistration::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'fun_walk_id' => 'required|exists:fun_walks,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before:today',
                'gender' => 'required|in:male,female,other',
                'email' => 'required|email|max:255',
                'phone' => 'required|digits:8',
                'ticket_number' => 'required|string|max:255|unique:fun_walk_registrations,ticket_number,' . $id,
                'qr_path' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $registration->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Registration updated successfully',
                'data' => $registration->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating registration'
            ], 500);
        }
    }

    /**
     * Remove the specified registration
     */
    public function destroy($id)
    {
        try {
            $registration = FunWalkRegistration::findOrFail($id);
            
            // Check if registration has payments
            if ($registration->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete registration with existing payments'
                ], 422);
            }
            
            $ticketNumber = $registration->ticket_number;
            $registration->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Registration {$ticketNumber} deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting registration'
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        try {
            $total = FunWalkRegistration::count();
            $paid = FunWalkRegistration::whereHas('payments', function($query) {
                $query->where('status', 'completed');
            })->count();
            $pending = $total - $paid;
            $revenue = FunWalkPayment::where('status', 'completed')->sum('amount');

            return response()->json([
                'total' => $total,
                'paid' => $paid,
                'pending' => $pending,
                'revenue' => $revenue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'total' => 0,
                'paid' => 0,
                'pending' => 0,
                'revenue' => 0
            ]);
        }
    }
}