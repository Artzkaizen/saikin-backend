<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PaymentPlan extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'level',
        'payment_plan_benefits',
        'amount',
        'discount',
        'currency',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'payment_plan_benefits' => 'array',
        'amount' => 'double',
        'discount' => 'double',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Establishes a belongs to many relationship with accounts table
     */
    public function accounts()
    {
        return $this->belongsToMany(Account::class);
    }

    /**
     * Establishes a belongs to many relationship with benefits table
     */
    public function benefits()
    {
        return $this->belongsToMany(Benefit::class);
    }
}
