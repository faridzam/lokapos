<?php

namespace App\Http\Controllers;

use App\Http\Controllers\printController;

use Illuminate\Http\Request;
use App\Models\pos_deposit_desktop;
use App\Models\pos_store_desktop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class depositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('deposit');
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

        if (session('store')) {
            foreach (session('store') as $id_store => $choosenStore) {
                $PC = $choosenStore['id'];
            }
        }

        pos_deposit_desktop::create([

            'pc_id' => $PC,
            'cashier_id' => Auth::guard('cashier')->user()->id,
            'pec100' => request('pec100'),
            'pec50' => request('pec50'),
            'pec20' => request('pec20'),
            'pec10' => request('pec10'),
            'pec5' => request('pec5'),
            'pec2' => request('pec2'),
            'pec1' => request('pec1'),
            'total' => request('nominal')

        ]);

        // Instantiate other controller class in this controller's method
        $print = new printController;
        // Use other controller's method in this controller's method
        $print->printDeposit();

        return redirect('app');

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
}
