<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaxHelper
{
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

    public static function getGstincluded($amount,$percent,$cgst,$sgst)
    {
        $result         = array();

        $gst_amount     = $amount-($amount*(100/(100+$percent)));
        $percentcgst    = number_format($gst_amount/2, 2);
        $percentsgst    = number_format($gst_amount/2, 2);


        if($cgst&&$sgst){
            $gst = $percentcgst + $percentsgst;
        }elseif($cgst){
            $gst = $percentcgst;
        }else{
            $gst = $percentsgst;
        }
        $withoutgst = number_format($amount - $gst_amount,2);
        $withoutgst = $amount - $gst_amount;        
        $withgst    = ($withoutgst + $gst_amount);

        $result = ['withoutgst' => $withoutgst, 'gst' => $gst, 'withgst' => $withgst, 'CGST' => $percentcgst, 'SGST' => $percentsgst];
        return $result;
    }

    public static function getGstexcluded($amount,$percent,$cgst,$sgst)
    {
        $result         = array();

        $gst_amount     = ($amount*$percent)/100;
        $amountwithgst  = $amount + $gst_amount;   
        $percentcgst    = number_format($gst_amount/2, 2);
        $percentsgst    = number_format($gst_amount/2, 2);

        if($cgst&&$cgst){
           $gst = $percentcgst + $percentsgst;
        }elseif($cgst){
           $gst = $percentcgst;
        }else{
           $gst = $percentsgst;
        }
        // $display .="</p>";
        // $display .="<p>".$amount . " + " . $gst . " = " . $amountwithgst."</p>";
        $result = ['amount' => $amount, 'gst' => $gst, 'amountwithgst' => $amountwithgst, 'CGST' => $percentcgst, 'SGST' => $percentsgst];
        return $result;
    }
}

