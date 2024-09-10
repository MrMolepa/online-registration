<?php

use App\Models\Candidate;
use App\Models\CenterCandidate;
use App\Models\FeeStracture;
use App\Models\Publication;
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
    $invoices = DB::table('invoices')
        ->where('client_id', '=', $candidate_no)
        ->where('national_id', '=', $national_id)
        ->where('level', '=', $level)
        ->where('session', '=', $session)
        ->where('financial_year', '=', $financial_year)
        ->first();
    return isset($invoices) ? true : false;
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
    $invoices = DB::table('invoices')
        ->where('client_id', '=', $center_candidate->candidate_no)
        ->where('national_id', '=', $center_candidate->national_id)
        ->where('level', '=', $center_candidate->level)
        ->where('session', '=', $center_candidate->session)
        ->where('financial_year', '=', $center_candidate->financial_year)
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







function is_sponsored($candidate_id, $sponsor)
{
    $sponsored = DB::table('request_action')
        ->select('actions.action_type', 'actions.order_number', 'action_types.status', 'action_types.name', 'action_types.label_color')
        ->join('requests', 'requests.id', '=', 'request_action.request_id')
        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
        ->join('processes', 'processes.id', '=', 'transitions.process')
        ->join('actions', 'actions.id', '=', 'request_action.action_id')
        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
        ->where('requests.request_data_id', '=', $candidate_id)
        ->where('requests.request_data', '=', CenterCandidate::class)
        ->where('processes.process_key', '=',  $sponsor)
        ->where('request_action.is_active', '=', 0)
        ->where('request_action.is_complete', '=', 1);
    return isset($sponsored) ? true : false;
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

function initials($full_names)
{
    preg_match('/(?:\w+\. )?(\w+).*?(\w+)(?: \w+\.)?$/', $full_names, $result);
    return strtoupper($result[1][0] . $result[2][0]);
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
