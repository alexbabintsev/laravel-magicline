<?php

namespace AlexBabintsev\Magicline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class MagiclineLog extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('magicline.logging.database.table', 'magicline_logs');
    }

    protected $fillable = [
        'resource_type',
        'resource_id',
        'action',
        'request_data',
        'response_data',
        'status',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'synced_at' => 'datetime',
    ];

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PENDING = 'pending';

    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_LIST = 'list';
    public const ACTION_FIND = 'find';
    public const ACTION_SYNC = 'sync';

    /**
     * Log a successful API operation
     */
    public static function logSuccess(
        string $resourceType,
        string $action,
        ?string $resourceId = null,
        ?array $requestData = null,
        ?array $responseData = null
    ): self {
        return self::create([
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'status' => self::STATUS_SUCCESS,
            'synced_at' => now(),
        ]);
    }

    /**
     * Log a failed API operation
     */
    public static function logError(
        string $resourceType,
        string $action,
        string $errorMessage,
        ?string $resourceId = null,
        ?array $requestData = null,
        ?array $responseData = null
    ): self {
        return self::create([
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'synced_at' => now(),
        ]);
    }

    /**
     * Log a pending operation
     */
    public static function logPending(
        string $resourceType,
        string $action,
        ?string $resourceId = null,
        ?array $requestData = null
    ): self {
        return self::create([
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'request_data' => $requestData,
            'status' => self::STATUS_PENDING,
            'synced_at' => now(),
        ]);
    }

    /**
     * Update log entry with response data
     */
    public function updateWithResponse(array $responseData, bool $success = true): self
    {
        $this->update([
            'response_data' => $responseData,
            'status' => $success ? self::STATUS_SUCCESS : self::STATUS_FAILED,
        ]);

        return $this;
    }

    /**
     * Update log entry with error
     */
    public function updateWithError(string $errorMessage, ?array $responseData = null): self
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'response_data' => $responseData,
        ]);

        return $this;
    }

    /**
     * Check if operation was successful
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if operation failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if operation is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get logs for specific resource type
     */
    public function scopeForResource(Builder $query, string $resourceType): Builder
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Get logs for specific resource ID
     */
    public function scopeForResourceId(Builder $query, string $resourceId): Builder
    {
        return $query->where('resource_id', $resourceId);
    }

    /**
     * Get logs with specific status
     */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Get logs for specific action
     */
    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Get successful operations
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Get failed operations
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Get pending operations
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Get logs within date range
     */
    public function scopeInDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('synced_at', [$startDate, $endDate]);
    }

    /**
     * Get recent logs
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('synced_at', '>=', now()->subHours($hours));
    }

    /**
     * Get statistics for resource type
     */
    public static function getStats(?string $resourceType = null, int $days = 7): array
    {
        $query = self::query();

        if ($resourceType) {
            $query->forResource($resourceType);
        }

        $query->inDateRange(now()->subDays($days), now());

        $total = $query->count();
        $successful = $query->clone()->successful()->count();
        $failed = $query->clone()->failed()->count();
        $pending = $query->clone()->pending()->count();

        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $failed,
            'pending' => $pending,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
        ];
    }
}
