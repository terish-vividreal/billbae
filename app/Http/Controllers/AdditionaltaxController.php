<?php

namespace App\Http\Controllers;

use App\Models\Additionaltax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DataTables;
use Validator;

class AdditionaltaxController extends Controller
{
    protected $title    = 'Additional tax';
    protected $viewPath = 'additional-tax';
    protected $link     = 'additional-tax';
    protected $route    = 'additional-tax';
    protected $entity   = 'additionalTax';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page           = collect();
        $page->title    = $this->title;
        $page->link     = url($this->link);
        $page->route    = $this->route;
        $page->entity   = $this->entity;
        return view($this->viewPath . '.list', compact('page'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Additionaltax::where('shop_id', SHOP_ID)->orderBy('id', 'desc');  
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail){
                $action = ' <a  href="javascript:" onclick="manageAdditionalTax(' . $detail->id . ')" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="deleteAdditionalTax(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></a>';
                return $action;
            })
            ->addColumn('percentage', function($detail){
                $percentage = $detail->percentage. ' %';
                return $percentage;
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);          
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->passes()) {

            $data               = new Additionaltax();
            $data->shop_id      = SHOP_ID;
            $data->name         = $request->name;
            $data->percentage   = $request->percentage;
            $data->information  = $request->information;
            $data->save();

            return ['flagError' => false, 'message' => $this->title. " Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Additionaltax  $additionaltax
     * @return \Illuminate\Http\Response
     */
    public function show(Additionaltax $additionaltax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Additionaltax  $additionaltax
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Additionaltax::findOrFail($id);
        if($data){
            return ['flagError' => false, 'data' => $data];
        }else{
            return ['flagError' => true, 'message' => "Data not found, Try again!"];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Additionaltax  $additionaltax
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->passes()) {

            $data               = Additionaltax::findOrFail($id);
            $data->name         = $request->name;
            $data->percentage   = $request->percentage;
            $data->information  = $request->information;
            $data->save();

            return ['flagError' => false, 'message' => $this->title. " updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Additionaltax  $additionaltax
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Additionaltax::findOrFail($id);

        $data->delete();
        return ['flagError' => false, 'message' => $this->title. " Deleted successfully"];
    }
}
