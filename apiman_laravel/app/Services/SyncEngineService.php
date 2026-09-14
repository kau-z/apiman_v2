<?php

namespace App\Services;

use App\Models\SyncApi;
use App\Models\SyncApiDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncEngineService
{
    /**
     * Fetch general records using dynamic database connection and SQL query
     */
    public function getGeneral(string $fromDb, string $fromQuery): array
    {
        $connection = $this->resolveConnection($fromDb);

        // Check if query contains multiple queries separated by semicolon
        if (!str_contains($fromQuery, ';')) {
            return $connection->select($fromQuery);
        }

        // Multi-query execution
        $pdo = $connection->getPdo();
        $stmt = $pdo->prepare($fromQuery);
        $stmt->execute();

        $data = [];
        do {
            try {
                $rows = $stmt->fetchAll(\PDO::FETCH_OBJ);
                if (!empty($rows)) {
                    $data = $rows;
                }
            } catch (Exception $e) {
                // Ignore cursor errors on non-SELECT statements in multi-queries
            }
        } while ($stmt->nextRowset());

        return $data;
    }

    /**
     * Execute query returning a single scalar value
     */
    public function getSingleResult(string $db, ?string $query)
    {
        if (empty($query)) {
            return null;
        }

        $connection = $this->resolveConnection($db);
        $results = $connection->select($query);

        if (!empty($results)) {
            $firstRow = (array) $results[0];
            return reset($firstRow);
        }

        return null;
    }

    /**
     * Get last sync record for an API by type
     */
    public function getLastSyncDetails(string $apiName, int $type): ?SyncApiDetail
    {
        return SyncApiDetail::whereHas('syncApi', function ($q) use ($apiName) {
            $q->where('api_name', $apiName);
        })
            ->whereIn('status', [1, 9])
            ->where('type', $type)
            ->latest('id')
            ->first();
    }

    /**
     * Insert sync detail log entry
     */
    public function insertSyncApiDetail(
        int $mainId,
        ?string $lastTime,
        ?int $totalCount,
        int $status,
        ?string $remarks,
        int $type
    ): SyncApiDetail {
        $now = Carbon::now('Asia/Colombo');

        return SyncApiDetail::create([
            'main_id' => $mainId,
            'last_time' => $lastTime ? Carbon::parse($lastTime) : $now,
            'total_records' => $totalCount,
            'sync_time' => $now,
            'status' => $status,
            'remarks' => $remarks,
            'type' => $type,
        ]);
    }

    /**
     * Main sync handler (DB to DB, DB to JSON, JSON to DB)
     */
    public function insertGeneral(
        string $apiName,
        SyncApi $apiDetails,
        ?string $lastAddedTime,
        ?string $lastUpdatedTime,
        ?string $lastDeletedTime,
        ?int $totalCount,
        string $addedDate,
        ?array $jsonData = null
    ) {
        $fromDb = $apiDetails->from_db;
        $toDb = $apiDetails->to_db;
        $fromAddedQuery = $apiDetails->from_added_query;
        $toAddedQuery = $apiDetails->to_added_query;
        $mainId = $apiDetails->main_id;
        $prefix = $apiDetails->Prefix ?? '';
        $suffix = $apiDetails->Suffix ?? '';
        $statusToAsyncApiDetail = $apiDetails->status_to_aync_api_detail;
        $waitTillConfirmation = $apiDetails->wait_till_confirmation;

        // Retrieve from-data
        if ($jsonData === null) {
            $fromAddedQuery = str_replace('{{AddedDate}}', "'$addedDate'", $fromAddedQuery);
            $fromData = $this->getGeneral($fromDb, $fromAddedQuery);
        } else {
            $fromData = $jsonData;
        }

        // Case 1: DB to JSON
        if (!empty($fromDb) && empty($toDb)) {
            if (!empty($toAddedQuery)) {
                $finalQuery = '';
                foreach ($fromData as $row) {
                    $rowArray = (array) $row;
                    $singleQuery = $toAddedQuery;
                    foreach ($rowArray as $key => $value) {
                        $sanitized = str_replace(['"', "\n", "\r", "\t"], '', (string) $value);
                        if ($key !== 'id') {
                            $singleQuery = str_replace("{{" . $key . "}}", '"' . $sanitized . '"', $singleQuery);
                        }
                    }
                    $finalQuery .= $singleQuery;
                }

                $finalQuery = rtrim(trim($finalQuery), ',');
                $decoded = json_decode($prefix . $finalQuery . $suffix, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->insertSyncApiDetail($mainId, $lastAddedTime, $totalCount, 0, 'DB TO JSON ' . json_last_error_msg(), 1);
                    return [
                        'status' => false,
                        'message' => 'Error decoding JSON: ' . json_last_error_msg()
                    ];
                }

                if ($statusToAsyncApiDetail == 0) {
                    if ($lastAddedTime) $this->insertSyncApiDetail($mainId, $lastAddedTime, $totalCount, 1, 'DB TO JSON', 1);
                    if ($lastUpdatedTime) $this->insertSyncApiDetail($mainId, $lastUpdatedTime, $totalCount, 1, 'DB TO JSON', 2);
                    if ($lastDeletedTime) $this->insertSyncApiDetail($mainId, $lastDeletedTime, $totalCount, 1, 'DB TO JSON', 3);
                }

                return $decoded;
            }

            // If to_added_query is empty, return raw fromData
            if ($statusToAsyncApiDetail == 0) {
                if ($lastAddedTime) $this->insertSyncApiDetail($mainId, $lastAddedTime, $totalCount, 1, 'DB TO JSON', 1);
                if ($lastUpdatedTime) $this->insertSyncApiDetail($mainId, $lastUpdatedTime, $totalCount, 1, 'DB TO JSON', 2);
                if ($lastDeletedTime) $this->insertSyncApiDetail($mainId, $lastDeletedTime, $totalCount, 1, 'DB TO JSON', 3);
            }

            return $fromData;
        }

        // Case 2: DB to DB or JSON to DB
        $targetConnection = $this->resolveConnection($toDb);
        $pdo = $targetConnection->getPdo();

        $targetConnection->beginTransaction();
        $transError = '';

        try {
            foreach ($fromData as $row) {
                $rowArray = (array) $row;
                $query = $toAddedQuery;

                foreach ($rowArray as $key => $value) {
                    $escaped = $pdo->quote((string) $value);
                    $query = str_replace("{{" . $key . "}}", $escaped, $query);
                }

                $targetConnection->statement($query);
            }

            $targetConnection->commit();

            if ($statusToAsyncApiDetail == 0) {
                if ($lastAddedTime) $this->insertSyncApiDetail($mainId, $lastAddedTime, $totalCount, 1, 'Success', 1);
                if ($lastUpdatedTime) $this->insertSyncApiDetail($mainId, $lastUpdatedTime, $totalCount, 1, 'Success', 2);
                if ($lastDeletedTime) $this->insertSyncApiDetail($mainId, $lastDeletedTime, $totalCount, 1, 'Success', 3);
            }

            return [
                'status' => true,
                'message' => 'success'
            ];
        } catch (Exception $e) {
            $targetConnection->rollBack();
            $transError = $e->getMessage();
            $this->insertSyncApiDetail($mainId, $lastAddedTime, $totalCount, 0, $transError, 1);

            return [
                'status' => false,
                'message' => $transError
            ];
        }
    }

    /**
     * Update general records in target database
     */
    public function updateGeneral(
        SyncApi $apiDetails,
        ?string $lastUpdatedTimeOld,
        ?string $lastAddedTime,
        ?string $lastUpdatedTime,
        ?int $totalCount,
        ?array $data = null,
        ?string $tableName = null
    ): array {
        $toDb = $apiDetails->to_db;
        $toUpdatedQuery = $apiDetails->to_Updated_query;
        $mainId = $apiDetails->main_id;
        $statusToAsyncApiDetail = $apiDetails->status_to_aync_api_detail;

        if (empty($toDb) || empty($toUpdatedQuery)) {
            return ['status' => false, 'message' => 'No target configuration for update'];
        }

        $targetConnection = $this->resolveConnection($toDb);
        $pdo = $targetConnection->getPdo();

        $targetConnection->beginTransaction();
        try {
            if (!empty($data)) {
                foreach ($data as $item) {
                    $itemArray = (array) $item;
                    $query = $toUpdatedQuery;
                    if (!empty($tableName)) {
                        $query = str_replace(['{{tableName}}', '{{table_name}}'], $tableName, $query);
                    }
                    foreach ($itemArray as $key => $val) {
                        $escaped = $pdo->quote((string) $val);
                        $query = str_replace("{{" . $key . "}}", $escaped, $query);
                    }
                    $targetConnection->statement($query);
                }
            }

            $targetConnection->commit();

            if ($statusToAsyncApiDetail == 0) {
                $this->insertSyncApiDetail($mainId, $lastUpdatedTime, $totalCount, 1, 'Updated', 2);
            }

            return ['status' => true, 'message' => 'updated successfully'];
        } catch (Exception $e) {
            $targetConnection->rollBack();
            $this->insertSyncApiDetail($mainId, $lastUpdatedTime, $totalCount, 0, $e->getMessage(), 2);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete general records in target database
     */
    public function deleteGeneral(
        string $apiName,
        SyncApi $apiDetails,
        ?string $lastDeletedTime,
        ?string $lastDeletedTimeOld,
        ?array $data = null
    ): array {
        $toDb = $apiDetails->to_db;
        $toDeleteQuery = $apiDetails->to_delete_query;
        $mainId = $apiDetails->main_id;

        if (empty($toDb) || empty($toDeleteQuery)) {
            return ['status' => false, 'message' => 'No target delete configuration'];
        }

        $targetConnection = $this->resolveConnection($toDb);
        $pdo = $targetConnection->getPdo();

        $targetConnection->beginTransaction();
        try {
            if (!empty($data)) {
                foreach ($data as $item) {
                    $itemArray = (array) $item;
                    $query = $toDeleteQuery;
                    foreach ($itemArray as $key => $val) {
                        $escaped = $pdo->quote((string) $val);
                        $query = str_replace("{{" . $key . "}}", $escaped, $query);
                    }
                    $targetConnection->statement($query);
                }
            }

            $targetConnection->commit();
            $this->insertSyncApiDetail($mainId, $lastDeletedTime, null, 1, 'Deleted', 3);

            return ['status' => true, 'message' => 'deleted successfully'];
        } catch (Exception $e) {
            $targetConnection->rollBack();
            $this->insertSyncApiDetail($mainId, $lastDeletedTime, null, 0, $e->getMessage(), 3);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Helper to resolve dynamic database connections
     */
    protected function resolveConnection(?string $name)
    {
        if (empty($name) || $name === 'default') {
            return DB::connection();
        }

        try {
            return DB::connection($name);
        } catch (Exception $e) {
            // Fallback to default if named connection doesn't exist
            Log::warning("Database connection [{$name}] not configured, falling back to default.");
            return DB::connection();
        }
    }
}
