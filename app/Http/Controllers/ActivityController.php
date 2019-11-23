<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Activity;
use Validator;

class ActivityController extends BaseController
{
    //
    public function index()
    {
        $Activitys = Activity::all();

        return $this->sendResponse($Activitys->toArray(), 'Activitys retrieved successfully.');
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
        $input = $request->all();

        $validator = Validator::make($input, [
            'startDate' => 'required',
            'deadline' => 'required',
            'endDate' => 'required',
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'owner' => 'required' 
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        
        if( $this->isWeekend($request->input("startDate")) || 
            $this->isWeekend($request->input("deadline")) ||
            $this->isWeekend($request->input("endDate")) ){
            
            return $this->sendError('Validation Error.', "Activities must not allow during weekends.");
        }

        $Activitys = Activity::where('owner', $request->input("owner") )
                            ->where(function ($q) use($request) {
                                $q->where(function ($q) use($request) {
                                    $q->Where('startDate','<=', $request->input("startDate"))
                                    ->Where('endDate','>=', $request->input("startDate"));
                                })
                                ->orwhere(function ($q) use($request) {
                                    $q->Where('endDate','>=', $request->input("endDate"))
                                    ->Where('startDate','<=', $request->input("endDate"));
                                });
                            })
                             ->get();

        if( count($Activitys->toArray()) != 0){
            return $this->sendError('Validation Error.', "Two Activities on the same time.");       
        }

        $Activity = Activity::create($input);

        return $this->sendResponse($Activity->toArray(), 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Activity = Activity::find($id);

        if (is_null($Activity)) {
            return $this->sendError('Activity not found.');
        }

        return $this->sendResponse($Activity->toArray(), 'Activity retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $Activity)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'startDate' => 'required',
            'startDate' => 'required',
            'deadline' => 'required',
            'endDate' => 'required',
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'owner' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if( $this->isWeekend($request->input("startDate")) || 
            $this->isWeekend($request->input("deadline")) ||
            $this->isWeekend($request->input("endDate")) ){
            
            return $this->sendError('Validation Error.', "Activities must not allow during weekends.");
        }

        $Activitys = Activity::where('owner', $request->input("owner") )
                            ->where(function ($q) use($request) {
                                $q->where(function ($q) use($request) {
                                    $q->Where('startDate','<=', $request->input("startDate"))
                                      ->Where('endDate','>=', $request->input("startDate"));
                                })
                                ->orwhere(function ($q) use($request) {
                                    $q->Where('endDate','>=', $request->input("endDate"))
                                      ->Where('startDate','<=', $request->input("endDate"));
                                });
                            })
                             ->Where('id','!=', $Activity->id)
                             ->get();

        if( count($Activitys->toArray()) != 0){
            return $this->sendError('Validation Error.', "Two Activities on the same time.");       
        }

        $Activity->startDate = $input['startDate'];
        $Activity->deadline = $input['deadline'];
        $Activity->endDate = $input['endDate'];
        $Activity->title = $input['title'];
        $Activity->description = $input['description'];
        $Activity->status = $input['status'];
        $Activity->save();

        return $this->sendResponse($Activity->toArray(), 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $Activity)
    {
        $Activity->delete();

        return $this->sendResponse($Activity->toArray(), 'Activity deleted successfully.');
    }


    /** 
     * Function to know if is Weekend.
     * 
     * @param  String  $date
     * @return Boolean 
     */

    public function isWeekend(String $date) {
        return (date('N', strtotime($date)) >= 6);
    }
}
