<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\image;
use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Exception;

class PostController extends Controller implements ICRUD
{
    //
    public function list()
    {
        $list = post::all();
        $categories = category::all();
        $image = image::all();
        return view('be.interface.post.post', compact('list','categories','image'));
    }
    public function doadd(){
        $categories = category::all();
        return view('be.interface.post.post-add', compact('categories'));
    }

    public function add(Request $request)
    {
        try {
            $data = $request->all();
            unset($data['_token']);
            unset($data['insert']);
            if(isset($data['image_id']))
            {
                $array = $data['image_id'];
                $mainImageName = time().'1'.$array->getClientOriginalName();
                $array->storeAs('/album', $mainImageName, 'public');
                $urlImage= 'storage/album/' . $mainImageName;
                $cate_id=$data['category_id'];
                $dataImage = [
                    'name' => $array->getClientOriginalName(),
                    'OriginalName' => $mainImageName,
                    'path_url' => $urlImage,
                    'album_id' => $cate_id
                ];
                $id = image::insertGetId($dataImage);
                $data['image_id'] = $id;
            }
            DB::table('posts')->insert($data);
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'thêm thất bại!');
        }
        return redirect(route('admin.post.list'))->with('success', 'thêm thành công!');
    }

    public function doedit($id){
        $list=post::find($id);
        $categories=category::all();
        return view('be.interface.post.post-edit', compact('list','categories'));
    }
        public function edit(Request $request)
    {
       try {
        $data = $request->all();
        unset($data['_token']);
        unset($data['insert']);
        if(isset($data['image-upload']))
        {
            $array = $data['image-upload'];
            $mainImageName = time().'1'.$array->getClientOriginalName();
            $array->storeAs('/album', $mainImageName, 'public');
            $urlImage= 'storage/album/' . $mainImageName;
            $cate_id=$data['category_id'];
            $id =$data['image_id'];
            $dataImage = [
                'name' => $array->getClientOriginalName(),
                'OriginalName' => $mainImageName,
                'path_url' => $urlImage,
                'album_id' => $cate_id,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            image::where('id', '=',$id )->update($dataImage);
            unset($data['image-upload']);
        }
        DB::table('posts')->where('id', '=', $data['id'])->update($data);
       }
       catch (Exception $exception) {
           return redirect()->back()->with('error', 'Sửa thất bại!');
       }
       return redirect(route('admin.post.list'))->with('success', 'Sửa thành công!');
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}
