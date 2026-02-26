<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Level;
use App\Models\OneTimeService;
use App\Models\OneTimeServicesItem;
use App\Models\ServiceAttribute;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceRegistrationService
{
    // -------------------------------------------------------------------------
    // Service items
    // -------------------------------------------------------------------------

    public function getServiceItemsHtml(string $serviceId): JsonResponse
    {
        if ($serviceId === 'status') {
            return response()->json(['html' => $this->buildStatusCheckHtml()]);
        }

        $financialYear = $this->currentFinancialYear();
        $oneTimeService = OneTimeService::with('OneTimeServicesItem')
            ->whereHas('OneTimeServicesItem', fn($q) => $q->where('financial_year', $financialYear))
            ->findOrFail($serviceId);

        $html = '';
        foreach ($oneTimeService->OneTimeServicesItem as $item) {
            if ($item->financial_year === $financialYear) {
                $html .= $this->buildServiceItemRadioHtml($item, $serviceId);
            }
        }

        return response()->json(['html' => $html]);
    }

    public function getServiceItems(string $serviceId): JsonResponse
    {
        return $this->getServiceItemsHtml($serviceId);
    }

    // -------------------------------------------------------------------------
    // Requirements panels
    // -------------------------------------------------------------------------

    public function getRequirements(Request $request): JsonResponse
    {
        $levels            = Level::where('is_active', 1)->get();
        $sessions          = Session::orderBy('id')->get();
        $serviceAttributes = ServiceAttribute::where(['one_time_service_id' => $request->service])->get();

        return response()->json([
            'attributesHTML'    => view('service.requirements.requirements', compact(
                'serviceAttributes',
                'levels',
                'sessions'
            ))->render(),
            'personalInfoHTML'  => view('service.personal-info.personal-info')->render(),
            'paymentsHTML'      => view('service.payment.payment')->render(),
            'client'            => $request->all(),
            'serviceAttributes' => $serviceAttributes,
        ]);
    }

    // -------------------------------------------------------------------------
    // Status check
    // -------------------------------------------------------------------------

    public function getStatusHtml(string $referenceOrId): JsonResponse
    {
        $sales = DB::table('one_time_services_item_sale')
            ->select(
                'clients.first_name',
                'clients.last_name',
                'clients.email',
                'clients.phone',
                'clients.national_identity',
                'one_time_services_item.name',
                'one_time_services_item_sale.id',
                'one_time_services_item_sale.one_time_services_id',
                'one_time_services_item_sale.price',
                'one_time_services_item_sale.financial_year',
                'one_time_services_item_sale.requirements',
                'one_time_services_item_sale.reference_number',
                'one_time_services_item_sale.is_checked',
                'one_time_services_item_sale.updated_at'
            )
            ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
            ->join('one_time_services_item', function ($join) {
                $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id')
                     ->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
            })
            ->where(fn($q) => $q->where('reference_number', $referenceOrId)
                                ->orWhere('national_identity', $referenceOrId))
            ->get();

        if ($sales->isEmpty()) {
            return response()->json([
                'errors' => ['reference_no' => ['These record do not match our records.']],
                'test'   => $sales,
            ]);
        }

        return response()->json(['status' => $this->buildStatusHtml($sales)]);
    }

    // -------------------------------------------------------------------------
    // Candidate helpers
    // -------------------------------------------------------------------------

    public function findCandidateOrEmpty(string $id): JsonResponse
    {
        $candidate = Candidate::find($id);

        if ($candidate) {
            return response()->json($candidate);
        }

        return response()->json($this->emptyModelObject(new Candidate()));
    }

    // -------------------------------------------------------------------------
    // HTML builders
    // -------------------------------------------------------------------------

    private function buildStatusCheckHtml(): string
    {
        return "
            <div class='form__field'>
                <label for='reference_no'>
                    Reference number
                    <span data-required='true' aria-hidden='true'></span>
                </label>
                <input id='reference_no' type='text' name='reference_no'
                    placeholder='Please Enter reference number' autocomplete='reference_no'>
                <div class='statuses-container mt-2 card'></div>
                <button type='button' class='btn btn-primary btn-lg btn-block' id='check-status'>
                    Check
                </button>
            </div>";
    }

    private function buildServiceItemRadioHtml(OneTimeServicesItem $item, string $serviceId): string
    {
        return "
            <div class='form__radio'>
                <label for='{$item->name}'>
                    {$item->name} (M {$item->price}.00)
                </label>
                <input id='{$item->name}'
                    data-price='{$item->price}'
                    data-service='{$item->name}'
                    data-id='{$serviceId}'
                    name='serviceItem'
                    value='{$item->id}'
                    type='radio' />
            </div>";
    }

    private function buildStatusHtml(Collection $sales): string
    {
        $firstName = strtoupper($sales->first()->first_name);
        $lastName  = strtoupper($sales->first()->last_name);

        $html = "
            <div class='card-header'>$firstName $lastName</div>
            <ul class='list-group list-group-flush'>";

        foreach ($sales as $sale) {
            $progress = $sale->is_checked * 33;
            $date     = date('Y-m-d', strtotime($sale->updated_at));
            $html    .= "
                <a href='#' class='list-group-item list-group-item-action'>{$sale->name}</a>
                <a href='#' class='list-group-item list-group-item-action'>
                    <div class='status-progress-wrap'>
                        <div class='status-progress-bar-wrap'>
                            <div style='width:{$progress}%;' class='status-progress-bar'></div>
                        </div>
                        <div class='status-wrap'>Pending<span> $date</span></div>
                        <div class='status-wrap'>Checked<span> $date</span></div>
                        <div class='status-wrap'>Completed<span> $date</span></div>
                    </div>
                </a>";
        }

        return $html . '</ul>';
    }

    // -------------------------------------------------------------------------
    // Utilities
    // -------------------------------------------------------------------------

    private function currentFinancialYear(): string
    {
        return (date('m') <= 3)
            ? (date('Y') - 1) . '-' . date('Y')
            : date('Y') . '-' . (date('Y') + 1);
    }

    private function emptyModelObject(Model $model): object
    {
        return (object) array_fill_keys(Schema::getColumnListing($model->getTable()), '');
    }
}