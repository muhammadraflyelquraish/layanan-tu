<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'no_identity' => ['required', 'string', 'max:255'],
            'old_password' => ['string', 'nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults(), 'required_with:old_password'],
        ];
    }
}
