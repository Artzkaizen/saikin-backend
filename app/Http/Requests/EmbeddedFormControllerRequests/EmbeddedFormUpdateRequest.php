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
            'custom_short_url' => 'sometimes|required|url|max:255',
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
