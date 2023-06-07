<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * The file under validation must be an base64 image string of (jpeg, png, bmp, gif, svg, or webp)
 */
class Base64Image implements Rule
{
    // Use case example
    // $request->validate([
    //     'base64_photos.*' => [new Base64Image()],
    // ])

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // Check if string is a valid base64 image string
        if (preg_match('/^data:image\/(\w+);base64,/', $value)) {

            try {
                $file_info = \getimagesize($value);
            } catch (\Throwable $th) {
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ['base_64_image' => ':attribute is not a valid base64 image file.'];
    }
}
