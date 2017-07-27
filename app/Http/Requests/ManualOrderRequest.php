<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ManualOrderRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            //'merchant'                   => 'required|exists:hapi.merchants,id',
            'channel'                    => 'required|exists:hapi.channels,id',
            'tp_code'                    => 'required_if:channel_type,10',
            'recipient_name'             => 'required',
            'recipient_contact'          => 'required',
            'recipient_address_1'        => 'required',
            'recipient_address_city'     => 'required',
            'recipient_address_state'    => 'required',
            'recipient_address_postcode' => 'required',
            'recipient_address_country'  => 'required',
            'customer_name'              => 'required',
            'customer_email'             => 'email',
            'customer_contact'           => 'required',
            'payment_type'               => 'required',
            'cart_discount'              => 'required|numeric',
            'currency'                   => 'required',
            'amount_paid'                => 'required|numeric',
            'shipping_fee'               => 'sometimes',
            'shipping_no'                => 'sometimes',
            'total_tax'                  => 'required|numeric',
            'order_date'                 => 'required|date_format:Y-m-d',
            'order_time'                 => 'required|date_format:H:i:s',
        ];

        foreach($this->request->all() as $key => $input) {
            if(is_array($input)){
                $i = 0;
                foreach($input as $val){
                    if($key == 'discount' || $key == 'sold_price' || $key == 'weighted_discount')
                        $rules[$key.'.'.$i] = 'required|numeric';
                    elseif ($key == 'unit_price' || $key == 'sale_price')
                         $rules[$key.'.'.$i] = 'required|numeric';
                    elseif($key == 'quantity')
                        $rules[$key.'.'.$i] = 'required|integer|min:1';
                    elseif($key == 'hubwire_sku')
                        $rules[$key.'.'.$i] = 'required|exists:hapi.sku,hubwire_sku';
                    elseif($key == 'ref_no' || $key == 'tp_item_id')
                        $rules[$key.'.'.$i] = '';
                    else
                        $rules[$key.'.'.$i] = 'required';
                    $i++;
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
      $messages = [];
      foreach($this->request->all() as $key => $input)
      {
        $messages['tp_code.required_if'] = 'Third Party Order cannot be empty if channel type is 11street.';

        if(is_array($input)){
            foreach($input as $k => $val){
                $messages['hubwire_sku.'.$k.'.exists'] = 'Invalid Hubwire SKU.';
                $messages['unit_price.'.$k.'.numeric'] = 'Invalid retail price.';
                $messages['sale_price.'.$k.'.numeric'] = 'Invalid listing price.';
                $messages['discount.'.$k.'.required'] = 'Discount cannot be empty.';
                $messages['sold_price.'.$k.'.required'] = 'Sold Price cannot be empty.';
                $messages['quantity.'.$k.'.required'] = 'Quantity cannot be empty.';
                $messages['discount.'.$k.'.numeric'] = 'Discount must be a number.';
                $messages['sold_price.'.$k.'.numeric'] = 'Sold Price must be a number.';
                $messages['quantity.'.$k.'.integer'] = 'Quantity must be a number.';
                $messages['quantity.'.$k.'.min'] = 'Quantity must be more than 0.';
            }
        }
      }

      return $messages;
    }

}
