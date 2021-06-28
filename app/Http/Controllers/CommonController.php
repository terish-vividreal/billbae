<?php

namespace App\Http\Controllers;

use Form;
use Illuminate\Http\Request;
use App\Helpers\TaxHelper;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\Package;
use DB;
use Session;
use Auth;

class CommonController extends Controller
{

    public function getStates(Request $request)
    {
        $states   = State::where('country_id',$request->country_id)->pluck('name','id');
        $form     = Form::select('state_id', $states , '', ['class' => 'form-control', 'placeholder' => 'Select a state' , 'id'=>'state_id' ]);
        return response($form);
    }

    public function getDistricts(Request $request)
    {
        $districts  = District::where('state_id',$request->state_id)->pluck('name','id');
        $form       = Form::select('district_id', $districts , '', ['class' => 'form-control', 'placeholder' => 'Select a district' , 'id'=>'district_id' ]);
        return response($form);
    }

    public function getShopDistricts(Request $request)
    {
        $districts  = DB::table('shop_districts')->where('state_id',$request->state_id)->pluck('name','id');
        $form       = Form::select('district_id', $districts , '', ['class' => 'form-control', 'placeholder' => 'Select a district' , 'id'=>'district_id' ]);
        return response($form);
    }
    
    public function getAllServices(Request $request)
    {
        // if($request->category_id)
        // whereIn('service_category_id', $request->category_id)->

        $services   = Service::select('name','id')->where('shop_id', SHOP_ID)->get();
        if($services)
            return response()->json(['flagError' => false, 'data' => $services]);
    }
    
    public function getAllPackages(Request $request)
    {

        $packages   = Package::select('name','id')->where('shop_id', SHOP_ID)->get();
        if($packages)
            return response()->json(['flagError' => false, 'data' => $packages]);
    }
    
    public function getServices(Request $request)
    {
        $query   = Service::with('hours')->where('shop_id', SHOP_ID)->orderBy('id', 'desc');

        if($request->data_ids)
            $query   = $query->whereIn('id', $request->data_ids);

        $services   = $query->get();   

        if($services)
            return response()->json(['flagError' => false, 'data' => $services, 'totalPrice' => $services->sum('price')]);
    }
    
    public function getPackages(Request $request)
    {
        $query   = Package::where('shop_id', SHOP_ID)->orderBy('id', 'desc');

        if($request->data_ids)
            $query   = $query->whereIn('id', $request->data_ids);

        $ackages   = $query->get();   

        if($ackages)
            return response()->json(['flagError' => false, 'data' => $ackages, 'totalPrice' => $ackages->sum('price')]);
    }

    public function getCustomerDetails(Request $request)
    {
        $customer   = Customer::where('id', $request->customer_id)->where('shop_id', SHOP_ID)->first();
        if($customer)
            return response()->json(['flagError' => false,'data'=>$customer]);
        else
          return response()->json(['flagError' => true,'message'=>'Customer not fount']);
    }
    
    public function calculateTax(Request $request)
    {
        
        $type = $request->type;
        if($type == 'services'){
            $result = Service::with('additionaltax')->where('shop_id', SHOP_ID)->whereIn('id', $request->data_ids)->orderBy('id', 'desc')->get();
        }else{
            $result = Package::with('additionaltax')->where('shop_id', SHOP_ID)->whereIn('id', $request->data_ids)->orderBy('id', 'desc')->get();
        }

        if($result){

            $html                   = '';
            $index                  = 1 ;
            $total_percentage       = 0 ;
            $total_service_tax      = 0 ;
            $gross_charge           = 0 ;
            $gross_value            = 0 ;
            $grand_total            = 0 ;
            $additional_tax_array   = array();

            foreach($result as $row){

                // $tax_data       = TaxHelper::simpleTaxCalculation($row);


                $total_percentage = $row->gst_tax ;                
                if(count($row->additionaltax) > 0){
                    foreach($row->additionaltax as $additional){
                        $total_percentage = $total_percentage+$additional->percentage;
                    } 
                }

                $total_service_tax          = ($row->price/100) * $total_percentage ;        
                $tax_onepercentage          = $total_service_tax/$total_percentage;
                $total_gst_amount           = $tax_onepercentage*$row->gst_tax ;
                $total_cgst_amount          = $tax_onepercentage*($row->gst_tax/2) ;
                $total_sgst_amount          = $tax_onepercentage*($row->gst_tax/2) ;

                if($row->tax_included == 1) {
                    $included = 'Tax Included' ;
                    $gross_charge   = $row->price ;
                    $gross_value    = $row->price - $total_service_tax ;
                }else{
                    $included = 'Tax Excluded' ;
                    $gross_charge   = $row->price + $total_service_tax  ;
                    $gross_value    = $row->price ;
                }


                    $html.='<tr data-widget="expandable-table" aria-expanded="true"><td>'.$index.'</td><td>'.$row->name.' - HSN Code : '.$row->hsn_code.' ( '.$included.' )</td><td> '.number_format($gross_value,2).'</td></tr>';
                    $html.='<tr class="expandable-body"> </tr>';
                    $html.='<tr class="expandable-body" style="text-align:center;">';
                    $html.='<td colspan="2">';


                    $html.='<div id="cgst"> <span> '.($row->gst_tax/2).' % CGST -  </span> '.number_format($total_cgst_amount,2).' </div>';
                    $html.='<div id="sgst"> <span> '.($row->gst_tax/2).' % SGST -  </span> '.number_format($total_sgst_amount,2).' </div>';
                
                    if(count($row->additionaltax) > 0){
                        foreach($row->additionaltax as $additional){
                            $html.='<div id="additionalTax" class="test gst"> <span>  ' . $additional->percentage . ' % ' . $additional->name. '  </span> - '.number_format($tax_onepercentage*$additional->percentage,2). '</div>';
                        }
                    }

                    $html.='</td></tr>';
                    $html.='<tr data-widget="expandable-table" aria-expanded="false"><td colspan="2">';
                    // $html.='<div style="text-align:left;"></div>';
                    $html.='<div style="text-align:right;"><b>Total </b><br></td><td><b>'.number_format($gross_charge,2).'</b></div></td></tr>';

                $grand_total = ($grand_total + $gross_charge); ;
                $index++;
            }
            
            // $table_footer='<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td><b>Total - <h4>₹ '.$total_tax.'</h4></b></td><td><b>Total - <h4>₹ '.number_format($total_amount,2).'</h4></b></td></tr></tfoot>';
        }

        return response()->json(['flagError' => false, 'grand_total' => $grand_total, 'html' => $html]);
    }
    

    // public function getSubjects($curriculum_id)
    // {
       
    //     $subjects   = Subject::Join('subject_curriculum','subjects.id','subject_curriculum.subject_id')
    //                 ->where('subject_curriculum.curriculum_id',$curriculum_id)->pluck('subjects.name','subjects.id');
    //     $form       = Form::select('subject_id', $subjects , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Subject' , 'id'=>'subject_id' ]);
    //     return response($form);
    // }

    // public function getChapters($topic_id)
    // {

    //     $chapters     = Chapter::where('topic_id',$topic_id)->pluck('name','id');
    //     $form         = Form::select('chapter_id', $chapters , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Chapter' , 'id'=>'chapter_id' ]);

    //     return response($form);
    // }

    // public function getTopics(Request $request)
    // {
    //     $year  = $request->year;
    //     $paper = $request->paper;
    //     $topics   = Topic::where('curriculum_id',$request->curriculum)
    //                 ->where('subject_id',$request->subject)
    //                /* ->where(function($query) use ($year,$paper)  {
    //                    if($year>0) {
    //                       $query->where('year_id', $year);
    //                     }else{
    //                         $query->where('paper_id', $paper);
    //                     }
    //                  })*/
    //                 ->pluck('name','id');
                   
    //     $form       = Form::select('topic_id', $topics , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Topic' , 'id'=>'topic_id' ]);
    //     return response($form);
    //     
    // }

    // public function getUnits($chapter_id)
    // {
    //     $units   = Unit::where('chapter_id',$chapter_id)
    //                     ->pluck('name','id');
    //     $form       = Form::select('unit_id', $units , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Unit' , 'id'=>'unit_id' ]);
    //     return response($form);
    // }
    // public function getClasses($year_id)
    // {
    //     $user_id        = Auth::user()->id; 
    //     $classes   = Classes::where('year_id',$year_id)->where('teacher_id',$user_id)->pluck('name','id');
    //     $form         = Form::select('class_id', $classes , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Class' , 'id'=>'class_id' ]);

    //     return response($form);
    // }

    // public function getClassesByUser(Request $request)
    // {
    //     $year_id        = $request->year_id;
    //     $teacher_id     = $request->teacher_id;

    //     if($year_id != null)
    //         $class_query    = Classes::where('year_id',$year_id);

    //     if($teacher_id)
    //         $class_query    = $class_query->where('teacher_id',$teacher_id);


    //     $classes    = $class_query->pluck('name','id');
    //     $form         = Form::select('class_id', $classes , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Class' , 'id'=>'class_id' ]);

    //     return response($form);
    // }

    // public function getYears($curriculam_id)
    // {
    //     $component   = Curriculum::where('id',$curriculam_id)->first()->component_type;
    //     $ctype       = [];
    //     $ph          = '';
    //     if($component == 'paper')
    //     {
    //         $ph      = 'Select Paper';
    //         $ctype   = Paper::pluck('paper','id');
    //     }
    //     else if($component == 'year')
    //     {
    //         $ph      = 'Select Year';
    //         $ctype   = Year::pluck('year','id');
    //     }

    //     $form         = Form::select('year_id', $ctype , '', ['class' => 'form-control new-drop-section', 'placeholder' => $ph , 'id'=>'year_id' ]);

    //     return response($form);
    // }
    //  public function getStudents($class_id)
    // {
    //     $students     = Student::where('class_id',$class_id)->pluck('fname','id');
    //     $form         = Form::select('student_id', $students , '', ['class' => 'form-control new-drop-section','multiple'=>'multiple', 'placeholder' => 'Select Students' ,'name'=>'student_id[]', 'id'=>'student_id' ]);

    //     return response($form);
    // }

    // public function getRegions($country_id)
    // {
     
    //     $region =  Region::where('country_id',$country_id)->pluck('name','id');
    //     $form         = Form::select('region_id', $region , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Region' , 'id'=>'region_id' ]);
    //     return response($form);
    // }
    // public function getSchool($region_id)
    // {
    //     $school =  School::where('region_id',$region_id)->pluck('name','id');
    //     $form         = Form::select('school_id', $school , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select School' , 'id'=>'school_id' ]);
    //     return response($form);
    // }

    // public function getSyllubus( Request $request){

    //     $topicsSet   = Topic::select('id',DB::raw("CONCAT(topic_code,': ',name) AS topic"),'paper_id')
    //              ->where('country_id', $request->country_id)
    //              ->where('curriculum_id', $request->curriculum_id)
    //              ->where('subject_id', $request->subject_id);

    //      if($request->search )  {
    //         $topicsSet->where('topics.name', 'like', "%{$request->search}%"); 
    //         $topicsSet->orWhere('topics.topic_code', 'like', "%{$request->search}%"); 
    //      }      
    //     $topicsSet = $topicsSet->get()->toArray();
    //     $topicGroup   =  Helper::_array_group($topicsSet,'paper_id') ;
    //     if(!$topicGroup){
    //         return response("<p> No syllabus found </p>");    
    //     }
          
    //     $html = '';
    //     foreach ($topicGroup as $key => $topics) {
    //         $html.= '<div class="col-md-6">';
    //         foreach ($topics as $key => $topic) {
               
    //              $html.='<div class="scroll-content" id="topic-'.$topic['id'].'">';
    //              $html.='<div class="subject-detail-box-section"><div class="subject-detail-box-section-topic"><h2>'.$topic['topic'].'</h2></div>';

    //              $chapters   = Chapter::select('chapters.id as chapter_id',
    //                            DB::raw("CONCAT(chapter_code,': ',chapters.name) AS chapter"))
    //                            ->where('chapters.topic_id', $topic['id']);
    //             // if($request->search){
    //             //      $chapters->where('chapters.name', 'like', "%{$request->search}%"); 
    //             // }                
                
    //            $chapters = $chapters->get() ;
                               
    //              foreach ($chapters as $key => $chapter) {
    //                     $units = Unit::select('id as unit_id','name') 
    //                             ->where('chapter_id',$chapter->chapter_id)
    //                             ->get();
    //                     $html.='<div class="listed-section">';
    //                     $html.=' <h3>'.$chapter->chapter.'</h3>';

    //                     foreach ($units as $key => $unit) {      
    //                         $html.=' <p><a class="" href="javascript:void(0);" onclick="createQuestion('.$unit->unit_id.')">'.$unit->name.'</a></p>'; 
    //                     } 
    //                     $html.='</div><div class="line-strip"></div>';  

    //             }  

    //             $html.= '</div></div>';

    //         }
    //         $html.= '</div>';

            
    //     }


        
    //     return response($html);    
                      
    // }

    // public function getcurriculums($country_id)
    // {
        
    //     $curriculum   = Curriculum::where('country_id',$country_id)->select('id','name')->get();
    //     $html = '';
    //     foreach ($curriculum as $key => $data) {
    //        $html.='<button type="button" class="btn btn-info font-weight-bold btn-md mr-2 btn-cur btn-cur-'.$data->id.'" onclick="setCurriculum(this.id)" id="'. $data->id .'">'. $data->name .'</button>';
    //     }
    //     return response($html);
    // }

    // public function getTopicCodes(Request $request){

    //     $topics   = Topic::where('country_id',$request->country_id)
    //                 ->where('curriculum_id',$request->curriculum_id)
    //                 ->where('subject_id',$request->subject_id)
    //                 ->select('id','topic_code')
    //                 ->get();
    //      $html ='';           
    //     foreach ($topics as $key => $topic) {
    //        $html.='<button type="button" class="btn btn-success font-weight-bold btn-md mr-2 btn-top btn-top-'.$topic->id.'" onclick="gotoTopic(this.id)" id="'. $topic->id .'">'. $topic->topic_code .'</button>';
    //     }
    //     return response($html);           
    // }

    // public function getClassButtons($year_id)
    // {
    //     $user_id        = Auth::user()->id; 
    //     $claasess   = Classes::where('year_id',$year_id)->select('id','name')->where('teacher_id',$user_id)->get();
    //     $html = '';
    //     foreach ($claasess as $key => $data) {
    //        $html.='<button type="button" class="btn btn-info font-weight-bold btn-md mr-2 btn-cur btn-cur-'.$data->id.'" onclick="setClass(this.id)" id="'. $data->id .'">'.$data->name .'</button>';
    //     }
    //     return response($html);
    // }
    // // public function clearFilter()
    // // {
    // //     session()->forget(['country','curriculum','subject','year','topic','chapter','unit','paper']);
    // //     $message = "session cleared";
    // //     return response($message);
    // // }

    // public function ConfirmUser($id)
    // {
    //    // $user      = Token::where('token_code',$id)->first();
    //    // $user_id   = $user->user_id;
    //     $user_type = User::where('id',$id)->first();
    //     $user_type->is_verified = 1;
    //     $user_type->save();
        
    //     return redirect('/login');

    // }

    // public function ForgotPasswordUser($id)
    // {
    //     $user = Token::where('token_code',$id)->first();
    //     $user_id = $user->user_id;
    //     $user_type = User::where('id',$user_id)->first();

    //   return view('admin.setting.resetPassword',compact('user_id'));

    // }

    // public function setPassword(Request $request)
    // {

    //     $user = Token::where('token_code',$request->userId)->first();
       
    //     $user_type = User::where('id',$request->userId)->first();
    //     $user_type->password = Hash::make($request->password);
       
    //     if($user_type->save()){
    //         if($user_type->role == 'school'){
    //             $school = School::where('user_id',$request->userId)->first();
    //             $school->is_confirmed = 1;
    //             $school->save();
    //             $delete = Token::where('user_id',$request->userId)->delete();
    //         }elseif($user_type->role == 'student'){
    //             $student = Student::where('user_id',$request->userId)->first();
    //             $student->is_confirmed = 1;
    //             $student->save();
    //             $delete = Token::where('user_id',$request->userId)->delete();
    //         }
        
    //    }

    //    return view('login');
    // }
}

