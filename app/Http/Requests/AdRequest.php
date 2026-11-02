<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise the "Active" checkbox: "on"/"1" becomes true, an absent
     * field becomes false, so the tinyint column never receives "on".
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'html_snippet' => 'nullable|string',
            'target_url' => 'nullable|url',
            'zone' => 'required|in:header_left,header_center,header_right,sidebar,inline,footer,wrapper',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
}
