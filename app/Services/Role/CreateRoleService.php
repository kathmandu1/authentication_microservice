<?php

declare(strict_types=1);

namespace App\Services\Role;

use App\Models\Role;
use App\Services\Validation\AbstractValidator;

class CreateRoleService extends AbstractValidator
{
    public function __construct(protected Role $role, protected array $changes)
    {
    }

    /** @inheritDoc */
    public function changes(): array
    {
        return $this->changes;
    }

    /** @inheritDoc */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
        ];
    }

    /** @inheritDoc */
    public function save(): bool
    {
        $this->role->name = $this->getChange('name');

        return $this->role->save();
    }
}
