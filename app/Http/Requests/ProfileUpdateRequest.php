<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'username'   => ['sometimes', 'nullable', 'string', 'max:50', 'alpha_dash', Rule::unique(User::class)->ignore($this->user()->user_id, 'user_id')],
            'email'      => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->user_id, 'user_id'),
            ],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:500'],
        ];
    }
}
