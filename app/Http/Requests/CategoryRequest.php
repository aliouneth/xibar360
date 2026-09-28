<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_fr' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug',
            'icon' => 'nullable|string|max:50',
        ];
    }
}
