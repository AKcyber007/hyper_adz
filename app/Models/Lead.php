<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'lead_code',
        'lead_type',
        'name',
        'company_name',
        'phone',
        'email',
        'message',
        'source',
        'status',
        'assigned_admin_id',
        'remarks',
        'approved_at',
        'rejected_at',
        'converted_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    /**
     * Cache array to keep track of registered dispatchers to prevent duplicate bindings.
     */
    protected static array $dispatcherListenersRegistered = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        static::registerCreatingListenerOnce();
    }

    /**
     * Register creating and created event listeners on active event dispatcher.
     */
    protected static function registerCreatingListenerOnce(): void
    {
        $dispatcher = static::getEventDispatcher();
        if (!$dispatcher || $dispatcher instanceof \Illuminate\Events\NullDispatcher) {
            $dispatcher = app('events');
            if ($dispatcher) {
                static::setEventDispatcher($dispatcher);
            }
        }

        if ($dispatcher) {
            $hash = spl_object_hash($dispatcher);
            if (empty(static::$dispatcherListenersRegistered[$hash])) {
                static::creating(function ($lead) {
                    // Auto generate UUID
                    if (empty($lead->uuid)) {
                        $lead->uuid = (string) Str::uuid();
                    }

                    // Generate temporary unique lead code on creation
                    if (empty($lead->lead_code)) {
                        $lead->lead_code = 'LEAD-TEMP-' . strtoupper(Str::random(6));
                    }
                });

                static::created(function ($lead) {
                    // Refine unique sequential code based on ID
                    if (str_starts_with($lead->lead_code, 'LEAD-TEMP-')) {
                        $lead->lead_code = 'LEAD-' . str_pad($lead->id, 5, '0', STR_PAD_LEFT);
                        $lead->saveQuietly();
                    }
                });

                static::$dispatcherListenersRegistered[$hash] = true;
            }
        }
    }

    /**
     * Get the admin user assigned to this lead.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    /**
     * Mutator to automatically normalize phone numbers on set.
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = User::normalizePhone($value);
    }
}
