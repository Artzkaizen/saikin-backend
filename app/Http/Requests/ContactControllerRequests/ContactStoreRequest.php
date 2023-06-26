<?php

namespace App\Http\Requests\ContactControllerRequests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactStoreRequest extends FormRequest
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
            'title' => 'required|string|min:4|max:50',
            'name' => 'required|string|max:100',
            'email' => 'sometimes|required|email|max:100',
            'phone' => 'required|numeric|digits_between:1,25',
            'address' => 'required|string|max:250|min:1',
            'city' => 'required|string|max:50|min:1',
            'state' => 'required|string|max:50|min:1',
            'country' => 'required|string|max:50|min:1',
            'zip' => 'sometimes|required|string|max:10|min:1',
            'latitude' => 'sometimes|required|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'sometimes|required|numeric|between:-180,180|required_with:latitude',
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
            'title.required' => 'A title field is required',
            'title.string'  => 'Title field characters are not valid',
            'title.max'  => 'Title characters can not be more than 50',
            'title.min'  => 'Title characters can not be less than 4',

            'name.required' => 'A name is required',
            'name.string' => 'Name characters are not valid',
            'name.max' => 'A name can not have more than 100 characters',

            'email.sometimes' => 'An email field should be present, else entirely exclude the field',
            'email.required' => 'An email is required',
            'email.email' => 'Email is not valid',
            'email.max' => 'An email can not have more than 100 characters',

            'phone.required' => 'A phone number is required',
            'phone.numeric' => 'Phone number characters are not valid',
            'phone.digits_between' => 'A phone number can not have more than 25 characters or less than 1',

            'address.required' => 'An address is required',
            'address.string'  => 'address characters are not valid',
            'address.max'  => 'address characters can not be more than 250',
            'address.min'  => 'address characters can not be less than 1',

            'city.required' => 'A city field is required',
            'city.string'  => 'City field characters are not valid',
            'city.max'  => 'City field characters can not be more than 50',
            'city.min'  => 'City field characters can not be less than 1',

            'state.required' => 'A state field is required',
            'state.string'  => 'State field characters are not valid',
            'state.max'  => 'State field characters can not be more than 50',
            'state.min'  => 'State field characters can not be less than 1',

            'country.required' => 'A country field is required',
            'country.string'  => 'Country field characters are not valid',
            'country.max'  => 'Country field characters can not be more than 50',
            'country.min'  => 'Country field characters can not be less than 1',

            'zip.sometimes' => 'A zip field should be present, else entirely exclude the field',
            'zip.required' => 'A zip field maybe required',
            'zip.string'  => 'Zip field characters are not valid',
            'zip.max'  => 'Zip field characters can not be more than 10',
            'zip.min'  => 'Zip field characters can not be less than 1',

            'latitude.sometimes' => 'latitude field should be present, else entirely exclude the field',
            'latitude.required' => 'latitude field maybe required',
            'latitude.string'  => 'latitude field characters are not valid, integer is expected',
            'latitude.between' => 'latitude must be between -90 and 90 degrees',
            'latitude.required_with' => 'latitude must have a longitude field',

            'longitude.sometimes' => 'longitude field should be present, else entirely exclude the field',
            'longitude.required' => 'longitude field maybe required',
            'longitude.string'  => 'longitude field characters are not valid, integer is expected',
            'longitude.between' => 'longitude must be between -180 and 180 degrees',
            'longitude.required_with' => 'longitude must have a latitude field',
        ];
    }
}
