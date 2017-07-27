<?php namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepository as SupplierRepositoryInterface;
use Illuminate\Http\Exception\HttpResponseException as HttpResponseException;

class SupplierRepository extends Repository implements SupplierRepositoryInterface
{
    /**
     * @param User $model
     */
    public function __construct(Supplier $model)
    {
        $this->model = $model;
        //$this->role = $role;
    }

    public function create(array $data)
    {
        // Inputs validations

    $v = \Validator::make($data, [
            'name' => 'required|string',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'mobile' => 'sometimes',
            'contact_person' => 'required', 
            'merchant_id' => 'sometimes|required|exists:merchants,id'
        ]);

        if ($v->fails()) {
            $response = new \stdClass();
            $response->error = $v->errors();
            $response->validator = $v;
            return $response;
        }
        $newinputs = array();
        foreach ($data as $k => $v) {
            $key = $k;
            if (isset($this->maps[$key])) {
                $key = $this->maps[$key];
            }
            $newinputs[$key] = $v;
        }
   
        $data = $newinputs;
        unset($data['access_token']);
    // \Log::info(print_r($inputs, true));
    $model = parent::create($data);
        return $this->find($model->id);
    }

    public function update(array $data, $id, $attribute='id')
    {
        // Inputs validations

    $v = \Validator::make($data, [
            'name' => 'required|string',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'mobile' => 'sometimes',
            'contact_person' => 'required',
            'merchant_id' => 'sometimes|required|exists:merchants,id'
        ]);

        if ($v->fails()) {
            $errors =  response()->json(
             array(
                'code' =>  422,
                'error' => $v->errors()
            ));
            throw new HttpResponseException($errors);
        }
        unset($data['access_token']);
        $newinputs = array();
        foreach ($data as $k => $v) {
            $key = $k;
            if (isset($this->maps[$key])) {
                $key = $this->maps[$key];
            }
            $newinputs[$key] = $v;
        }
        $data = $newinputs;
        $model = parent::update($data, $id, $attribute);
        return $this->find($id);
    }
}
