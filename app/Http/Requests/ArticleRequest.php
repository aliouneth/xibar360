<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Checked boxes post "on"/"1"; unchecked ones post nothing. Normalise
     * both to booleans so the article flags always save as 0 or 1.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title_fr' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'summary_fr' => 'nullable|string',
            'summary_en' => 'nullable|string',
            'content_fr' => 'required|string',
            'content_en' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'source_url' => 'nullable|url',
            'thumbnail' => 'nullable|image|max:2048',
            'publication_date' => 'required|date',
            'language' => 'required|in:fr,en',
            'source_type' => 'required|in:imported,local',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}
