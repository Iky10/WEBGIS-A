<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * Pada update:
     *   - email: unique kecuali untuk user yang sedang diedit
     *   - password: nullable (kosongkan = tidak ubah password)
     *
     * @return array
     */
    public function rules()
    {
        // Resource binding 'user' (sesuai Route::resource('users'))
        $userId = $this->route('user');

        return [
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            'password' => 'nullable|string|min:6|max:255',
            'role'     => 'required|in:admin,user',
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
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar. Gunakan email lain.',
            'password.min'   => 'Password minimal 6 karakter.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid. Pilih admin atau user.',
        ];
    }
}
