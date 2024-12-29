<?php

namespace App\Repositories\Eloquent;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repositories\EloquentRepositoryInterface;
use App\Contracts\EloquentRepositoryFilter;

/**
 * The base class for all eloquent repositories
 */
class EloquentRepository implements EloquentRepositoryInterface
{
	/**
	 * An empty instance of the model related to this repository
	 * @var \Illuminate\Database\Eloquent\Model $model
	 */
	protected $model;

	/**
	 * Eloquent filter class
	 * @var \App\Contracts\EloquentRepositoryFilter
	 */
	protected $filter;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	/**
	 * Get multiple rows from database
	 * 
	 * @param  array $filters
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 *         | \Illuminate\Database\Eloquent\Collection
	 */
	public function getList(
		array $params = [],
	): object {
		if (! empty($this->filter)) {
			$query = $this->filter->setQuery($this->model)->apply($params);
		} else {
			$query = $this->model->query();
		}

		$relations = $params['relations'] ?? [];
		$counts = $params['counts'] ?? [];

		$query->with($relations)->withCount($counts);

		if (isset($params['perPage'])) {
			$perPage = (int) ($params['perPage'] ?? $this->perPage ?? 10);
			if ($perPage > 50) {
				$perPage = 50;
			}

			return $query->paginate($perPage);
		} else {
			return $query->when(isset($params['limit']), function($q) use ($params) {
				$limit = (int) $params['limit'];
				$limit = $limit <= 25 ? $limit : 25;
				$q->take((int) $limit);
			})->get();
		}
	}

	/**
	 * Get all records
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getAll(): Collection
	{
		return $this->model->all();
	}

	/**
	 * Get single row from database
	 * 
	 * @param  int|array    $id
	 * 
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function getOne(int|array $id, array $relations = []): ?Model
	{
		if (! is_array($id)) {
			$id = ['id' => $id];
		}
		
		return $this->model->where($id)->with($relations)->first();
	}

	/**
	 * Get first row from database
	 * 
	 * @param  array  $where
	 * 
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function getFirst(array $where = [], array $relations = []): ?Model
	{
		return $this->getOne($where, $relations);
	}

	public function getMany(array $ids, $where = [], $relations = [])
	{
		return $this->model->whereIn('id', $ids)
			->where($where)->with($relations)->get();
	}

	/**
	 * Create new model and persist in db
	 * 
	 * @param  array  $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $data): Model
	{
		return $this->model->create($data);
	}

	/**
	 * Update model
	 * 
	 * @param  mixed $model
	 * @param  array $data
	 * @param  array $where
	 * @return \Illuminate\Database\Eloquent\Model
	 * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public function update(Model|int $model, array $data, $where = []): Model|false
	{
		$model = $this->isModel($model) ? $model : $this->getOne($model, $where);
		if (! $this->isModel($model)) {
			return false;
		}

		$model->update($data);
		return $model;
	}

	/**
	 * Delete by ids
	 * 
	 * @param  int|array|json $id
	 * @return void
	 */
	public function delete(int | array $id, $where = []): array
	{
		// If no ids are specified and no where conditions provided
		// We cannot allow this delete operation
		if (empty($ids) && empty($where)) {
			return [];
		}
		
		$this->model->when( ! empty($ids), function($query) use ($ids) {
			$query->whereIn('id', $ids);
		})->where($where)->delete();
		
		return $ids;
	}

	/**
	 * Pick some keys from array
	 * @param  array  $data
	 * @param  array  $keys
	 * @return array
	 */
	public function only(array $data, array $keys) : array
	{
		return Arr::only($data, $keys);
	}

	/**
	 * Check if variable is a Model
	 * @param  mixed  $variable
	 * @return boolean
	 */
	public function isModel($variable) : bool
	{
		return $variable instanceof Model;
	}
}
