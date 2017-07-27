<?php namespace App\Repositories\Eloquent;

use App\Models\Merchant;
use App\Repositories\Contracts\MerchantRepository as MerchantRepositoryInterface;
use Illuminate\Http\Exception\HttpResponseException as HttpResponseException;
use Carbon\Carbon;
use DB;

class MerchantRepository extends Repository implements MerchantRepositoryInterface
{
    /**
     * @param User $model
     */
    public function __construct(Merchant $model)
    {
        $this->model = $model;
        //$this->role = $role;
    }

    public function create(array $data)
    {
        // Inputs validations

    $v = \Validator::make($data, [
            'name' => 'required|string',
            'slug' => 'required|string|unique:hapi.merchants,slug',
            'address' => 'sometimes|required_if:self_invoicing,1',
            'contact' => 'required',
            'email' => 'required|email',
            'logo_url' => 'sometimes|required_if:self_invoicing,1|url',
            'gst_reg_no' => 'sometimes|required_if:self_invoicing,1',
            'self_invoicing' => 'required|boolean',
            'timezone' => 'required',
            'currency' => 'required',
            'forex_rate' => 'required',
            'ae' => 'required|integer',
            'status' => 'required|string',

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
            // 'slug' => 'required|string|unique:merchants,slug,'.$id,
            'address' => 'required',
            'contact' => 'required',
            'email' => 'required|email',
            'logo_url' => 'sometimes|required|url',
            'gst_reg_no' => 'sometimes|required',
            'self_invoicing' => 'sometimes|required|boolean',
            'timezone' => 'sometimes|required',
            'currency' => 'sometimes|required',
            'forex_rate' => 'sometimes|required_with:currency',
            'ae' => 'required|integer',
            'status' => 'required|string',
            'created_at' => 'sometimes|date_format:Y-m-d H:i:s',
            'updated_at' => 'sometimes|date_format:Y-m-d H:i:s'
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

    public function getActiveMerchants($byDate=null)
    {
        if (is_null($byDate)) {
            $merchants = Merchant::select('name', 'id')->where('status', '<>', 'Inactive')->get();
        }
        else {
            if($byDate == 'week'){
                $date = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->subDays(6); 
            }
            elseif($byDate == 'month'){
                $date = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->subDays(30); 
            }
            $merchants = Merchant::select(DB::raw('merchants.*'))
                                    ->leftJoin('order_items', 'merchants.id', '=', 'order_items.merchant_id')
                                    ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                                    ->whereBetween('orders.updated_at', [$date, date("Y-m-d")])
                                    ->groupBy('merchants.id')
                                    ->get();
        }
        
        return $merchants;
    }
}
