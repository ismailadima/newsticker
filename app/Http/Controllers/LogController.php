<?php

namespace App\Http\Controllers;

use App\Newsticker;
use App\User;
use Auth;
use App\Category;
use App\Status;
use App\Unit;
use Illuminate\Http\Request;
use App\Traits\NewstickerTrait;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    use NewstickerTrait;
    use UserTrait;

    private function getValidator($method, Request $request,$id=null)
    {
        if($method == 'searchNewstickers'){
            return \Illuminate\Support\Facades\Validator::make($request->all(),[
                // 'newsticker_date' => 'required',
                // 'list_unit' => 'required',
//                'list_category' => 'required'
            ]);
        }

    }

    public function __construct()
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '20000M');
    }


    public function newstickersIndex(Request $request)
    {
        $is_mcr = $this->isMCR();
        $is_user_inews = $this->isUserInews();

        $messages = "";
        $newsticker_data = null;

        if($request->isMethod('post')){
            $validator = $this->getValidator('searchNewstickers',$request);

            if($validator->fails()){
                $messages = implode(',', array_column($validator->messages()->toArray(), 0));
                return redirect()->back()->with('msg', $messages);
            }else{
                $newsticker_data = self::newstickerSearch($request);
            }
        }

        $conditions_category = [];
        $conditions_unit = [];
        $conditions_category[] = ['is_active', 'Y'];
        $conditions_unit[] = ['is_active', 'Y'];
        $conditions_status[] = ['is_active', 'Y'];

        if (session('unit_id_sess') <> Unit::UNIT_ALL && !$is_mcr)
        {
            $conditions_category[] = ['id', session('category_id_sess')];
            $conditions_unit[] = ['id', session('unit_id_sess')];
        }

        $categories = Category::where($conditions_category)->orderBy('category_name')->get();
        $units = Unit::where($conditions_unit)->orderBy('unit_name')->get();
        $statuses = Status::where($conditions_status)->orderBy('status_name')->get();

        return view('logs.newsticker_index', compact(
            'categories',
            'units',
            'statuses',
            'newsticker_data',
            'request',
            'is_mcr',
            'is_user_inews'
        ));
    }

    private function newstickerSearch($request = null)
    {
        $is_mcr = $this->isMCR();
        $is_user_inews = $this->isUserInews();

        $newstickers = Newsticker::whereNotNull('id');

        if(!empty($request->newsticker_date))
        {
            $arr = explode('-', $request->newsticker_date);
            $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            $newsticker_date_selected = $request->newsticker_date;
            $conditions[] = ['newsticker_date', $newsticker_date];
        }

        if(!empty($request->created_date))
        {
            $created_date = date("Y-m-d", strtotime($request->created_date));
            // $newstickers = $newstickers->whereDate('created_at', $created_date);
            $newstickers = $newstickers->where(function ($query) use($created_date) {
                $query->whereDate('created_at', $created_date)
                      ->orWhereDate('updated_at', '=', $created_date);
            });

        }

        $category_selected = '';

        if(!$is_mcr){
            if (!empty($request->list_category))
            {
                $conditions[] = ['category_id', $request->list_category];
                $category_selected = $request->list_category;
            }
            else
            {
                $conditions[] = ['category_id', session('category_id_sess')];
                $category_selected = session('category_id_sess');
            }
        }else{
            if (!empty($request->list_category) && $request->list_category != Category::CAT_ALL){
                $conditions[] = ['category_id', $request->list_category];
                $category_selected = $request->list_category;
            }
        }

        $status_selected = '';
        if (!empty($request->list_status))
        {
            $conditions[] = ['status_id', $request->list_status];
        }


        $unit_selected = '';
        if (!empty($request->list_unit))
        {
            $conditions[] = ['unit_id', $request->list_unit];
            $unit_selected = $request->list_unit;
        }


        $newstickers = $newstickers->where($conditions)
            ->orderBy('id', 'DESC')
            ->get();

        return $newstickers;
    }


}
