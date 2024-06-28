<?php

namespace Celysium\Helper\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Mobile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!isMobile($value)) {
            $fail(__('validation.invalid'));
        }
    }
}
