<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class HistoricalQuotesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'      => 'required|email',
            'start-date' => 'required|date|before_or_equal:end-date|before_or_equal:today|date_format:m/d/Y',
            'end-date'   => 'required|date|after:start-date|before_or_equal:today|date_format:m/d/Y',
            'symbol'     => 'required|string|exists:companies,symbol',
        ];
    }
}
