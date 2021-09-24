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

    public static function simpleTaxCalculation($row, $discount = array())
    {
        $total_percentage       = 0 ;
        $total_service_tax      = 0 ;
        $gross_charge           = 0 ;
        $gross_value            = 0 ;
        $discount_amount        = 0 ;
        $discount_type          = '';
        $discount_value         = 0;
        $additional_tax_array   = array();
        $tax_array              = array();

            $total_percentage       = $row->gst_tax ; 
            $total_percentage       = $row->gsttax->percentage;               
            if(count($row->additionaltax) > 0){
                foreach($row->additionaltax as $additional){
                    $total_percentage = $total_percentage+$additional->percentage;
                } 
            }

            $total_service_tax          = ($row->price/100) * $total_percentage ;        
            $tax_onepercentage          = $total_service_tax/$total_percentage;
            $total_gst_amount           = $tax_onepercentage*$row->gsttax->percentage;
            $total_cgst_amount          = $tax_onepercentage*($row->gsttax->percentage/2) ;
            $total_sgst_amount          = $tax_onepercentage*($row->gsttax->percentage/2) ;

            if($row->tax_included == 1) {
                $included = 'Tax Included' ;
                $gross_charge   = $row->price ;
                $gross_value    = $row->price - $total_service_tax ;
            }else{
                $included = 'Tax Excluded' ;
                $gross_charge   = $row->price + $total_service_tax  ;
                $gross_value    = $row->price ;
            }

            if(count($row->additionaltax) > 0){
                foreach($row->additionaltax as $additional)
                {
                    $additional_tax_array[] = ['name' => $additional->name, 'percentage' => $additional->percentage, 'amount' => number_format($tax_onepercentage*$additional->percentage,2)];
                }
            }

            // Discount calculation starts
            if($row->is_discount_used == 1)
            {
                if($row->tax_included == 1) 
                {
                    if($row->discount_type == 'amount'){
                        $discount_type      = 'amount';
                        $discount_amount  = $row->discount_value ;
                        $balance_discount_amount  = $gross_charge - $row->discount_value;
                    }else{
                        $discount_type      = 'percentage';
                        $discount_value     = $row->discount_value;
                        $discount_amount  = $gross_charge * ($row->discount_value/100);
                        $balance_discount_amount  = $gross_charge - $discount_amount;
                    }


                    $gross_value            = $balance_discount_amount * ((100 - $total_percentage)/100)  ;  
                    $total_cgst_amount      = $balance_discount_amount * (($row->gsttax->percentage/2)/100) ;  
                    $total_sgst_amount      = $balance_discount_amount * (($row->gsttax->percentage/2)/100) ;  
                    
                    
                    if(count($additional_tax_array) > 0){
                        foreach($additional_tax_array as $key => $additional)
                        {
                            $additional_tax_array[$key]['amount'] = $balance_discount_amount * ($additional['percentage']/100) ; 
                        }
                    }

                }
                else
                {
                    if($row->discount_type == 'amount'){
                        $discount_amount    = $row->discount_value ;
                        $discount_type      = 'amount';
                    }else{
                        $discount_type      = 'percentage';
                        $discount_value     = $row->discount_value;
                        $discount_amount    = $gross_value * ($row->discount_value/100);
                    }

                    $gross_value        = $gross_value - ($discount_amount * (100 - $total_percentage)/100) ;  
                    $total_cgst_amount  = $total_cgst_amount - ($discount_amount * ($row->gsttax->percentage/2) / 100) ;
                    $total_sgst_amount  = $total_sgst_amount - ($discount_amount * ($row->gsttax->percentage/2) / 100) ;


                    if(count($additional_tax_array) > 0){
                        foreach($additional_tax_array as $key => $additional)
                        {
                            $additional_tax_array[$key]['amount'] = $additional['amount'] - ($discount_amount * $additional['percentage'] / 100) ; 
                        }
                    }
                }
            }

            

            $tax_array = [  'name' => $row->name, 
                            'tax_method' => $included, 
                            'hsn_code' => $row->hsn_code, 
                            'amount' => $gross_value,
                            'total_tax_percentage' => $row->gsttax->percentage,
                            'cgst_percentage' => ($row->gsttax->percentage/2),
                            'sgst_percentage' => ($row->gsttax->percentage/2),
                            'cgst' => number_format($total_cgst_amount,2),
                            'sgst' => number_format($total_sgst_amount,2),
                            'total_amount' => $gross_charge,
                            'additiona_array' => $additional_tax_array,
                            'discount_applied' => $row->is_discount_used,
                            'discount_amount' => $discount_amount,
                            'discount_value' => $discount_value,
                            'discount_type' => $discount_type,
                            ];
            
            return $tax_array;

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

