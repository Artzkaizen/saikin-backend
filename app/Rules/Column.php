<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * The file under validation must be a string 
 * having matched the given constrains. 
 */
class Column implements Rule
{
    // Use case example
    // $request->validate([
    //     'column' => [new Column('exists=foods')],
    // ]);

    /**
    *  Holds the column constraints
    *
    * @var array
    */
    protected $constraints = [];

    /**
    *  Holds the failed column validations
    *
    * @var array
    */
    protected $message_bag = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $string)
    {
        // Split the string
        $this->constraints = Str::of($string)->explode('|')->mapWithKeys(function ($item) {

            $array = explode('=', $item);
            return [$array[0] => $array[1] ?? ''];

        })->toArray();
    }

    /**
     * Get all values in the message bag
     * 
     * @param integer $key
     * @param array
     */
    public function getFromMessageBag($key)
    {
        return isset($this->message_bag[$key]) ? $this->message_bag[$key] : null;
    }

    /**
     * Append a value to the message bag
     * 
     * @param array $item
     * @return array
     */
    public function appendToMessageBag(array $item)
    {
        return $this->message_bag = array_merge($this->message_bag, $item);
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
        // Get constraints
        $constraints = $this->constraints;

        // Check if column exists
        if (isset($constraints['exists']) && !Schema::hasColumn($constraints['exists'], $value)) {
            $this->appendToMessageBag(['column '.$value.' does not exist in the database.']);
        }

        // Check if message bag is empty
        return empty($this->message_bag)? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ['column' =>  $this->message_bag];
    }
}
