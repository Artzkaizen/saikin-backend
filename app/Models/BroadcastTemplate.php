<?php

namespace App\Models;

use App\Casts\BaseUrlArrayCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BroadcastTemplate extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'title',
        'message',
        'preview_phone',
        'contact_start_date',
        'contact_end_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at', 'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'pictures' => BaseUrlArrayCast::class,
        'videos' => BaseUrlArrayCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Set the attribute's title.
     *
     * @param string $value
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $count=0;
        $title = Str::of($value)->explode(' ')->takeUntil(function ($string) use (&$count) {
            $count = $count + strlen($string) +1;
            return $count > 50;
        });

        $this->attributes['title'] = (string) Str::of($title->join(' '))->trim()->lower()->limit(50);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->created_by = auth()->user()->id;
        });

        self::updating(function ($model) {
            $model->updated_by = auth()->user()->id;
        });

        self::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });
    }

    /**
     * Establishes a belongs to relationship with users table
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Establishes a belongs to relationship with accounts table
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}