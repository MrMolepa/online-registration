<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Center;
use App\Models\OneTimeService;
use App\Models\ServiceAttribute;
use App\Services\PaymentService;
use App\Services\ServiceRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private ServiceRegistrationService $registrationService,
    ) {}

    // -------------------------------------------------------------------------
    // Views
    // -------------------------------------------------------------------------

    

    public function index()
    {
        $services = OneTimeService::with('oneTimeServicesItem')
            ->where('status', 1)
            ->get();

        return view('service.services', compact('services'));
    }

    // -------------------------------------------------------------------------
    // Service selection
    // -------------------------------------------------------------------------

    public function getOneTimeServicesItem(Request $request): JsonResponse
    {
        return $this->registrationService->getServiceItemsHtml($request->service);
    }

    public function serviceRequirements(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['service' => 'required']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        return $this->registrationService->getRequirements($request);
    }

    public function getItems(Request $request): JsonResponse
    {
        $request->validate(['service' => 'required']);

        return $this->registrationService->getServiceItems($request->service);
    }

    public function getRequirements(Request $request): JsonResponse
    {
        return $this->registrationService->getRequirements($request);
    }

    // -------------------------------------------------------------------------
    // Payment
    // -------------------------------------------------------------------------

    public function paymentTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required|exists:one_time_services,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_identity' => 'required|digits:12',
            'phone' => 'required|string|max:8',
            'email' => 'required|email',
            'serviceItem' => 'required|exists:one_time_services_item,id',
            'total_sale_price' => 'required|numeric|min:0',
            'mpesa_mobile' => 'required_if:payment,VclMpesa|digits:8',
            'ecocash_mobile' => 'required_if:payment,EcoCash|digits:8',
            'deposit_proof' => 'required_if:payment,BankDeposit|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'deposit_date' => 'required_if:payment,BankDeposit|date',
            'deposit_reference' => 'required_if:payment,BankDeposit|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return match ($request->payment) {
            'CreditCard' => $this->paymentService->processCreditCard($request),
            'VclMpesa'   => $this->paymentService->processMpesa($request),
            'EcoCash'    => $this->paymentService->processEcoCash($request),
            default      => response()->json(['error' => 'Invalid payment method'], 400),
        };
    }

    // -------------------------------------------------------------------------
    // Status check
    // -------------------------------------------------------------------------

    public function checkStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['reference_no' => 'required']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        return $this->registrationService->getStatusHtml($request->reference_no);
    }

    // -------------------------------------------------------------------------
    // Candidate
    // -------------------------------------------------------------------------

    public function searchCandidate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['search' => 'required']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        return $this->registrationService->findCandidateOrEmpty($request->search);
    }

    public function validCandidate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => ['required', 'exists:candidates,candidate_no'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $candidate = Candidate::find($request->candidate_no);
        if (!$candidate) {
            return response()->json(['errors' => 'Candidate number is Invalid'], 401);
        }

        return response()->json($candidate, 200);
    }

    // -------------------------------------------------------------------------
    // Centre autocomplete
    // -------------------------------------------------------------------------

    public function autocompleteAllCentersSearch(Request $request): JsonResponse
    {
        if (!$request->has('q')) {
            return response()->json([]);
        }

        $centers = Center::where('center_name', 'LIKE', $request->get('q') . '%')
            ->limit(5)
            ->get();

        return response()->json($centers);
    }

    public function searchCenter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['candidate_no' => 'required']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        return response()->json(
            Schema::getColumnListing((new Candidate())->getTable())
        );
    }

    // -------------------------------------------------------------------------
    // Multi-step form validation
    // -------------------------------------------------------------------------

    public function multiform(Request $request): JsonResponse
    {
        $currentPage = 2;

        $rules = [
            1 => [
                'first_name' => ['required'],
                'last_name'  => ['required'],
                'phone_no'   => ['required'],
                'email'      => ['required', 'email'],
                'address'    => ['required'],
            ],
            2 => [],
        ];

        foreach (ServiceAttribute::where(['one_time_service_id' => 3])->get() as $attr) {
            $rules[2][$attr->code] = ['required'];
        }

        $validator = Validator::make($request->all(), $rules[$currentPage]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        return response()->json(['success' => true]);
    }
}