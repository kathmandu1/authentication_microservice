<?php

declare(strict_types=1);

namespace App\Repositories;

use Closure;
use DateTime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;

abstract class AbstractRepository
{
    public static string $modelClass = Model::class;
    public ReflectionClass $model;
    protected Builder $query;

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->model = new ReflectionClass(static::$modelClass);
        $this->query();
    }

    public function query(): self
    {
        /* @var Builder query */
        $this->query = $this->model->getMethod('query')->invoke(null);

        return $this;
    }

    /**
     * @return Collection
     * @throws ReflectionException
     */
    public function all(): Collection
    {
        try {
            return $this->model->getMethod('all')->invoke(null);
        } catch (ReflectionException $e) {
            Log::channel('merodiscount')->error($e->getMessage());

            return new Collection();
        }
    }

    public function count($columns = '*'): int
    {
        return $this->query->count($columns);
    }

    public function createdAfter(DateTime $dateTime): self
    {
        $this->query->where('created_at', '>', $dateTime);

        return $this;
    }

    public function createdBefore(DateTime $dateTime): self
    {
        $this->query->where('created_at', '<', $dateTime);

        return $this;
    }

    public function first(): ?Model
    {
        return $this->query->first();
    }

    public function get($columns = ['*']): Collection
    {
        return $this->query->get($columns);
    }

    public function delete(): int
    {
        return $this->query->delete();
    }

    public function exists(): bool
    {
        return $this->query->exists();
    }

    public function id(int $id): self
    {
        $this->query->where('id', $id);

        return $this;
    }

    public function ids(array $ids): self
    {
        $this->query->whereIn('id', $ids);

        return $this;
    }

    public function lastId(): self
    {
        $this->query->orderBy('id')
            ->limit(1);

        return $this;
    }

    public function limit(int $total): self
    {
        $this->query->limit($total);

        return $this;
    }

    public function name(string $name): self
    {
        $this->query->where('name', $name);

        return $this;
    }

    public function orderBy(string $key): self
    {
        $this->query->orderBy($key);

        return $this;
    }

    public function orderByDesc(string $key): self
    {
        $this->query->orderByDesc($key);

        return $this;
    }

    public function orderByRaw(string $key): self
    {
        $this->query->orderByRaw($key);

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->query->where('slug', $slug);

        return $this;
    }

    public function updatedAfter(DateTime $dateTime): self
    {
        $this->query->whereDate('updated_at', '>', $dateTime);

        return $this;
    }

    public function updatedBefore(DateTime $dateTime): self
    {
        $this->query->whereDate('updated_at', '<', $dateTime);

        return $this;
    }

    public function uuid(string $uuid): self
    {
        $this->query->where('uuid', $uuid);

        return $this;
    }

    public function where(string $key, $param2, $param3 = null): self
    {
        if (empty($param3)) {
            $this->query->where($key, $param2);
        } else {
            $this->query->where($key, $param2, $param3);
        }

        return $this;
    }

    public function whereDate(string $key, string $param2, string $param3 = null): self
    {
        if (empty($param3)) {
            $this->query->whereDate($key, $param2);
        } else {
            $this->query->whereDate($key, $param2, $param3);
        }

        return $this;
    }

    public function whereNull(array|string $columns): self
    {
        $this->query->whereNull($columns);

        return $this;
    }

    public function whereNotNull(array|string $columns): self
    {
        $this->query->whereNotNull($columns);

        return $this;
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator
    {
        return $this->query->paginate($perPage, $columns, $pageName, $page);
    }

    public function with($relations = [], $callback = null): self
    {
        $this->query->with($relations, $callback);

        return $this;
    }

    public function select(...$columns): self
    {
        $this->query->select($columns);

        return $this;
    }

    public function whereHas($relations, Closure $callback = null): self
    {
        $this->query->whereHas($relations, $callback);

        return $this;
    }

    public function withCount($relations): self
    {
        $this->query->withCount($relations);

        return $this;
    }

    public function distinct($column = null): self
    {
        $this->query->distinct($column);

        return $this;
    }

    public function groupBy(...$groups): self
    {
        $this->query->groupBy($groups);

        return $this;
    }

    public function selectRaw($expression, array $bindings = []): self
    {
        $this->query->selectRaw($expression, $bindings);

        return $this;
    }

    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null): self
    {
        $this->query->has($relation, $operator, $count, $boolean, $callback);

        return $this;
    }
}
