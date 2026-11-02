<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Accept "on"/"1" from the checkbox and turn it into a real boolean.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('enabled')) {
            $this->merge(['enabled' => $this->boolean('enabled')]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048', Rule::unique('sources', 'url')->ignore($this->route('source'))],
            'language' => ['required', Rule::in(['fr', 'en'])],
            'user_agent' => ['nullable', 'string', 'max:500'],
            'enabled' => ['sometimes', 'boolean'],
        ];
    }
}
