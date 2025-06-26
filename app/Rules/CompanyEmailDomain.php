<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompanyEmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedDomains = ['@company.com'];
        $domain = '@' . substr(strrchr($value, '@'), 1);

        if (!in_array($domain, $allowedDomains)) {
            $fail('Registration is only allowed with company email addresses (@company.com).');
        }
    }
}
