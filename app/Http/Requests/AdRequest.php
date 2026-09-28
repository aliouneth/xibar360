<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'html_snippet' => 'nullable|string',
            'target_url' => 'nullable|url',
            'zone' => 'required|in:header,sidebar,inline,footer',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }
}
