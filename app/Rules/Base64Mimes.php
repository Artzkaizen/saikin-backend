<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * The file under validation must be a base64 image 
 * string, having a mime matching the given constrains. 
 */
class Base64Mimes implements Rule
{
    // Use case example
    // $request->validate([
    //     'base64_photos.*' => [new Base64Mimes('mimes=jpg,png,gif')],
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
        $string = Str::of($string)->explode('=')->toArray();
        $string[1] = Str::of($string[1])->explode(',')->flatten()->toArray();
        $this->supplied_constraints = [$string[0] => $string[1]];
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

        // Get available file mime
        $file_ext = explode('/', mime_content_type($value))[1];

        // Get supplied_constraints
        $constraints = $this->supplied_constraints;

        // Check available user defined constrains
        if (!empty($constraints['mimes']) && !in_array($file_ext, $constraints['mimes'], true) ) {
            $this->appendToMessageBag([':attribute file under validation must match one of the given MIMES : '.implode(", ",$constraints['mimes'])]);
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
        return ['base_64_mimes' =>  $this->getFromMessageBag()];
    }
}
