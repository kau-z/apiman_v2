<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Business;
use App\Models\Client;
use App\Models\ExpenseCategory;
use App\Models\Leave;
use App\Models\Payable;
use App\Models\PaymentApplication;
use App\Models\SubContractor;
use App\Models\SyncApi;
use App\Models\VehicleCost;
use App\Services\SyncEngineService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DatalayerController extends Controller
{
    protected SyncEngineService $syncEngine;

    public function __construct(SyncEngineService $syncEngine)
    {
        $this->syncEngine = $syncEngine;
    }

    /**
     * Test function for magiclight
     */
    public function magiclight(Request $request): JsonResponse
    {
        $data = $request->json()->all() ?: $request->all();

        return response()->json([
            'user name' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
        ], Response::HTTP_CREATED);
    }

    /**
     * General sync endpoint (DB to DB, DB to JSON, JSON to DB)
     */
    public function general(Request $request)
    {
        $payload = $request->json()->all() ?: $request->all();

        $apiName = $payload['apiName'] ?? ($payload['header']['apiName'] ?? null);

        if (empty($apiName)) {
            return response()->json(['error' => 'API Name required'], Response::HTTP_BAD_REQUEST);
        }

        $syncApi = SyncApi::where('api_name', $apiName)->first();
        if (!$syncApi) {
            return response()->json(['error' => "API '{$apiName}' not configured"], Response::HTTP_NOT_FOUND);
        }

        $lastAddedDetail = $this->syncEngine->getLastSyncDetails($apiName, 1);
        $lastUpdatedDetail = $this->syncEngine->getLastSyncDetails($apiName, 2);
        $lastDeletedDetail = $this->syncEngine->getLastSyncDetails($apiName, 3);

        $lastAddedTimeOld = $lastAddedDetail?->last_time?->toDateTimeString();
        $totalCountOld = $lastAddedDetail?->total_records;
        $lastUpdatedTimeOld = $lastUpdatedDetail?->last_time?->toDateTimeString();
        $lastDeletedTimeOld = $lastDeletedDetail?->last_time?->toDateTimeString();

        if (!empty($syncApi->from_db)) {
            $lastAddedTime = $this->syncEngine->getSingleResult($syncApi->from_db, $syncApi->last_added_time_query) ?: ($lastAddedTimeOld ?? Carbon::now('Asia/Colombo')->format('Y-m-d H:i:s'));
            $lastUpdatedTime = $this->syncEngine->getSingleResult($syncApi->from_db, $syncApi->last_updated_time_query) ?: ($lastUpdatedTimeOld ?? $lastAddedTime);
            $totalCount = $this->syncEngine->getSingleResult($syncApi->from_db, $syncApi->total_count_query) ?? $totalCountOld;
            $lastDeletedTime = $this->syncEngine->getSingleResult($syncApi->from_db, $syncApi->last_deleted_time_query) ?: ($lastDeletedTimeOld ?? Carbon::now('Asia/Colombo')->format('Y-m-d H:i:s'));
        } else {
            // JSON to DB mode
            $parseHeaderDate = function ($rawDate) {
                if ($rawDate === null || trim((string) $rawDate) === '') {
                    return Carbon::now('Asia/Colombo')->format('Y-m-d H:i:s');
                }
                $rawDate = trim((string) $rawDate);
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) {
                    return $rawDate . ' 00:00:00';
                }
                try {
                    return Carbon::parse($rawDate, 'Asia/Colombo')->format('Y-m-d H:i:s');
                } catch (Exception $e) {
                    return $rawDate;
                }
            };

            $parseHeaderCount = function ($rawCount) {
                if ($rawCount === null || trim((string) $rawCount) === '') {
                    return null;
                }
                return (int) $rawCount;
            };

            $lastAddedTime = $parseHeaderDate($payload['header']['last_added_time'] ?? null);
            $lastUpdatedTime = $parseHeaderDate($payload['header']['last_updated_time'] ?? null);
            $totalCount = $parseHeaderCount($payload['header']['total_count'] ?? null);
            $lastDeletedTime = $parseHeaderDate($payload['header']['last_deleted_time'] ?? null);
        }

        $tableName = $payload['header']['tableName'] ?? null;

        // Check if existing records have been synced before
        if ($lastAddedTimeOld !== null) {
            $lastUpdatedTimeOld = $lastUpdatedTimeOld ?? $lastUpdatedTime;
            $lastDeletedTimeOld = $lastDeletedTimeOld ?? $lastDeletedTime;

            if (
                $lastAddedTimeOld == $lastAddedTime &&
                $lastUpdatedTimeOld == $lastUpdatedTime &&
                $totalCountOld == $totalCount &&
                $lastDeletedTimeOld == $lastDeletedTime
            ) {
                return response()->json('you are up to date', Response::HTTP_OK);
            }

            // Incremental Insert
            if ($lastAddedTime > $lastAddedTimeOld && (!empty($syncApi->from_added_query) || !empty($syncApi->to_added_query))) {
                if (!empty($payload['data'])) {
                    $result = $this->syncEngine->insertGeneral($apiName, $syncApi, $lastAddedTime, '', '', $totalCount, $lastAddedTimeOld, $payload['data']);
                    return response()->json($result);
                } elseif (!empty($syncApi->from_added_query)) {
                    $result = $this->syncEngine->insertGeneral($apiName, $syncApi, $lastAddedTime, '', '', $totalCount, $lastAddedTimeOld);
                    return response()->json($result);
                } else {
                    return response()->json('JSON data cannot be blank', Response::HTTP_BAD_REQUEST);
                }
            }

            // Incremental Delete
            if ($lastDeletedTime > $lastDeletedTimeOld && (!empty($syncApi->from_delete_query) || !empty($syncApi->to_delete_query))) {
                $dataToDelete = $payload['data'] ?? null;
                $result = $this->syncEngine->deleteGeneral($apiName, $syncApi, $lastDeletedTime, $lastDeletedTimeOld, $dataToDelete);
                return response()->json($result);
            }

            // Incremental Update
            if ($lastUpdatedTime > $lastUpdatedTimeOld && (!empty($syncApi->from_Updated_query) || !empty($syncApi->to_Updated_query))) {
                $dataToUpdate = $payload['data'] ?? null;
                $result = $this->syncEngine->updateGeneral($syncApi, $lastUpdatedTimeOld, $lastAddedTime, $lastUpdatedTime, $totalCount, $dataToUpdate, $tableName);
                return response()->json($result);
            }
        } else {
            // First time sync
            if (!empty($payload['data'])) {
                if (!empty($syncApi->to_added_query)) {
                    $result = $this->syncEngine->insertGeneral($apiName, $syncApi, $lastAddedTime, $lastUpdatedTime, $lastDeletedTime, $totalCount, '1970-01-01', $payload['data']);
                    return response()->json($result);
                } elseif (!empty($syncApi->to_Updated_query)) {
                    $result = $this->syncEngine->updateGeneral($syncApi, $lastUpdatedTimeOld, $lastAddedTime, $lastUpdatedTime, $totalCount, $payload['data'], $tableName);
                    return response()->json($result);
                }
            } elseif (!empty($syncApi->from_added_query) && !empty($syncApi->from_db)) {
                $result = $this->syncEngine->insertGeneral($apiName, $syncApi, $lastAddedTime, $lastUpdatedTime, $lastDeletedTime, $totalCount, '1970-01-01');
                return response()->json($result);
            } else {
                return response()->json('JSON data cannot be blank', Response::HTTP_BAD_REQUEST);
            }
        }

        return response()->json(['status' => true, 'message' => 'Processed']);
    }

    /**
     * Subcontractor registration
     */
    public function subcontractor(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'Sub_Contractor_Code' => 'required|integer|unique:acc_sub_contractor,Sub_Contractor_Code',
            'Sub_Contractor_Name' => 'required|string',
            'Sub_Contractor_Address' => 'required|string|max:200',
            'Attn_Person' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $username = $request->getUser() ?: $request->server('PHP_AUTH_USER', 'API');

        $subcontractor = SubContractor::create([
            'Sub_Contractor_Code' => $payload['Sub_Contractor_Code'],
            'Sub_Contractor_Name' => $payload['Sub_Contractor_Name'],
            'Sub_Contractor_Address' => $payload['Sub_Contractor_Address'],
            'Land_Phone_No' => $payload['Land_Phone_No'] ?? null,
            'Mobile' => $payload['Mobile'] ?? null,
            'Fax' => $payload['Fax'] ?? null,
            'add_by' => $username,
            'NBT' => $payload['NBT'] ?? null,
            'Is_VAT_Registered' => $payload['Is_VAT_Registered'] ?? null,
            'Registation_No' => $payload['Registation_No'] ?? null,
            'VAT_For_Transport' => $payload['VAT_For_Transport'] ?? null,
            'Attn_Person' => $payload['Attn_Person'],
        ]);

        return response()->json([
            'sub_contractor_id' => $subcontractor->id,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Client registration
     */
    public function clientreg(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'Client_Id' => 'required|unique:ma_client,Client_Id',
            'Client_Name' => 'required|string|unique:ma_client,Client_Name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $username = $request->getUser() ?: $request->server('PHP_AUTH_USER', 'API');

        $client = Client::create([
            'Client_Id' => $payload['Client_Id'],
            'Client_Name' => $payload['Client_Name'],
            'Official_Contact_Dtl' => $payload['Official_Contact_Dtl'] ?? null,
            'Project_Contact_Dtl' => $payload['Project_Contact_Dtl'] ?? null,
            'Contact_Person_Name' => $payload['Contact_Person_Name'] ?? null,
            'Contact_Person_Email' => $payload['Contact_Person_Email'] ?? null,
            'Contact_Person_Phone' => $payload['Contact_Person_Phone'] ?? null,
            'Contact_Person_Mobile' => $payload['Contact_Person_Mobile'] ?? null,
            'Added_By' => $username,
            'Client_Address' => $payload['Client_Address'] ?? null,
            'Vat_No' => $payload['Vat_No'] ?? null,
        ]);

        return response()->json([
            'Client_Code' => $client->Client_Code,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Client billing
     */
    public function clientbill(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'bill_date' => 'required',
            'Client_ID_PMS' => 'required',
            'amount' => 'required|numeric',
            'retention_amount' => 'required|numeric',
            'Project_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $client = Client::where('Client_Id', $payload['Client_ID_PMS'])->first();
        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Client not registered. Please register client first.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $project = Business::where('PMS_project_id', $payload['Project_id'])->first();
        if (!$project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not registered. Please register the project first.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $username = $request->getUser() ?: $request->server('PHP_AUTH_USER', 'API');

        $bill = PaymentApplication::create([
            'bill_date' => $payload['bill_date'],
            'Client_Code' => $client->Client_Code,
            'Client_ID_PMS' => $payload['Client_ID_PMS'],
            'particulars' => $payload['particulars'] ?? null,
            'amount' => $payload['amount'],
            'retention_amount' => $payload['retention_amount'],
            'invoice_status' => 'active',
            'added_by' => $username,
            'business_id' => $project->project_id,
        ]);

        return response()->json([
            'bill_id' => $bill->bill_id,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Accounts payable registration
     */
    public function payable(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'due_date' => 'required',
            'pms_project_id' => 'required',
            'payee_name' => 'required',
            'payable_amount' => 'required|numeric',
            'expense_category' => 'required',
            'period_from' => 'required',
            'period_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $project = Business::where('PMS_project_id', $payload['pms_project_id'])->first();
        if (!$project) {
            return response()->json([
                'status' => false,
                'message' => 'Project not registered. Please register the project first.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $username = $request->getUser() ?: $request->server('PHP_AUTH_USER', 'API');

        $payable = Payable::create([
            'due_date' => $payload['due_date'],
            'project_id' => $project->project_id,
            'payee_id' => $payload['payee_name'],
            'payable_amount' => $payload['payable_amount'],
            'description' => $payload['payable_description'] ?? null,
            'expense_category' => $payload['expense_category'],
            'period_from' => $payload['period_from'],
            'period_to' => $payload['period_to'],
            'added_by' => $username,
            'payable_status' => 'Not Paid',
        ]);

        return response()->json([
            'payable_id' => $payable->payable_id,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Project registration
     */
    public function projectRegister(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'project_name' => 'required|string|unique:acc_business,business_name',
            'PMS_project_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $project = Business::create([
            'business_name' => $payload['project_name'],
            'status' => 1,
            'PMS_project_id' => $payload['PMS_project_id'],
        ]);

        return response()->json([
            'acc_project_id' => $project->project_id,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Get expense categories
     */
    public function expenseCategories(): JsonResponse
    {
        $categories = ExpenseCategory::whereNull('parent_id')->orWhere('parent_id', 0)->get();

        if ($categories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No data available'
            ], Response::HTTP_BAD_REQUEST);
        }

        $response = $categories->map(function ($cat) {
            return [
                'exp_id' => $cat->exp_id,
                'expense_category' => $cat->expense_category,
            ];
        });

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Get expense sub categories
     */
    public function expenseSubCategories($exp_id): JsonResponse
    {
        $subCategories = ExpenseCategory::where('parent_id', $exp_id)->get();

        if ($subCategories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No data available'
            ], Response::HTTP_BAD_REQUEST);
        }

        $response = $subCategories->map(function ($cat) {
            return [
                'exp_sub_id' => $cat->exp_id,
                'expense_sub_category' => $cat->expense_category,
            ];
        });

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Daily attendance record
     */
    public function dailyAttendance(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'Project_Code' => 'required|string',
            'Employee_Code' => 'required|string',
            'Date' => 'required|date_format:Y-m-d',
            'Time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $now = Carbon::now('Asia/Colombo');

        $attendance = Attendance::create([
            'project_code' => $payload['Project_Code'],
            'Employee_Code' => $payload['Employee_Code'],
            'Date' => $payload['Date'],
            'Time' => $payload['Time'],
            'Machine' => $payload['Machine'] ?? null,
            'Location' => $payload['Location'] ?? null,
            'Row' => $payload['Row'] ?? null,
            'UpdatedDate' => $now,
        ]);

        return response()->json([
            'id' => $attendance->id,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Vehicle cost record
     */
    public function vehicleCost(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'vehicle_no' => 'required|string',
            'cost_category' => 'required|string',
            'amount' => 'required|numeric',
            'cost_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $cost = VehicleCost::create([
            'vehicle_no' => $payload['vehicle_no'],
            'cost_category' => $payload['cost_category'],
            'amount' => $payload['amount'],
            'cost_date' => $payload['cost_date'],
            'description' => $payload['description'] ?? null,
        ]);

        return response()->json([
            'id' => $cost->id,
            'message' => 'record added successfully.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Apply leave
     */
    public function applyLeave(Request $request): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        $validator = Validator::make($payload, [
            'Employee_No' => 'required|string',
            'leave_type' => 'required|string',
            'leave_date' => 'required|date_format:Y-m-d',
            'leave_apply_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => implode(' ', $validator->errors()->all())
            ], Response::HTTP_BAD_REQUEST);
        }

        $employeeNo = $payload['Employee_No'];
        $leaveDate = $payload['leave_date'];
        $toDate = $payload['To_date'] ?? null;
        $leaveType = $payload['leave_type'];
        $applyType = $payload['leave_apply_type'];
        $remarks = $payload['Remarks'] ?? null;
        $cancel = !empty($payload['Cancel']) ? 1 : 0;

        $datesToProcess = [$leaveDate];

        if (!empty($toDate) && $toDate !== $leaveDate) {
            $start = Carbon::parse($leaveDate);
            $end = Carbon::parse($toDate);

            if ($start->gt($end)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Dates (Date1 > Date2) found!'
                ], Response::HTTP_BAD_REQUEST);
            }

            $period = CarbonPeriod::create($start, $end);
            $datesToProcess = [];
            foreach ($period as $date) {
                $datesToProcess[] = $date->format('Y-m-d');
            }
        }

        $now = Carbon::now('Asia/Colombo');
        $lastId = null;

        foreach ($datesToProcess as $singleDate) {
            // Delete existing leave for this employee & date
            Leave::where('Employee_No', $employeeNo)
                ->where('leave_date', $singleDate)
                ->delete();

            if ($cancel === 0) {
                $created = Leave::create([
                    'Employee_No' => $employeeNo,
                    'leave_type' => $leaveType,
                    'leave_date' => $singleDate,
                    'leave_apply_type' => $applyType,
                    'Remarks' => $remarks,
                    'mark_type' => 1,
                    'added_by' => 'API',
                    'added_date' => $now,
                ]);
                $lastId = $created->id;
            }
        }

        return response()->json([
            'id' => $lastId,
            'message' => 'record processed successfully.'
        ], Response::HTTP_CREATED);
    }
}
