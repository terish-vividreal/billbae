<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Keygen\Keygen;
use Image;

class FunctionHelper
{

    public static function generateCode($length, $prefix, $user_id = null)
    {
			
        $code 		= Keygen::numeric($length)->prefix($prefix, false)->suffix($user_id)->generate();
        return $code;		
        // do {
        // 	$code 		= Keygen::numeric($length)->prefix($prefix, false)->generate();
        // 	$data 		= Admin::where('code', $code)->first();
        // 	$flag 		= (isset($data))? true:false;
        // }
        // while ($data->count() > 0);

    }
    public static function storeImage($file, $path, $slug)
    {
        $path = 'public/' . $path;
        $slug = Str::slug($slug);
        Storage::makeDirectory($path);
        $extension = $file->getClientOriginalExtension();
        $file_name = $slug . '-' . time() . '.' . $extension;
        Storage::putFileAs($path, $file, $file_name);
        return $file_name;
    }

    public static function moveCropped($image, $new_path, $slug)
    {
        $image_name = '';
        if ($image != '') {
            $path = 'public/' . $new_path;
            $temp = 'public/temp/' . Session::get('temp_url') . '/';
            Storage::makeDirectory($path);
            $result = explode('.', $image);
            $extension = $result[1];
            $image_name = $slug . '-' . time() . '.' . $extension;
            Storage::move($temp . $image, $path . $image_name);
            Storage::deleteDirectory($temp);
            Session::forget('temp_url');
        }
        return $image_name;
    }

    public static function cropAndStore($image, $path, $slug)
    {
        $image_name = '';

        if ($image != '') {

            $input['imagename'] = $slug . '-' . time().'.'.$image->extension();         
            $destinationPath    = public_path('/thumbnail');

            // Create storage folder
            $store_path = 'public/' . $path;
            Storage::makeDirectory($store_path);



            // $img = Image::make($image->path());
            // $img->resize(100, 100, function ($constraint) {
            //     $constraint->aspectRatio();
            // })->save($destinationPath.'/'.$input['imagename']);


                $resize = Image::make($image)->resize(215, 215, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('jpg');


                $hash           = md5($resize->__toString());
                $image_name     = $hash."jpg";
                $save           = Storage::put($store_path.'/'.$image_name, $resize->__toString());
                return $image_name;
        }

        return $image_name;
    }
}

