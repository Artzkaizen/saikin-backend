<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * The field under validation can not be present if another_field is present.
 */
class FailIfPresent implements Rule
{
    // Use case example
    // $request->validate([
    //     'base64_photos.*' => [new FailIfPresent('another_field')],
    // ])

    /**
    *  Holds the name of another field
    *
    * @var string
    */
    protected $another_field = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($field)
    {
        $this->another_field = $field;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $name = $this->another_field;
        return empty(request()->$name)? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ['fail_if_present' => 'The :attribute field and '.$this->another_field.' field can not both be present.'];
    }
}
