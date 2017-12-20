<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'address'     => 'required|max:191',
            'description' => 'required',
            'begin'       => 'required|date:format="Y-m-d H:i:s"',
            'end'         => 'required|date:format="Y-m-d H:i:s"',
            'private'     => 'required|numeric',
            'user_id'     => 'required|numeric'
        ];
    }
}
