<?php

namespace App\Http\Requests;

use App\Models\Performance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Performance::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'show_id' => ['required', 'integer', 'exists:shows,id'],
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'starts_at' => ['required', 'date', 'after:now'],
        ];
    }
}
