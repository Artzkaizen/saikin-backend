<?php

namespace App\Http\Requests\EmbeddedFormControllerRequests;

use App\Models\EmbeddedForm;
use App\Rules\Maximum;
use App\Rules\Base64Image;
use App\Rules\UrlImage;
use Illuminate\Foundation\Http\FormRequest;

class EmbeddedFormStoreRequest extends FormRequest
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
            'group_id' => 'required|integer|min:1',
            'title' => 'required|string|max:100',
            'custom_short_url' => 'sometimes|required|url|max:100',
            'description' => 'sometimes|required|string|max:16777215',

            'input_fields' => 'required|array',
            'input_fields' => 'sometimes|required|array|max:18|min:1',
            'input_fields.*' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',

            'input_fields.first_name' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.first_name.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.first_name.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.last_name' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.last_name.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.last_name.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.whatsapp_number' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.whatsapp_number.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.whatsapp_number.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.email_address' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.email_address.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.email_address.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.house_address' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.house_address.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.house_address.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.*' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.*.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.*.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'form_header_text' => 'sometimes|required|string|max:255',

            'form_header_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_header_base64_photos','form_header_url_photos')],
            'form_header_photos.*' => 'required_unless:form_header_photos,'.null.'|image|dimensions:min_width=200,min_height=200|mimes:jpg,jpeg,png,gif,bmp|max:1999',

            'form_header_base64_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_header_photos','form_header_url_photos')],
            'form_header_base64_photos.*' => ['required_unless:form_header_base64_photos,'.null, new Base64Image(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_header_url_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_header_photos','form_header_base64_photos')],
            'form_header_url_photos.*' => ['required_unless:form_header_url_photos,'.null, new UrlImage(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_footer_text' => 'sometimes|required|string|max:255',

            'form_footer_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_footer_base64_photos','form_footer_url_photos')],
            'form_footer_photos.*' => 'required_unless:form_footer_photos,'.null.'|image|dimensions:min_width=200,min_height=200|mimes:jpg,jpeg,png,gif,bmp|max:1999',

            'form_footer_base64_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_footer_photos','form_footer_url_photos')],
            'form_footer_base64_photos.*' => ['required_unless:form_footer_base64_photos,'.null, new Base64Image(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_footer_url_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_footer_photos','form_footer_base64_photos')],
            'form_footer_url_photos.*' => ['required_unless:form_footer_url_photos,'.null, new UrlImage(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_background_color' => ['required','string','regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i','max:20'],
            'form_width' => 'required|in:small,normal,large|max:30',
            'form_border_radius' => 'sometimes|required|integer|max:10|min:1',
            'submit_button_color' => ['required','string','regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i','max:20'],
            'submit_button_text' => 'sometimes|required|string|max:100|min:1',
            'submit_button_text_color' => ['required','string','regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i','max:20'],
            'submit_button_text_before' => 'sometimes|required|string|max:100|min:1',
            'submit_button_text_after' => 'sometimes|required|string|max:100|min:1',
            'thank_you_message' => 'sometimes|required|string|max:255|min:1',
            'thank_you_message_url' => 'sometimes|required|url|max:255|min:1',
            'facebook_pix_cel_code' => 'sometimes|required|string|max:100|min:1',
            'auto_responder_id' => 'sometimes|required|integer|min:1',
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
            'group_id.required' => 'A group id is required',
            'group_id.integer'  => 'Group id characters are not valid, Integer is expected',
            'group_id.min'  => 'Group id characters can not be less than 1',

            'title.required' => 'A title field is required',
            'title.string'  => 'Title field characters are not valid',
            'title.max'  => 'Title characters can not be more than 100',

            'custom_short_url.sometimes' => 'A custom short url field should be present, else entirely exclude the field',
            'custom_short_url.required' => 'A custom short url field maybe required',
            'custom_short_url.url'  => 'Custom short url field characters are not valid, Url is expected',
            'custom_short_url.max'  => 'Custom short url characters can not be more than 100',

            'description.sometimes' => 'A description field should be present, else entirely exclude the field',
            'description.required' => 'A description field maybe required',
            'description.string'  => 'Description field characters are not valid, String is expected',
            'description.max'  => 'Description characters can not be more than 16777215',

            'input_fields.required' => 'Input fields are required',
            'input_fields.array'  => 'Input fields characters are not valid, Array is expected',
            'input_fields.max'  => 'Input fields array can not contain more than 18 items',
            'input_fields.min'  => 'Input fields array can not have less than 1 item',

            'input_fields.first_name.required_unless' => 'First name field should be present, else entirely exclude the field',
            'input_fields.first_name.array'  => 'First name field is not valid, Array is expected',
            'input_fields.first_name.max'  => 'First name field can not be more than 2 items',
            'input_fields.first_name.min'  => 'First name field can not be less than 2 items',
            'input_fields.first_name.is_required.required_unless' => 'First name (is_required) field should be present, else entirely exclude the field',
            'input_fields.first_name.is_required.boolean'  => 'First name (is_required) field is not valid, Boolean is expected',
            'input_fields.first_name.is_shown.required_unless' => 'First name (is_shown) field should be present, else entirely exclude the field',
            'input_fields.first_name.is_shown.string'  => 'First name (is_shown) field is not valid, Boolean is expected',

            'input_fields.last_name.required_unless' => 'Last name field should be present, else entirely exclude the field',
            'input_fields.last_name.array'  => 'Last name field is not valid, Array is expected',
            'input_fields.last_name.max'  => 'Last name field can not be more than 2 items',
            'input_fields.last_name.min'  => 'Last name field can not be less than 2 items',
            'input_fields.last_name.is_required.required_unless' => 'Last name (is_required) field should be present, else entirely exclude the field',
            'input_fields.last_name.is_required.boolean'  => 'Last name (is_required) field is not valid, Boolean is expected',
            'input_fields.last_name.is_shown.required_unless' => 'Last name (is_shown) field should be present, else entirely exclude the field',
            'input_fields.last_name.is_shown.string'  => 'Last name (is_shown) field is not valid, Boolean is expected',

            'input_fields.whatsapp_number.required_unless' => 'Whatsapp number field should be present, else entirely exclude the field',
            'input_fields.whatsapp_number.array'  => 'Whatsapp number field is not valid, Array is expected',
            'input_fields.whatsapp_number.max'  => 'Whatsapp number field can not be more than 2 items',
            'input_fields.whatsapp_number.min'  => 'Whatsapp number field can not be less than 2 items',
            'input_fields.whatsapp_number.is_required.required_unless' => 'Whatsapp number (is_required) field should be present, else entirely exclude the field',
            'input_fields.whatsapp_number.is_required.boolean'  => 'Whatsapp number (is_required) field is not valid, Boolean is expected',
            'input_fields.whatsapp_number.is_shown.required_unless' => 'Whatsapp number (is_shown) field should be present, else entirely exclude the field',
            'input_fields.whatsapp_number.is_shown.string'  => 'Whatsapp number (is_shown) field is not valid, Boolean is expected',

            'input_fields.email_address.required_unless' => 'Email address field should be present, else entirely exclude the field',
            'input_fields.email_address.array'  => 'Email address field is not valid, Array is expected',
            'input_fields.email_address.max'  => 'Email address field can not be more than 2 items',
            'input_fields.email_address.min'  => 'Email address field can not be less than 2 items',
            'input_fields.email_address.is_required.required_unless' => 'Email address (is_required) field should be present, else entirely exclude the field',
            'input_fields.email_address.is_required.boolean'  => 'Email address (is_required) field is not valid, Boolean is expected',
            'input_fields.email_address.is_shown.required_unless' => 'Email address (is_shown) field should be present, else entirely exclude the field',
            'input_fields.email_address.is_shown.string'  => 'Email address (is_shown) field is not valid, Boolean is expected',

            'input_fields.house_address.required_unless' => 'House address field should be present, else entirely exclude the field',
            'input_fields.house_address.array'  => 'House address field is not valid, Array is expected',
            'input_fields.house_address.max'  => 'House address field can not be more than 2 items',
            'input_fields.house_address.min'  => 'House address field can not be less than 2 items',
            'input_fields.house_address.is_required.required_unless' => 'House address (is_required) field should be present, else entirely exclude the field',
            'input_fields.house_address.is_required.boolean'  => 'House address (is_required) field is not valid, Boolean is expected',
            'input_fields.house_address.is_shown.required_unless' => 'House address (is_shown) field should be present, else entirely exclude the field',
            'input_fields.house_address.is_shown.string'  => 'House address (is_shown) field is not valid, Boolean is expected',

            'input_fields.*.required_unless' => ':attribute field should be present, else entirely exclude the field',
            'input_fields.*.array'  => ':attribute field is not valid, Array is expected',
            'input_fields.*.max'  => ':attribute field can not be more than 2 items',
            'input_fields.*.min'  => ':attribute field can not be less than 2 items',
            'input_fields.*.is_required.required_unless' => ':attribute (is_required) field should be present, else entirely exclude the field',
            'input_fields.*.is_required.boolean'  => ':attribute (is_required) field is not valid, Boolean is expected',
            'input_fields.*.is_shown.required_unless' => ':attribute (is_shown) field should be present, else entirely exclude the field',
            'input_fields.*.is_shown.string'  => ':attribute (is_shown) field is not valid, Boolean is expected',

            'form_header_text.sometimes' => 'A form header text field should be present, else entirely exclude the field',
            'form_header_text.required' => 'A form header text field maybe required',
            'form_header_text.string'  => 'Form header text field characters are not valid, Url is expected',
            'form_header_text.max'  => 'Form header text characters can not be more than 255',
        ];
    }
}
