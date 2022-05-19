<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pos_store_desktop;
use App\Models\pos_pc_desktop;

class appController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('index');
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
        //
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
        //
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
        //
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

    public function pickStore($id)
    {
        $stores = pos_pc_desktop::findOrFail($id);

        $store = session()->get('store', []);

        if(isset($store[$id])) {
            unset($store);
            session()->forget('store');
            session()->put('store', []);
            $store[$id] = [
                "id" => $stores->id,
                "store_id" => $stores->id_store,
                "name" => $stores->name,
            ];
        } else {
            unset($store);
            session()->forget('store');
            $store[$id] = [
                "id" => $stores->id,
                "store_id" => $stores->id_store,
                "name" => $stores->name,
            ];
        }

        session()->put('store', $store);
        return redirect('/deposit');
    }

}
