<?php namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\Repository as RepositoryInterface;
use Carbon\Carbon;

abstract class Repository implements RepositoryInterface
{

    /**
     * Eloquent model
     */
    protected $model;
    private $with;
    
    /**
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Fetch all records
     *
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * Fetch records by page
     *
     * @param $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 10, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a record by id
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return $this->model->findOrFail($id)->update($data);
    }

    /**
     * Update a record by field
     *
     * @param array $data
     * @param $field
     * @param $value
     * @return mixed
     */
    public function updateBy(array $data, $field, $value)
    {
        return $this->model->where($field, '=', $value)->update($data);
    }

    public function with($relations)
    {

        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->model->with = $this->with = $relations;
        $this->model = $this->model->newQuery()->with($relations);

        return $this;
    }

    /**
     * Delete a record
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
    
    public function deleteBy($field, $value)
    {
        return $this->model->where($field, '=', $value)->delete();
    }

    /**
     * Fetch a record by id
     *
     * @param $id
     * @param $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Fetch a record by a field value
     *
     * @param $field
     * @param $value
     * @param $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*'])
    {
        return $this->model->where($field, '=', $value)->first($columns);
    }
    
    public function findLike($field, $value, $columns = ['*'])
    {
        return $this->model->where($field, 'like', '%'.$value.'%')->first($columns);
    }

    public function findAllBy($field, $value, $columns = ['*'])
    {
        return $this->model->where($field, '=', $value)->get($columns);
    }

    public function findForUser($id, $userId, $columns = ['*'])
    {
        return $this->model->whereUserId($userId)->findOrFail($id, $columns);
    }

    public function where($column,  $operator = null,  $value = null,  $boolean = 'and')
    {
        $this->model = $this->model->where($column,  $operator, $value,  $boolean);
        return $this;
    }

    public function lists($value, $key = null)
    {
        $this->model = $this->model->lists($value, $key);
        return $this;
    }


    protected function getList($prepend = ['' => ''], $toStr, $items)
    {
        $items = $this->prependList($prepend, $items);

        if ($toStr) {
            return $this->listToStr($items);
        }

        return $items;
    }

    protected function prependList($prepend, $list)
    {
        return ( is_array($prepend) )? array_merge($prepend,[$list]) : $list;
    }

    protected function listToStr($items, $delimeter = ';')
    {
        $list = '';
        foreach ($items as $id => $name) {
            if (!empty($list)) {
                $list .= $delimeter;
            }
            $list .= "{$id}:{$name}";
        }

        return $list;
    }
}
