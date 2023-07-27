<?php

namespace App\Http\Requests\GroupControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class GroupStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:50|min:1',
            'contacts' => 'required|array|max:9|min:1',
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
            'title.required' => 'A group title is required',
            'title.string'  => 'Group title characters are not valid',
            'title.max'  => 'Group title characters can not be more than 50',
            'title.min'  => 'Group title characters can not be less than 1',

            'contacts.required' => 'Group contacts field is required',
            'contacts.array'  => 'Group contacts field characters are not valid, Array is expected',
            'contacts.max'  => 'Group contacts field array can not contain more than 9 items',
            'contacts.min'  => 'Group contacts field array can not have less than 1 items',
        ];
    }
}
