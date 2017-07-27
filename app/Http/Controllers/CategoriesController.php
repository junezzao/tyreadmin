<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Merchant;
use App\Http\Traits\GuzzleClient;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Form;
use Illuminate\Support\MessageBag;


class CategoriesController extends Controller
{
	use GuzzleClient;
    protected $admin;
    private $max_level = 3;
    public function __construct()
    {
        $this->middleware('permission:view.category', ['only' => ['index', 'show']]);
        $this->middleware('permission:create.category', ['only' => ['create','store']]);
        $this->middleware('permission:edit.category', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete.category', ['only' => ['destroy']]);
        $this->admin = \Auth::user();
    }

    public function index()
    {
    	$data = array();
    	$data['user'] = $this->admin;
        $data['categories'] = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;
        $data['parents'] = array();
        foreach($data['categories'] as $category)
        {
        	if($category->level < $this->max_level)
        	{
        		$data['parents'][$category->id] = $category->full_name;
        	}
        }
        return view('categories.index', $data);
    }

    public function show($id)
    {

    }

    public function create()
    {

    }

    public function store()
    {
    	$response = json_decode($this->postGuzzleClient(request()->all(), 'admin/categories')->getBody()->getContents());
    	if(!empty($response->error) || empty($response->id))
    	{
    		flash()->error('Whoops! Something went wrong.');
        	return redirect()->back()->withErrors(new MessageBag((array)$response->error));
    	}
    	flash()->success('The category was successfully created.');
        return redirect()->route('products.categories.index');

    }

    public function edit($id)
    {
        $data = array();
        $data['user'] = $this->admin;
        $data['category'] = json_decode($this->getGuzzleClient(null, 'admin/categories/'.$id)->getBody()->getContents());
        $data['categories'] = json_decode($this->getGuzzleClient(null, 'admin/categories')->getBody()->getContents())->categories;
        $data['parents'] = array();
        foreach($data['categories'] as $category)
        {
            if( ($category->level < $this->max_level ) && ($category->id != $id && $category->parent_id != $id) )
            {
                $data['parents'][$category->id] = ($category->full_name);
            }
        }
        return view('categories.edit', $data);
    }

    public function update($id)
    {
    	$response = json_decode($this->putGuzzleClient(request()->all(), 'admin/categories/'.$id)->getBody()->getContents());
    	if(!empty($response->error) || empty($response->id))
    	{
    		flash()->error('Whoops! Something went wrong.');
        	return redirect()->back()->withErrors(new MessageBag((array)$response->error));
    	}
    	flash()->success('The category was successfully updated.');
        return redirect()->route('products.categories.index');
    }

    public function destroy($id)
    {
    	$response = json_decode($this->deleteGuzzleClient(request()->all(), 'admin/categories/'.$id)->getBody()->getContents());
    	if(!empty($response->errors) || !$response->acknowledged)
    	{
            $errorMsgs = array();
            foreach($response->errors as $error){
                $errorMsgs[] = $error;
            }

            if(count($errorMsgs) > 0){
                flash()->error(implode('<br>', $errorMsgs));
            }else{
                flash()->error('Whoops! Something went wrong.');
            }
        	return redirect()->back();
    	}
    	flash()->success('The category was successfully deleted.');
        return redirect()->route('products.categories.index');
    }
}
