<?php

declare(strict_types=1);

namespace App\Services\Validation;

interface ValidatorInterface
{
    /**
     * Renaming the error message default by laravel.
     * @return array
     */
    public function messages(): array;

    /**
     * This is the input changes from the form.
     * @return array
     */
    public function changes(): array;

    /**
     * This is the rules to be provided to the validator.
     * @return array
     */
    public function rules(): array;
}
