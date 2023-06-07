<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * Check the difference between two fields with valid date time
 * and ensure it is not greater than a given range.
 * 
 * Please note that strtotime was used for date string validation
 * which means that strings of 'now','next Thursday','last Monday'
 * etc will be validate as valid date time strings.
 * 
 * If you wish to avoid this, additional validation is recommend
 * for the fields under validation.
 */
class DateDifference implements Rule
{
    // Use case example
    // $request->validate([
    //     'start_date' => [new DateDifference('compared_with=end_date,max_difference=5 months')],
    //     'end_date' => [new DateDifference('compared_with=start_date,max_difference=5 months')],
    // ]);

    /**
    *  Holds the date difference constraints
    *
    * @var array
    */
    protected $supplied_constraints = [];

    /**
    *  Holds the date difference failed validations
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
        $this->supplied_constraints = Str::of($string)->explode(',')->mapWithKeys(function ($item, $key) {

            $debris = \explode('=', $item);
            return [$debris[0] => $debris[1]];

        })->toArray();
    }

    /**
     * Get all values in the message bag
     * 
     * @param void|integer $var
     * @param array $var
     */
    public function getFromMessageBag($var = null)
    {
        if (!is_integer($var)) {
            return $this->message_bag;
        }

        return $this->message_bag[$var];
    }

    /**
     * Append a value to the message bag
     * 
     * @param array $var
     * @return array
     */
    public function appendToMessageBag(array $var)
    {
        $messages = $this->getFromMessageBag();
        return $this->message_bag = array_merge($messages,$var);
    }

    /**
     * Clear the message bag
     * 
     * @param void
     * @return void
     */
    public function clearMessageBag()
    {
        $this->message_bag = [];
        return;
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
        // Clear message bag
        $this->clearMessageBag();

        // Get supplied_constraints
        $constraints = $this->supplied_constraints;
        $compared_with = $constraints['compared_with'];

        // Check available user defined constrains
        if (!$value || !is_string($value) || !strtotime($value)) {
            $this->appendToMessageBag([':attribute field is not a valid date string.']);
        }

        if (!isset($constraints['compared_with'])) {
            $this->appendToMessageBag([':attribute field has no comparison field.']);
        }

        if (isset($constraints['compared_with']) && (!is_string(request()->$compared_with) || !strtotime(request()->$compared_with))) {
            $this->appendToMessageBag([Str::of($constraints['compared_with'])->replaceMatches('/_/',' ').' field is not a valid date string.']);
        }

        if (!isset($constraints['max_difference'])) {
            $this->appendToMessageBag([':attribute requires a maximum difference for comparison.']);
        }

        if (isset($constraints['max_difference']) && !strtotime($constraints['max_difference'])) {
            $this->appendToMessageBag([':attribute stated maximum difference is not a valid date string.']);
        }

        // Check if they are errors and return them if any
        if (!empty($this->getFromMessageBag())) {
            return false;
        }

        // Convert date time strings in to carbon date time strings
        $date = Carbon::parse($value);
        $compared_with = Carbon::parse(request()->$compared_with);
        $max_difference = Carbon::now()->diffInDays(Carbon::parse($constraints['max_difference']));
        $total_days_in_range = $date->diffInDays($compared_with);

        // Check if the total days between the two dates is greater than the given maximum difference
        if ($total_days_in_range > $max_difference) {
            $this->appendToMessageBag(['The difference between :attribute field and '.Str::of($constraints['compared_with'])->replaceMatches('/_/',' ').' field is greater than the stated maximum range of '.$constraints['max_difference']]);
        }

        // Check if message bag is empty
        return empty($this->getFromMessageBag())? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ['date_difference' =>  $this->getFromMessageBag()];
    }
}
