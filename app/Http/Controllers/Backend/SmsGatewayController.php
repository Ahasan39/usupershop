<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Sms;
use Illuminate\Http\Request;

class SmsGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['countsms'] = Sms::count();
        $data['allData'] = Sms::select('id','apiKey','userName','SenderName')->orderBy('id','DESC')->get();
        return view('backend.sms-gateway.view-smsgateway',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.sms-gateway.add-smsgateway');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\DB::transaction(function() use($request){
            $this->validate($request,[
                'apiKey'=>'required',
                'userName'=>'required',
                'SenderName'=>'required'
            ]);
            $smsData = new Sms();
            $smsData->apiKey = $request->apiKey;
            $smsData->userName = $request->userName;
            $smsData->SenderName = $request->SenderName;
            $smsData->save();
        });
        return redirect()->route('smsgateways.view')->with('success', 'Data inserted successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['editData'] = Sms::find($id);
        return view('backend.sms-gateway.add-smsgateway', $data);
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
        \Illuminate\Support\Facades\DB::transaction(function() use($request,$id){
            $data = Sms::find($id);
            $data->apiKey = $request->apiKey;
            $data->userName = $request->userName;
            $data->SenderName = $request->SenderName;
            $data->save();
        });
        return redirect()->route('smsgateways.view')->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
