<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * The file under validation must be a base64 image string meeting 
 * the dimension constraints as specified by the rule's parameters.
 * Available constraints are: min_width, max_width, min_height, 
 * max_height, width, height, ratio
 * 
 */
class Base64Dimensions implements Rule
{
    // Use case example
    // $request->validate([
    //     'base64_photos.*' => [new Base64Dimensions('min_width=1000,min_height=2000,ratio=2/1')],
    // ]);

    /**
    *  Holds the base64 dimensions constraints
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

        // Get available file information
        $width = (int) $file_info[0];
        $height = (int) $file_info[1];
        $ratio = $width / $height;

        // Get supplied_constraints
        $constraints = $this->supplied_constraints;

        // Evaluate ratio constrain
        if (isset($constraints['ratio'])) {
            $constraints['evaluated_ratio'] = eval('return '.$constraints['ratio'].';');
        }

        // Check available user defined constrains
        if (isset($constraints['width']) && ((int) $constraints['width'] !== $width )) {
            $this->appendToMessageBag([':attribute width is not equal to '.$constraints['width'].'px.']);
        }

        if (isset($constraints['height']) && ((int) $constraints['height'] !== $height )) {
            $this->appendToMessageBag([':attribute height is not equal to '.$constraints['height'].'px.']);
        }

        if (isset($constraints['min_width']) && ($width < (int) $constraints['min_width'] )) {
            $this->appendToMessageBag([':attribute minimum required width is '.$constraints['min_width'].'px.']);
        }

        if (isset($constraints['min_height']) && ($height < (int) $constraints['min_height'] )) {
            $this->appendToMessageBag([':attribute minimum required height is '.$constraints['min_height'].'px.']);
        }

        if (isset($constraints['max_width']) && ($width > (int) $constraints['max_width'] )) {
            $this->appendToMessageBag([':attribute maximum required width is '.$constraints['max_width'].'px.']);
        }

        if (isset($constraints['max_height']) && ($height > (int) $constraints['max_height'] )) {
            $this->appendToMessageBag([':attribute maximum required height is '.$constraints['max_height'].'px.']);
        }

        if (isset($constraints['ratio']) && ($ratio !== $constraints['evaluated_ratio']) ) {
            $this->appendToMessageBag([':attribute required ratio is '.$constraints['ratio'].' (width/height).']);
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
        return ['base_64_dimensions' =>  $this->getFromMessageBag()];
    }
}
