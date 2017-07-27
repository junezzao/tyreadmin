<?php namespace App\Repositories\Contracts;

interface Repository
{
    /**
     * Fetch all records
     *
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * Fetch records by page
     *
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * Create a new record
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update a record by id
     *
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * Delete a record
     *
     *  @param int $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Fetch a record by id
     *
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Fetch a record by field value
     *
     * @param str $field
     * @param str $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);

    /**
     * Fetch a record by field value
     *
     * @param str $field
     * @param str $value
     * @param array $columns
     * @return mixed
     */
    public function findLike($field, $value, $columns = ['*']);
    
    /**
     * Fetch a record by id and user id
     *
     * @param int $id
     * @param int $userId
     * @param array $columns
     * @return mixed
     */
    public function findForUser($id, $userId, $columns = ['*']);
}
