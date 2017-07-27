<?php
namespace App\Http\Controllers\Admin;

use App\Http\Traits\GuzzleClient;
use Log;
use Response;
use Input;

class CategoriesController extends AdminController
{
    use GuzzleClient;

    protected $admin;

    public function __construct()
    {
        $this->middleware('permission:manage.category');

        $this->admin = \Auth::user();
    }

    public function index()
    {
    }

    public function edit($channel_type_id)
    {
        $channel_type = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$channel_type_id)->getBody()->getContents());
        $data = array();
        $data['channel_type'] = $channel_type;
        $data['categories'] = $this->getCategories($channel_type_id);

        return view('admin.channels.categories-edit', $data);
    }

    public function update($channel_type_id)
    {
        $channel_type = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$channel_type_id)->getBody()->getContents());

        // get existing categories, before updated
        $old_categories = $this->getCategories($channel_type_id);

        $response = json_decode($this->putGuzzleClient(array(), 'channels/admin/channel_type/'. $channel_type_id .'/update_categories')->getBody()->getContents());
        if(!$response->success)
        {   
            return redirect()->back()
                ->with('error', 'Error Encountered while getting categories ! '.print_r($response, true));
        }

        $categories = $response->categories;

        // get new categories, after updated
        $new_categories = array_flip((array) $categories);

        // get active categories (in use)
        $active_categories = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'. $channel_type_id .'/get_active_categories')->getBody()->getContents());

        // get deleted categories that are in use
        // at the same time, construct categories mapping array
        $deleted_categories = array_diff($old_categories, $new_categories);
        $mapping_categories = array();
        foreach($deleted_categories as $name=>$id)
        {
            $in_use = false;
            foreach($active_categories as $active_cat)
            {
                if($active_cat->cat_id == $id)
                {
                    $in_use = true;

                    $new_category_id = '';
                    if(isset($new_categories[$name])) {
                        $new_category_id = $new_categories[$name];
                    }

                    if($id != 0) {
                        $mapping_categories[] = array(
                            'old_category_id' => $id,
                            'new_category_id' => $new_category_id
                        );
                    }
                    break;
                }
            }

            if(!$in_use || $id == 0)
            {
                unset($deleted_categories[$name]);
            }
        }
        ksort($deleted_categories);

        // get newly added categories
        $added_categories = array_diff($new_categories, $old_categories);
        ksort($added_categories);

        $mapHtml = \View::make('admin.channels.categories-map', [
            'deleted_categories' => $deleted_categories, 
            'added_categories' => $added_categories, 
            'mapping_categories' => $mapping_categories, 
            'channel_type' => $channel_type,
            'categories' => $new_categories
        ])->render();

        $response = array(
            'success' => true,
            'mapHtml' => $mapHtml
        );
        return response()->json($response);
    }

    public function downloadProductsWithOutdatedCategory($channel_type_id)
    {
        $channel_type = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$channel_type_id)->getBody()->getContents());

        $categories = array_flip($this->getCategories($channel_type_id));

        $products = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'. $channel_type_id .'/get_outdated_categories_products')->getBody()->getContents());

        $header = config('csv.outdatedCategoriesProducts');
        $rows[] = $header;
        foreach ($products as $product)
        {
            if (empty($categories[$product->category_id])) {
                $rows[] = array(
                    $product->merchant_id,
                    str_replace(',', ' ', $product->merchant_name),
                    $product->product_id,
                    str_replace(',', ' ', $product->product_name),
                    str_replace(',', ' ', $product->brand_name) .sprintf("%06d", $product->product_id),
                    $product->category_id,
                    !empty($product->category_name) ? str_replace(',', ' ', $product->category_name) : 'Not defined'
                );
            }
        }

        $output = '';
        foreach($rows as $row)
        {
            $output .= implode(",", $row) ."\n";
        }

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$channel_type->name.'_products_with_outdated_category_'.date("dmY",time()).'.csv"'
        );

        return Response::make(rtrim($output), 200, $headers);
    }

    public function getCategories($channel_type_id) {
        $channel_type = json_decode($this->getGuzzleClient(array(), 'channels/admin/channel_type/'.$channel_type_id)->getBody()->getContents());
        $categories = (array) json_decode($this->getGuzzleClient(array('channel_type_name'=>$channel_type->name), 'channels/admin/categories/get')->getBody()->getContents());
        return !empty($categories) ? $categories : array();
    }

    public function remap($channel_type_id)
    {
        $category_list = array_flip($this->getCategories($channel_type_id));
        $input = json_decode(Input::get('data', []));
        foreach ($input as $key => $value) {
            $input[$key]->cat_name = isset($category_list[$value->to]) ? $category_list[$value->to] : 'Not defined';
        }

        $data = array();
        $data['channel_type_id'] = $channel_type_id;
        $data['data'] = $input;

        $products = json_decode($this->putGuzzleClient($data, 'channels/admin/channel_type/'. $channel_type_id .'/remap_categories')->getBody()->getContents());

        return redirect()->back()
                ->with('success', 'Mapping of categories has been saved successfully.');
    }
}
