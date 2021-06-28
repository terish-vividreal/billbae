<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\State;
use App\Models\Country;
use Form;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DataTables;
use Validator;

class DistrictController extends Controller
{
    protected $title    = 'District';
    protected $viewPath = 'district';
    protected $link     = 'districts';
    protected $route    = 'districts';
    protected $entity   = 'district';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:manage-location', ['only' => ['index','store', 'edit', 'destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;        
        $variants->country      = Country::where('shop_id', SHOP_ID)->pluck('name', 'id');        
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  District::where('shop_id', SHOP_ID)->orderBy('id', 'desc');
        if (isset($request->form)) {
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'search_name') {
                    $names = strtolower($search['value']);
                    $detail->where('name', 'like', "%{$names}%");
                }
            }
        }
            
            return Datatables::of($detail)
                ->addIndexColumn()
                ->addColumn('action', function($detail){
                    $action = ' <a  href="javascript:" onclick="manageState(' . $detail->id . ')" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                    $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"> <i class="icon-1x fas fa-trash-alt"></i></a>';
                    return $action;
                })
                ->addColumn('country', function($detail){
                    $country = $detail->state->country->name;
                    return $country;
                })
                ->addColumn('state', function($detail){
                    $country = $detail->state->name;
                    return $country;
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
            'name' => [
                'required',Rule::unique('districts')->where(function($query) {
                  $query->where('shop_id', '=', SHOP_ID);
              })
            ],
        ]);

        if ($validator->passes()) {

            $data           = new District();
            $data->name     = $request->name;
            $data->state_id = $request->state_id;
            $data->shop_id  = SHOP_ID;
            $data->save();

            return ['flagError' => false, 'message' => $this->title. " Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data   = District::with('state')->findOrFail($id);
        // $states = State::where('country_id',$data->state->country->id)->pluck('name','id');
        $states = State::where('country_id',$data->state->country->id)->get();

        if($data){
            return ['flagError' => false, 'data' => $data, 'country_id' => $data->state->country->id, 'states' => $states];
        }else{
            return ['flagError' => true, 'message' => "Data not found, Try again!"];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => [
                'required',
                    Rule::unique('districts')->where(function($query) use($id) {
                    $query->where('shop_id', '=', SHOP_ID)->where('id', '!=', $id);
                    })
            ],
        ];
    
        $messages = [
            'required' => 'Please enter district name'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);


        if ($validator->passes()) {
            $data = Country::findOrFail($id);
            if($data){
                $data->name = $request->name;
                $data->save();
                return ['flagError' => false, 'message' => $this->title. " Updated successfully"];
            }else{
                return ['flagError' => true, 'message' => "Data not found, Try again!"];
            }
        }
        return ['flagError' => true, 'error'=>$validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $data = Country::findOrFail($id);

        $data->delete();
        return ['flagError' => false, 'message' => $this->title. " Deleted successfully"];
    }
}
