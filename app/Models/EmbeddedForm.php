<?php

namespace App\Models;

use App\Casts\BaseUrlArrayCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmbeddedForm extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'group_id',
        'custom_short_url',
        'description',
        'input_fields',
        'form_header_text',
        'form_header_image',
        'form_footer_text',
        'form_footer_image',
        'form_background_color',
        'form_width',
        'form_border_radius',
        'submit_button_color',
        'submit_button_text',
        'submit_button_text_color',
        'submit_button_text_before',
        'submit_button_text_after',
        'thank_you_message',
        'thank_you_message_url',
        'facebook_pix_cel_code',
        'auto_responder_id',
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
        'input_fields' => 'array',
        'form_header_image' => BaseUrlArrayCast::class,
        'form_footer_image' => BaseUrlArrayCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
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
     * Establishes a belongs to relationship with groups table
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}