<?php

namespace App\Http\Requests\EmbeddedFormControllerRequests;

use App\Models\EmbeddedForm;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmbeddedFormUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied contact by id
        $this->embedded_form = EmbeddedForm::find($this->input('id'));

        if ($this->embedded_form) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of update_embedded_form
             */
            if (auth()->user()->isAbleTo('update_embedded_form', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found contact
             */
            if ($this->embedded_form->user_id === auth()->user()->id){
                return true;
            }

            return false;

        } else {

            // Return failure
            throw new NotFoundHttpException();
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1',
            'group_id' => 'sometimes|required|integer|min:1',
            'title' => 'sometimes|required|string|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => 'An id is required',
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1',

            'title.sometimes' => 'A title field should be present, else entirely exclude the field',
            'title.required' => 'A title field maybe required',
            'title.string'  => 'Title field characters are not valid',
            'title.max'  => 'Title characters can not be more than 100',

            'group_id.sometimes' => 'A group id should be present, else entirely exclude the field',
            'group_id.required' => 'A group id maybe required',
            'group_id.integer'  => 'Group id characters are not valid, Integer is expected',
            'group_id.min'  => 'Group id characters can not be less than 1',
        ];
    }
}
