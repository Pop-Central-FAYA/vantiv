<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;
use Yajra\DataTables\DataTables;
use Goutte\Client;


class IndustriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $industries = Utilities::switch_db('api')->select("SELECT * from sectors");
        return view('admin.industry.index', compact('industries'));
    }

    public function getData(DataTables $dataTables)
    {
        $industriesArray = [];
        $industries = Utilities::switch_db('api')->select("SELECT * from sectors");
        $j = 1;
        foreach ($industries as $industry){
            $industriesArray[] = [
                's_n' => $j,
                'id' => $industry->id,
                'name' => $industry->name,
                'sector_code' => $industry->sector_code,
            ];

            $j++;
        }
        return $dataTables->collection($industriesArray)
            ->addColumn('edit', function ($industriesArray) {
                return '<a href="' . route('industry.edit', $industriesArray['sector_code']) . '" class="btn btn-success btn-xs"> Edit  </a>';
            })
            ->addColumn('delete', function ($industriesArray) {
                return '<button data-toggle="modal" data-target=".deleteModal' . $industriesArray['id'] . '" class="btn btn-danger btn-xs" > Delete </button>';
            })
            ->rawColumns(['edit' => 'edit', 'delete' => 'delete'])->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.industry.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'industry_name' => 'required',
            'sic' => 'required',
        ]);

        //checking if the industry exist on our DB
        $check_industry = Utilities::switch_db('api')->select("SELECT * from sectors where sector_code = '$request->sic'");
        if(count($check_industry) === 1){
            \Session::flash('error', 'Industry already exists');
            return redirect()->back();
        }else{
            $id = uniqid();
            $insert_industry = Utilities::switch_db('api')->insert("INSERT into sectors (id, `name`, sector_code) VALUES ('$id', '$request->industry_name', '$request->sic')");
            if($insert_industry){
                \Session::flash('success', 'Industry created...');
                return redirect()->route('industry.index');
            }else{
                \Session::flash('error', 'Industry already exists');
                return redirect()->back();
            }
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        $industry = Utilities::switch_db('api')->select("SELECT * from sectors where sector_code = '$code'");
        return view('admin.industry.edit', compact('industry'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $industry = Utilities::switch_db('api')->select("SELECT * from sectors where id = '$id'");
        if($request->industry_name !== $industry[0]->name || $request->sic !== $industry[0]->sector_code){
            if($request->industry_name !== $industry[0]->name){
                $this->validate($request, [
                   'industry_name' => 'required'
                ]);
                $check_validity = Utilities::switch_db('api')->select("SELECT * from sectors where name = '$request->industry_name'");
                if(count($check_validity) === 1){
                    \Session::flash('error', 'Industry already exist');
                    return redirect()->back();
                }else{
                    $update_industry = Utilities::switch_db('api')->update("UPDATE sectors set name = '$request->industry_name' WHERE id = '$id'");
                    if($update_industry){
                        \Session::flash('success', 'Industry updated');
                        return redirect()->route('industry.index');
                    }else{
                        \Session::flash('error', 'An error occurred');
                        return back();
                    }
                }
            }
            if($request->sic !== $industry[0]->sector_code){
                $this->validate($request, [
                    'sic' => 'required'
                ]);
                $check_validity = Utilities::switch_db('api')->select("SELECT * from sectors where sector_code = '$request->sic'");
                if(count($check_validity) === 1){
                    \Session::flash('error', 'Industry already exist');
                    return redirect()->back();
                }else{
                    $update_industry = Utilities::switch_db('api')->update("UPDATE sectors set sector_code = '$request->sic' WHERE id = '$id'");
                    if($update_industry){
                        \Session::flash('success', 'Industry updated');
                        return redirect()->route('industry.index');
                    }else{
                        \Session::flash('error', 'An error occurred');
                        return back();
                    }
                }
            }
        }

        \Session::flash('info', 'No changes made');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function indexSubIndustry()
    {
        $industries = Utilities::switch_db('api')->select("SELECT * from subSectors");
        return view('admin.sub-industry.index', compact('industries'));
    }

    public function subIndustryData(DataTables $dataTables)
    {
        $industriesArray = [];
        $industries = Utilities::switch_db('api')->select("SELECT * from subSectors");
        $j = 1;
        foreach ($industries as $industry){
            $industry_name = Utilities::switch_db('api')->select("SELECT * from sectors where sector_code = '$industry->sector_id'");
            $industriesArray[] = [
                's_n' => $j,
                'id' => $industry->id,
                'name' => $industry->name,
                'sector_name' => $industry_name[0]->name,
                'sector_code' => $industry->sub_sector_code,
            ];

            $j++;
        }
        return $dataTables->collection($industriesArray)
            ->addColumn('edit', function ($industriesArray) {
                return '<a href="' . route('sub_industry.edit', $industriesArray['sector_code']) . '" class="btn btn-success btn-xs"> Edit  </a>';
            })
            ->addColumn('delete', function ($industriesArray) {
                return '<button data-toggle="modal" data-target=".deleteModal' . $industriesArray['id'] . '" class="btn btn-danger btn-xs" > Delete </button>    ';
            })
            ->rawColumns(['edit' => 'edit', 'delete' => 'delete'])->addIndexColumn()
            ->make(true);
    }

    public function editSubIndustry($code)
    {
        $sub_industry = Utilities::switch_db('api')->select("SELECT * from subSectors where sub_sector_code = '$code'");
        $industries = Utilities::switch_db('api')->select("SELECT * from sectors");
        return view('admin.sub-industry.edit', compact('sub_industry', 'industries'));
    }

    public function indexCreateIndustry()
    {
        $industries = Utilities::switch_db('api')->select("SELECT * from sectors");
        return view('admin.sub-industry.create', compact('industries'));
    }

    public function storeSubIndustry(Request $request)
    {
        $this->validate($request, [
            'sub_industry_name' => 'required',
            'sub_sic' => 'required',
            'industry' => 'required',
        ]);

        $check_sub_industry = Utilities::switch_db('api')->select("SELECT * from subSectors where sub_sector_code = '$request->sub_sic'");
        if(count($check_sub_industry) === 1){
            \Session::flash('error', 'Sub Industry already exist');
            return redirect()->back();
        }else{
            $id = uniqid();
            $insertSubIndustry = Utilities::switch_db('api')->insert("INSERT into subSectors (id, sector_id, name, sub_sector_code) VALUES ('$id', '$request->industry', '$request->sub_industry_name', '$request->sub_sic')");
            if($insertSubIndustry){
                \Session::flash('success', 'Sub Industry added successfully');
                return redirect()->route('sub_industry.index');
            }else{
                \Session::flash('error', 'An error occurred while adding this sub industry');
                return redirect()->back();
            }
        }
    }

    public function deleteSubIndustry($id)
    {

    }

    public function updateSubIndustry(Request $request, $id)
    {
        $sub_industry = Utilities::switch_db('api')->select("SELECT * from subSectors where id = '$id'");
        if($request->sub_industry_name !== $sub_industry[0]->name || $request->sub_sic !== $sub_industry[0]->sub_sector_code || $request->industry !== $sub_industry[0]->sector_id){
            if($request->sub_industry_name !== $sub_industry[0]->name){
                $this->validate($request, [
                    'sub_industry_name' => 'required'
                ]);
                $check_validity = Utilities::switch_db('api')->select("SELECT * from subSectors where name = '$request->sub_industry_name'");
                if(count($check_validity) === 1){
                    \Session::flash('error', 'Sub Industry already exist');
                    return redirect()->back();
                }else{
                    $update_industry = Utilities::switch_db('api')->update("UPDATE subSectors set name = '$request->sub_industry_name' WHERE id = '$id'");
                    if($update_industry){
                        \Session::flash('success', 'Sub Industry updated');
                        return redirect()->route('sub_industry.index');
                    }else{
                        \Session::flash('error', 'An error occurred');
                        return back();
                    }
                }
            }
            if($request->sub_sic !== $sub_industry[0]->sub_sector_code){
                $this->validate($request, [
                    'sub_sic' => 'required'
                ]);
                $check_validity = Utilities::switch_db('api')->select("SELECT * from subSectors where sub_sector_code = '$request->sub_sic'");
                if(count($check_validity) === 1){
                    \Session::flash('error', 'Sub Industry already exist');
                    return redirect()->back();
                }else{
                    $update_industry = Utilities::switch_db('api')->update("UPDATE subSectors set sub_sector_code = '$request->sub_sic' WHERE id = '$id'");
                    if($update_industry){
                        \Session::flash('success', 'Sub Industry updated');
                        return redirect()->route('sub_industry.index');
                    }else{
                        \Session::flash('error', 'An error occurred');
                        return back();
                    }
                }
            }
            if($request->industry !== $sub_industry[0]->sector_id){
                $this->validate($request, [
                    'industry' => 'required'
                ]);
                $check_validity = Utilities::switch_db('api')->select("SELECT * from subSectors where sub_sector_code = '$request->sub_sic' AND name = '$request->sub_industry_name' AND sub_sector_code = '$request->sub_sic'");
                if(count($check_validity) === 1){
                    \Session::flash('error', 'Sub Industry already exist');
                    return redirect()->back();
                }else{
                    $update_industry = Utilities::switch_db('api')->update("UPDATE subSectors set sector_id = '$request->industry' WHERE id = '$id'");
                    if($update_industry){
                        \Session::flash('success', 'Sub Industry updated');
                        return redirect()->route('sub_industry.index');
                    }else{
                        \Session::flash('error', 'An error occurred');
                        return back();
                    }
                }
            }
        }

        \Session::flash('info', 'No changes made');
        return redirect()->back();
    }
}
