<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;


/**
 * @group Category
 *
 * APIs for Category,Sub-Categories and Category By Id
 */
class CategoryController extends Controller
{


/**
 * Get-Parent-Category-list
 *
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 *  @response {
 *    "status": 200,
 *    "message": "Getting Category data successfully",
 *    "data": {
 *        "current_page": 1,
 *        "data": [
 *            {
 *                "id": 1,
 *                "name": "Mens",
 *                "image": "category/nkQoMtCQkV.jpg",
 *                "category_image": "http://127.0.0.1:8000/category/nkQoMtCQkV.jpg"
 *            }
 *        ],
 *        "first_page_url": "http://127.0.0.1:8000/api/get-categories?page=1",
 *        "from": 1,
 *        "last_page": 2,
 *        "last_page_url": "http://127.0.0.1:8000/api/get-categories?page=2",
 *        "links": [
 *            {
 *                "url": null,
 *                "label": "&laquo; Previous",
 *                "active": false
 *            },
 *            {
 *                "url": "http://127.0.0.1:8000/api/get-categories?page=1",
 *                "label": "1",
 *                "active": true
 *            },
 *            {
 *                "url": "http://127.0.0.1:8000/api/get-categories?page=2",
 *                "label": "2",
 *                "active": false
 *            },
 *            {
 *                "url": "http://127.0.0.1:8000/api/get-categories?page=2",
 *                "label": "Next &raquo;",
 *                "active": false
 *            }
 *        ],
 *        "next_page_url": "http://127.0.0.1:8000/api/get-categories?page=2",
 *        "path": "http://127.0.0.1:8000/api/get-categories",
 *        "per_page": 1,
 *        "prev_page_url": null,
 *        "to": 1,
 *        "total": 2
 *    }
 *}
 * 
 */
public function getCategory(){

$data = Category::where('parent_id',null)->select('id','name','image')->where('status','active')->paginate(10);
return Helper::ApiResponse(200,'Getting Category data successfully',$data);

}

/**
 * Get-SubCategory-list
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 * @urlParam id required The ID of the Category-id.URL/get-subcategories/1
 *  @response {
 *    "status": 200,
 *    "message": "Getting Sub-Category Data Successfully",
 *    "data": {
 *        "current_page": 1,
 *        "data": [
 *            {
 *                "id": 3,
 *                "name": "jeans Men",
 *                "image": "category/C6Dw91f1Wq.jpg",
 *                "category_image": "http://127.0.0.1:8000/category/C6Dw91f1Wq.jpg"
 *            }
 *        ],
 *        "first_page_url": "http://127.0.0.1:8000/api/get-subcategories/1?page=1",
 *        "from": 1,
 *        "last_page": 1,
 *        "last_page_url": "http://127.0.0.1:8000/api/get-subcategories/1?page=1",
 *        "links": [
 *            {
 *                "url": null,
 *                "label": "&laquo; Previous",
 *                "active": false
 *            },
 *            {
 *                "url": "http://127.0.0.1:8000/api/get-subcategories/1?page=1",
 *                "label": "1",
 *                "active": true
 *            },
 *            {
 *                "url": null,
 *                "label": "Next &raquo;",
 *                "active": false
 *            }
 *        ],
 *        "next_page_url": null,
 *        "path": "http://127.0.0.1:8000/api/get-subcategories/1",
 *        "per_page": 10,
 *        "prev_page_url": null,
 *        "to": 1,
 *        "total": 1
 *    }
 *}
 * 
 */
public function getSubCategory($id = null){

    if (is_null($id)) {
        return Helper::ApiResponse(400, 'Parent-Category ID is required', null);
    }      
    
    $data = Category::select('id','name','image')->where('parent_id',$id)->where('status','active')->paginate(10);   
    return Helper::ApiResponse(200,'Getting Sub-Category Data Successfully',$data);

}


/**
 * Category-By-Id
 * @header Content-Type application/JsonResponse
 * @header Authorization (Put Access-Token) 
 * @urlParam id required The ID of the Category-id.URL/category-by-id/1
 * @response{
 *    "status": 200,
 *    "message": "Getting Category Data Successfully",
 *    "data": {
 *        "id": 1,
 *        "name": "Mens",
 *        "image": "category/nkQoMtCQkV.jpg",
 *        "category_image": "http://127.0.0.1:8000/category/nkQoMtCQkV.jpg"
 *    }
 *}
 * 
 */
public function getCategoryById($id){
    
   $data = Category::with('SubCategory')->select('id','name','image')->first();   
   return Helper::ApiResponse(200,'Getting Category Data Successfully',$data);

}


}
