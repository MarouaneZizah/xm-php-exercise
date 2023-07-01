<?php

namespace App\Rules;

use Closure;
use App\Services\NasdaqClient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidSymbol implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string                                        $attribute
     * @param  mixed                                         $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nasdaq_list = (new NasdaqClient())->getListings();
        $symbols     = array_map(fn($item) => $item['symbol'], $nasdaq_list);

        if (!in_array($value, $symbols)) {
            $fail('The ' . $attribute . ' is invalid.');
        }
    }
}
