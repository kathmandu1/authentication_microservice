<?php

declare(strict_types=1);

namespace App\Services\Validation;

abstract class AbstractValidator implements ValidatorInterface
{
    /** @inheritDoc */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get a key from the provided changes array.
     * @param string $key
     * @return mixed
     */
    public function getChange(string $key): mixed
    {
        return $this->changes()[$key] ?? null;
    }

    /**
     * Set a key/value in the provided changes array.
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setChange(mixed $key, mixed $value): self
    {
        if (!empty($this->getChange($key))) {
            $this->changes()[$key] = $value;
        }

        return $this;
    }
}
