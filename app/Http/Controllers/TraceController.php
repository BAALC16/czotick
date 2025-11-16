<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trace;

class TraceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $traces = Trace::orderBy('created_at', 'desc');
        if(!empty(request('q'))) {
            $trace->where('trace', 'like', '%'.request('q').'%');
        }
        if(!empty(request('sort'))) {
            $sort = json_decode(str_replace("'", '"', request('sort')), true);
            if(null !== $sort && $this->validateSortInput($sort)) {
                $traces->orderBy($sort['col'], $sort['order']);
            }
        }

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Traces", 'description' => "Consultation de la page \"Traces\""]);
        $trace->save();

        return view('backend.traces.index', [
            'traces' => $traces->paginate(10),
        ]);
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
}
