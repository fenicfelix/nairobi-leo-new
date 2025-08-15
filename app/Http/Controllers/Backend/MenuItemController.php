<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MenuItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_order' => 'required',
            'menu_id' =>  'required|exists:menus,id',
            'menu_items' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $menu_items = json_decode($request->post('menu_items'));
        $menu_id = $request->post('menu_id');
        $menu_order = explode(",", $request->post('menu_order'));
        $ids = str_replace('item', '', $request->post('menu_order'));
        $insert = DB::transaction(function () use ($menu_items, $menu_id, $menu_order, $ids) {
            //Delete all and recreate the menu items
            $ids = explode(',', str_replace('item', '', $ids));
            MenuItem::where('menu_id', $menu_id)->forceDelete();
            foreach ($menu_items as $item) {
                $insert = MenuItem::query()->create([
                    'title' => $item->title,
                    'display_title' => $item->display_title,
                    'slug' => Str::slug($item->display_title, "-"),
                    'menu_id' => $menu_id,
                    'type' => $item->type,
                    'url' => $item->url,
                    'reference_id' => $item->reference_id,
                    'order' => array_search('item' . $item->order, $menu_order)
                ]);

                if (!$insert) return false;
            }

            return true;
        }, 2);

        if (!$insert) {
            return response()->json(['status' => status_error, 'message' => "The menu items were not added."], Response::HTTP_OK);
        }


        session()->flash("success", "The menu items have been added successfully.");
        session()->flash("menu", $request->post('menu_id'));
        return response()->json(['status' => status_success, 'message' => "The menu items have been added successfully."], Response::HTTP_OK);
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
}
