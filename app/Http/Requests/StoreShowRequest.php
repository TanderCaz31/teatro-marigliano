<?php

namespace App\Http\Requests;

use App\Models\Show;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Show::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'required', 'max:255'],
            'description' => ['string', 'required', 'max:1000'],
            'duration_minutes' => ['integer', 'required', 'numeric', 'max:240', 'min:40'],
            'is_featured' => ['boolean', 'required'],
        ];
    }
}
