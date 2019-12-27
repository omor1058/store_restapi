<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class StoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index()
    {
        $params = request()->query->all();

        // Get one or more specific values from the store
        if (count($params)) {
            if (isset($_GET['keys'])) {
                $keys = explode(",", $params['keys']);
                $stores = Store::whereIn('key', $keys)->get();
                Store::whereIn('key', $keys)->update(['ttl' => date('Y-m-d H:i:s')]);
            } else {
                // set response code - 400 bad request
                return response()->json(['message' => 'Something wrong with URL or parameters'], 400);
            }
        } else {

            // Get all the values of the store.
            $stores = Store::all();
            // Update TTL
            Store::query()->update(['ttl' => date('Y-m-d H:i:s')]);
        }

        $data = [];
        $i = 0;
        foreach ($stores as $key => $value) {
            $data[$value->key] = $value->value;
        }
        if(empty($data)){
            $data[] = 'No data found';
        }
        
        // return result
        return json_encode($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $keys = array_keys($request->all());
        $key_data['key'] = $keys;

        $validator = Validator::make($key_data, [
            'key.*' => 'unique:stores,key',
        ] , [
            'key.*.unique' => 'The :input has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $data = [];
        $i = 0;
        foreach ($request->all() as $key => $value) {
            if (empty($key) || empty($value)) {
                // set response code - 400 bad request
                return response()->json(['message' => 'Something wrong with URL or parameters'], 400);
            }
            $data[$i] = [
                'key' => $key,
                'value' => $value,
            ];

            $i++;
        }
        // Save a value in the store.
        Store::insert($data);
        return response()->json(['message' => 'Data Insert Successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        // Update value in the store
        foreach ($request->all() as $key => $value) {
            $store = Store::where('key', $key)->first();
            if ($store) {
                $store->update(['value' => $value]);
            }
        }

        return response()->json(['message' => 'Data Updated Successfully'], 200);
    }
    
}
