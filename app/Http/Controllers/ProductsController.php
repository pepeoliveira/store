<?php

namespace App\Http\Controllers;

use App\Category;
use App\ProductsAttribute;
use Illuminate\Http\Request;
use Auth;
use Session;
use Image;
use App\Product;

class ProductsController extends Controller
{
    public function addProduct(Request $request){

        if($request->isMethod('post')) {
            $data = $request->all();

            if(empty($data['category_id'])){
                return redirect()->back()->with('flash_message_error','Under Category is missing');
            }
            $product = new Product;
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            if(!empty($data['description'])){
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }
            if(!empty($data['care'])){
                $product->care = $data['care'];
            }else{
                $product->care = '';
            }
            $product->price = $data['price'];

            //upload photos
            if($request->hasFile('image')){
                $image_tmp = $request->file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,9999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;

                    //ajustar tamanhos
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);

                    //store photos name in products table

                    $product->image = $filename;
                }
            }

            $product->save();
            return redirect('/admin/view-products')->with('flash_message_success','Product has been added Successfully');

         }

        // Categories drop down start
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option selected disabled>Select</option>";
        foreach($categories as $cat){
            $categories_dropdown .= "<option value='".$cat->id."'>".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach($sub_categories as $sub_cat){
                $categories_dropdown .= "<option value= '".$sub_cat->id."'>&nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }
        // Categories drop down end
        return view('admin.products.add_product',['categories_dropdown'=>$categories_dropdown]);
    }
    public function editProduct(Request $request, $id=null){

        if($request->isMethod('post')){
            $data = $request->all();

            if($request->hasFile('image')){
                $image_tmp = $request->file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,9999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;

                    //ajustar tamanhos
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);

                }else{
                    $filename = $data['current_image'];
                }

                if(empty($data['description'])){
                    $data['description'] = '';
                }
                if(empty($data['care'])){
                    $data['care'] = '';
                }
            }



            Product::where(['id'=>$id])->update([
                'category_id'=>$data['category_id'],
                'product_name'=>$data['product_name'],
                'product_code'=>$data['product_code'],
                'product_color'=>$data['product_color'],
                'description'=>$data['description'],
                'care'=>$data['care'],
                'price'=>$data['price'],
                'image'=>$filename]);

            return redirect()->back()->with('flash_message_success','Product has been updated successfully!');
        }

        // Get Product Details
        $productDetails = Product::where(['id'=>$id])->first();

        // Categories drop down start
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option selected disabled>Select</option>";
        foreach($categories as $cat){
            if($cat->id==$productDetails->category_id){
                $selected = "selected";
            }else{
                $selected = "";
            }
            $categories_dropdown .= "<option value='".$cat->id."' ".$selected.">".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach($sub_categories as $sub_cat){
                if($sub_cat->id==$productDetails->category_id){
                    $selected = "selected";
                }else{
                    $selected = "";
                }
                $categories_dropdown .= "<option value= '".$sub_cat->id."' ".$selected.">&nbsp;--&nbsp;".$sub_cat->name."</option>";
            }
        }
        // Categories drop down end

        return view('admin.products.edit_product')->with(compact('productDetails','categories_dropdown'));
    }
    public function viewProducts(){
        $products = Product::orderby('id','DESC')->get();
        foreach($products as $key => $val){
            $category_name = Category::where(['id'=>$val->category_id])->first();
            $products[$key]->category_name = $category_name['name'];
        }
        return view('admin.products.view_products')->with(compact('products'));
    }
    public function deleteProduct(Request $request, $id=null){
        Product::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success','Product has been delete successfully!');
    }
    public function deleteProductImage($id = null){

        // Get Product Image Name
        $productImage=Product::where(['id'=>$id])->first();

        //Get Product Image Paths
        $large_image_path = 'imagens/backend_images/products/large/';
        $medium_image_path = 'imagens/backend_images/products/medium/';
        $small_image_path = 'imagens/backend_images/products/small/';

        // Delete Large Image if not exists in folder
        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        }

        // Delete Medium Image if not exists in folder
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }

        // Delete Small Image if not exists in folder
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }



        Product::where(['id'=>$id])->update(['image'=>'']);
        return redirect()->back()->with('flash_message_success','Product Image has been deleted successfully!');
    }

    // ATTRIBUTES

    // ADD ATTRIBUTE
    public function addAttribute(Request $request, $id=null){
        $productDetails = Product::with(['attributes'])->where(['id'=>$id])->first();
//        $productDetails = json_decode(json_encode($productDetails));
        if($request->isMethod('post')){
            $data = $request->all();

            foreach ($data['sku'] as $key => $val){
                if(!empty($val)){
                    $attribute = new ProductsAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                }
            }

            return redirect('admin/add-attributes/'.$id)->with('flash_message_success','Product Attributes has been added successfully!');

        }
        return view('admin.products.add_attributes',['productDetails'=>$productDetails]);
    }

    // DELETE ATTRIBUTE

    public function deleteAttribute($id = null){
        ProductsAttribute::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_success','Attribute has been deleted successfully!');
    }

    public function products($url = null){
        //get all categories and subcategories
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();

        $categoriesDetails = Category::where(['url'=>$url])->first();

        if($categoriesDetails->parent_id==0){
            //if url is main category url
            $subCategories = Category::where(['parent_id'=>$categoriesDetails->id])->get();
            //$cat_ids = "";
            foreach ($subCategories as $subCat){
                $cat_ids[] = $subCat->id; //$cat_ids .= $subCat->id.",";
            }
            $productsAll = Product::whereIn('category_id',$cat_ids)->get();  //array($cat_ids)
            $productsAll = json_decode(json_encode($productsAll));
            //echo "<pre>"; print_r($productsAll); die;

        }else{
            $productsAll = Product::where(['category_id'=>$categoriesDetails->id])->get();
        }

        $productsAll = Product::where(['category_id' => $categoriesDetails->id])->get();

        return view('products.listing')->with(compact('categories','categoriesDetails','productsAll'));


    }

    public function product($id = null){
        //Get Product Details
        $productDetails = Product::with('attributes')->where('id',$id)->first();
        $productDetails = json_decode(json_encode($productDetails));
        //echo "<pre>"; print_r($productDetails); die;

        // Get All Categories and Sub Categories
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();
        return view('products.detail')->with(compact('productDetails','categories'));

    }

    public function getProductPrice(Request $request){
        $data = $request->all();
        $proArr = explode("-",$data['idSize']);
        $proAttr = ProductsAttribute::where(['product_id' => $proArr[0],'size' => $proArr[1]])->first();
        echo $proAttr->price;
    }

}
