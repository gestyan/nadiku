<?php

namespace App\Models;

use App\Enums\LetterType;
use App\Enums\Config as ConfigEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Letter extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'reference_number',
        'disposition_number',
        'from',
        'to',
        'letter_date',
        'received_date',
        'description',
        'note',
        'type',
        'classification_code',
        'user_id',
        'send_status',
        'esign_status',
        'cc',
        'status',
        'number',
      	'satker',
      	'to_email',
      	'bcc',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'letter_date' => 'date',
        'received_date' => 'date',
    ];

    protected $appends = [
        'formatted_letter_date',
        'formatted_received_date',
        'formatted_created_at',
        'formatted_updated_at',
    ];

    public function getFormattedLetterDateAttribute(): string
    {
        return Carbon::parse($this->letter_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function getFormattedReceivedDateAttribute(): string
    {
        return Carbon::parse($this->received_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->isoFormat('dddd, D MMMM YYYY, HH:mm:ss');
    }

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at->isoFormat('dddd, D MMMM YYYY, HH:mm:ss');
    }

    public function getFormattedDueDateAttribute(): string
    {
        return Carbon::parse($this->due_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function scopeType($query, LetterType $type)
    {
        return $query->where('type', $type->type());
    }

    public function scopeIncoming($query)
    {
        return $this->scopeType($query, LetterType::INCOMING);
    }

    public function scopeOutgoing($query)
    {
        return $this->scopeType($query, LetterType::OUTGOING);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', now()->addDays(-1));
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query
                ->where('reference_number', $find)
                ->orWhere('disposition_number', $find)
                ->orWhere('from', 'LIKE', $find . '%')
                ->orWhere('to', 'LIKE', $find . '%');
        });
    }
  
  	public function scopeFilter($query, $esign, $since, $until, $to)
    {

        // dd(is_null($esign));
        return $query
            ->when(!is_null($esign), function ($query, $condition) use ($esign, $since, $until, $to) {
                return $query->where('esign_status', $esign)
                    ->when($since && $until, function ($query, $condition) use ($since, $until) {
                        $query->whereBetween(DB::raw('DATE(' . 'letter_date' . ')'), [$since, $until]);
                    })->when($to, function ($query, $condition) use ($to) {
                        $query->whereIn('user_id', $to);
                    });
            })->when(is_null($esign) || $since || $until || $to, function ($query, $condition) use ($since, $until, $to) {
                return $query->when($since && $until, function ($query, $condition) use ($since, $until, $to) {
                    $query->whereBetween(DB::raw('DATE(' . 'letter_date' . ')'), [$since, $until]);
                })->when($to, function ($query, $condition) use ($to) {
                    $query->whereIn('user_id', $to);
                });;
            });

    }

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }

    public function scopeRender($query, $search)
    {
        if (auth()->user()->role == 'staff') {
            return $query
                ->with(['attachments', 'classification'])
                ->where('user_id', '=', auth()->user()->id)
                ->search($search)
                ->latest('letter_date')
                ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
                ->appends([
                    'search' => $search,
                ]);
        } else {
            return $query
                ->with(['attachments', 'classification'])
                ->search($search)
                ->orderBy('disposition_number', 'DESC')
                ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
                ->appends([
                    'search' => $search,
                ]);
        }
    }

    public function scopeAgenda($query, $since, $until, $filter)
    {
        return $query
            ->when($since && $until && $filter, function ($query, $condition) use ($since, $until, $filter) {
                return $query->whereBetween(DB::raw('DATE(' . $filter . ')'), [$since, $until]);
            });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class, 'classification_code', 'code');
    }

    /**
     * @return HasMany
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'letter_id', 'id')->where('dispositions.user_id', '=', auth()->user()->id);
    }
  
  	public function allDispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'letter_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'letter_id', 'id');
    }
  
    public function attachments_esign(): HasMany
    {
        return $this->hasMany(Attachment::class, 'letter_id', 'id')->where('attachments.esign_file', '=', 1)->where('attachments.esign_status', '<>', 1);
    }

    public function attachments_other(): HasMany
    {
        return $this->hasMany(Attachment::class, 'letter_id', 'id')->where('attachments.esign_file', '<>', 1);
    }


    public function first_attachment(): HasMany
    {
        return $this->hasOne(Attachment::class, 'letter_id', 'id')->oldestOfMany();
    }

    public function status_(): BelongsTo
    {
        return $this->belongsTo(LetterStatus::class, 'letter_status', 'id');
    }

    public function disposition_from_name(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disposition_from', 'id');
    }
  
  	public function satkers(): BelongsTo
    {
        return $this->belongsTo(Satker::class, 'satker', 'kode');
    }
  
  	public function dispositions_names(): HasMany
    {
        return $this->hasMany(Disposition::class, 'letter_id', 'id')->where('dispositions.user_id', '=', $this->disposition_from);
    }

    public function dispositionNames()
    {
        return $this->dispositions_names->pluck('disposition_to_name')->pluck('name');
    }
}
