<?php

namespace App\Http\Controllers;

use App\Models\Scrapped;
use App\Models\ScrappedStatus;
use Illuminate\Http\Request;

class ScrappedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = new Scrapped();
        if($request->status != 0){
            $status = $request->status;
            if($status == 1){
                $data = $data->leftJoin('scrape_lead_status', 'scrape_leads.serial', '=', 'scrape_lead_status.serial')
                    ->select('scrape_leads.id', 'scrape_leads.serial', 'scrape_leads.data', 'scrape_leads.created_at as created_at', 'scrape_lead_status.serial', 'scrape_lead_status.updated_at')
                    ->orderBy('scrape_lead_status.updated_at', 'desc');
            }else{
                $data = $data->leftJoin('scrape_lead_status', 'scrape_leads.serial', '=', 'scrape_lead_status.serial')
                    ->select('scrape_leads.id', 'scrape_leads.serial', 'scrape_leads.data', 'scrape_leads.created_at as created_at', 'scrape_lead_status.serial', 'scrape_lead_status.updated_at')
                    ->orderBy('scrape_lead_status.updated_at', 'asc');
            }
        }
        
        if($request->name != null){
            $data = $data->where('data->Summary->Mark', 'like', '%' . strtoupper($request->name) . '%');
        }
        
        if($request->date != null){
            $data = $data->whereDate('scrape_leads.created_at', $request->date);
        }
        $data = $data->paginate(60);
        return view('scrapped.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBrandsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function show(Brands $brands)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBrandsRequest  $request
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brands  $brands
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
    
    public function updateStatus(Request $request){
        $serial = $request->serial;
        $status = $request->status;
        
        $get_data = ScrappedStatus::where('serial', $serial)->first();
        if($get_data == null){
            $data = new ScrappedStatus();
            $data->serial = $serial;
            $data->status = $status;
            $data->save();
        }else{
            $get_data->status = $status;
            $get_data->save();
        }
        
        return response()->json(['status' => true, 'message' => $status, 'serial' => $serial]);
    }
}