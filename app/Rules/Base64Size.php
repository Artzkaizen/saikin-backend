<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * The file under validation must be a base64 image 
 * string, having a size matching the given constrains. 
 * The size corresponds to the file size in kilobytes.
 */
class Base64Size implements Rule
{
    // Use case example
    // $request->validate([
    //     'base64_photos.*' => [new Base64Size('min=13,max=15')],
    // ]);

    /**
    *  Holds the base64 size constraints
    *
    * @var array
    */
    protected $supplied_constraints = [];

    /**
    *  Holds the base64 failed validations
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
     * @param string  $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Clear message bag
        $this->clearMessageBag();

        // Check if string is a valid base64 image string
        if (!preg_match('/^data:image\/(\w+);base64,/', $value)) {
           
            $this->appendToMessageBag([':attribute is not a valid base64 image file.']);
            return false;
        }

        try {
            $file_info = \getimagesize($value);
        } catch (\Throwable $th) {
            $this->appendToMessageBag([':attribute is not a valid base64 image file.']);
            return false;
        }

        // Get available file size in kilobytes
        $file_size =  (int) (strlen(rtrim($value, '=')) * 3 / 4)/1024; // size in kilobytes

        // Get supplied_constraints
        $constraints = $this->supplied_constraints;

        // Check available user defined constrains
        if (isset($constraints['min']) && ( $file_size < (int) $constraints['min'] )) {
            $this->appendToMessageBag([':attribute file size can not be less than '.$constraints['min'].'kb.']);
        }

        if (isset($constraints['max']) && ($file_size > (int) $constraints['max'] )) {
            $this->appendToMessageBag([':attribute file size can not be more than '.$constraints['max'].'kb.']);
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
        return ['base_64_size' =>  $this->getFromMessageBag()];
    }
}
