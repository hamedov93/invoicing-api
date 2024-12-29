<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * The base interface for all eloquent repositories
 */
interface EloquentRepositoryInterface
{
	/**
	 * Get multiple rows from database
	 * 
	 * @param  array $filters
	 * @param  array $relations
	 * @param array $counts
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 *         | \Illuminate\Database\Eloquent\Collection
	 */
	public function getList(array $params);

	/**
	 * Get single row from database
	 * 
	 * @param  int|array    $id
	 * @return null|\Illuminate\Database\Eloquent\Model
	 */
	public function getOne(int|array $id): ?Model;

	/**
	 * Get first row from database
	 * 
	 * @param  array  $where
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function getFirst(array $where = [], array $relations = []): ?Model;

	/**
	 * Create new model and persist in db
	 * @param  array  $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $data): Model;

	/**
	 * Update model
	 * @param  mixed  $model
	 * @param  array  $data
	 * @param  array  $where
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function update(Model|int $model, array $data, $where = []): Model|false;

	/**
	 * Delete models by ids
	 * @param  int|array $id
	 * @param  array $where
	 * @return void
	 */
	public function delete(int | array $id, $where = []): array;

	/**
	 * Pick some keys from array
	 * @param  array  $data
	 * @param  array  $keys
	 * @return array
	 */
	public function only(array $data, array $keys): array;
}
