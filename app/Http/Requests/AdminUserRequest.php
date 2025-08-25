<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'password' => 'bail|required|string|min:10',
            'username' => 'bail|required|string|max:40',
            'city' => 'bail|required|string|max:255',
            'postal_code' => 'bail|required|string|max:15',
            'country' => 'bail|required|string|max:20',
            'is_admin' => 'bail|boolean',
            'image' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,image/jpeg,image/png,image/jpg,image/gif|max:100000',// vérifie que les éléments sont des images
        ];
    }

    public $validator = null;
    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
}
