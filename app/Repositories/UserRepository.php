<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\VendorSearchRequestData;
use App\Enums\UserType;
use App\Models\User;
use App\Repositories\Pipelines\QueryFilters\CategoryFilter;
use App\Repositories\Pipelines\QueryFilters\PriceFilter;
use App\Repositories\Pipelines\QueryFilters\RatingFilter;
use App\Repositories\Pipelines\QueryFilters\ServiceFilter;
use App\Repositories\Pipelines\QueryFilters\SortFilterPip;
use Illuminate\Pipeline\Pipeline;

class UserRepository extends AbstractRepository
{
    public static string $modelClass = User::class;

    public function findByEmail($email)
    {
        return $this->query()->where('email', $email)->first();
    }

    public function findByPhone($phone)
    {
        return $this->query()->where('phone', $phone)->first();
    }

    private function getUserWithRelations($query)
    {
        return $query->with(['media', 'roles']);
    }

    public function searchUsers($search = [])
    {
        $query = User::query();

        $search['name']
            ? $this->applyUserNameFilter($query, $search['name'])
            : null;

        return $this->getUserWithRelations($query);
    }

    private function applyUserNameFilter($query, $userName)
    {
        $query->where('name', 'like', '%' . $userName . '%');
    }
}
