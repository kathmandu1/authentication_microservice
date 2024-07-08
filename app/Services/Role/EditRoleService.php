<?php

declare(strict_types=1);

namespace App\Services\Role;

use App\Models\Role;
use App\Services\Validation\AbstractValidator;
use Illuminate\Validation\Rule;

class EditRoleService extends AbstractValidator
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
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->role)],
        ];
    }

    /** @inheritDoc */
    public function save(): bool
    {
        $this->role->name = $this->getChange('name');

        return $this->role->save();
    }
}
