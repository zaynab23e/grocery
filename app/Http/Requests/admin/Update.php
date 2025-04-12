<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'nullable|string',
            'email'   => 'nullable|email|unique:admins,email,' . auth('admin')->id(),
            'phone'   => 'nullable|string|unique:admins,phone,' . auth('admin')->id(),
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8',
            
        ];
    }
}
