<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DataTables;
use Validator;

class StateController extends Controller
{
    protected $title    = 'State';
    protected $viewPath = 'state';
    protected $link     = 'states';
    protected $route    = 'states';
    protected $entity   = 'state';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        // $this->middleware('permission:service-category-list|service-category-create|service-category-edit|service-category-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:service-category-create', ['only' => ['create','store']]);
        // $this->middleware('permission:service-category-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:service-category-delete', ['only' => ['destroy']]);
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
        $variants->countries    = Country::where('shop_id', SHOP_ID)->pluck('name', 'id');
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  State::select(['name', 'id'])->where('shop_id', SHOP_ID)->orderBy('id', 'desc');
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
                'required',Rule::unique('countries')->where(function($query) {
                  $query->where('shop_id', '=', SHOP_ID);
              })
            ],
            'country_id' => 'required',
        ]);

        if ($validator->passes()) {

            $data           = new Country();
            $data->name     = $request->name;
            $data->shop_id  = SHOP_ID;
            $data->save();

            return ['flagError' => false, 'message' => $this->title. " Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        //
    }
}
