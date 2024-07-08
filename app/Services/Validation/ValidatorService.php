<?php

declare(strict_types=1);

namespace App\Services\Validation;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

/**
 * This Service was created to allow easy validation when creating/editing things. This allows additional custom
 * validation to be used when validating things,.
 */
class ValidatorService implements MessageProvider
{
    /** @var array|array of error messages from any rule validation that failed */
    private array $messages = [];

    public function __construct(protected ValidatorInterface $service)
    {
    }

    /**
     * Check if the service passes validation.
     * @return bool
     */
    public function validated(): bool
    {
        $validator = Validator::make(
            $this->service->changes(),
            $this->service->rules(),
            $this->service->messages()
        );

        if ($validator->fails()) {
            $this->messages = $validator->messages()->toArray();
        }

        return empty($this->messages);
    }

    /**
     * Return the messages array as a MessageBag instance - used for when redirecting back to view with error messages.
     * @return MessageBag
     */
    public function getMessageBag(): MessageBag
    {
        return new MessageBag($this->messages);
    }
}
