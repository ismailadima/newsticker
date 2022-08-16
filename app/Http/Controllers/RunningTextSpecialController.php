<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelpers;
use App\Helpers\NewstickerHelpers;
use App\LogNewsticker;
use App\User;
use Auth;
use App\Category;
use App\GtvNewstickerInfotainment;
use App\GtvNewstickerNews;
use App\GtvNewstickerPromo;
use App\GtvNewstickerPromo2;
use App\InewsNewstickerInfotainment;
use App\InewsNewstickerNews;
use App\InewsNewstickerPromo;
use App\InewsNewstickerPromo2;
use App\MnctvNewstickerInfotainment;
use App\MnctvNewstickerNews;
use App\MnctvNewstickerPromo;
use App\MnctvNewstickerPromo2;
use App\Newsticker;
use App\NewstickerInfotainment;
use App\RctiNewstickerInfotainment;
use App\RctiNewstickerNews;
use App\RctiNewstickerPromo;
use App\RctiNewstickerPromo2;
use App\Status;
use App\Unit;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Traits\NewstickerTrait;
use App\Http\Library\Serializer;
use App\RunningTextType;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\RunningTextInterface;

class RunningTextSpecialController extends Controller
{
    protected $runningTextInterface;
    use NewstickerTrait;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(RunningTextInterface $runningTextInterface)
    {
        $this->runningTextInterface = $runningTextInterface;
    }

    private function getValidator($method, Request $request, $id=null)
    {
        if($method == 'contentStore'){
            return Validator::make($request->all(),[
                'program_tv_name' => 'required|string|max:255',
                'category_id' => 'required',
                'newsticker_date' => 'required',
                'file_upload' => 'required|mimes:txt',
            ]);
        }else if($method == 'contentUpdate'){
            return Validator::make($request->all(),[
                'program_tv_name' => 'required|string|max:255',
                'id' => 'required',
                'category_id' => 'required',
                'newsticker_date' => 'required'
            ]);
        }

    }

    
    public function index(Request $request)
    {
        $is_mcr = Auth::user()->is_mcr == User::IS_MCR ? true : false;
        $is_admin = Auth::user()->is_admin == User::IS_ADMIN ? true : false;
        $is_promo = Auth::user()->category_id == Category::CAT_PROMO ? true : false;

        $conditions = [];

        $newsticker_date = date('Y-m-d');
        $newsticker_date_selected = date('d-m-Y');

        if(!empty($request->newsticker_date))
        {
            $arr = explode('-', $request->newsticker_date);
            $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            $newsticker_date_selected = $request->newsticker_date;
        }
        
        $conditions[] = ['newsticker_date', $newsticker_date];
        $conditions[] = ['rtext_type_id', '!=', RunningTextType::TYPE_REGULAR_PROGRAM];

        $category_selected = '';

        //Hanya untuk user bukan MCR
        if(!$is_mcr && !$is_admin){
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
            $status_selected = $request->list_status;
            $conditions[] = ['status_id', $request->list_status];
        }


        $unit_selected = '';
        //searching
        if($request->method() == 'POST' && !empty($request->list_unit)){
            $unit_selected = $request->list_unit;
            $conditions[] = ['unit_id', $unit_selected];
        }else{
            $unit_selected = $is_mcr && $is_admin ? '' : Auth::user()->unit_id;
            $conditions[] = ['unit_id', $unit_selected];
        }

        $newstickers = Newsticker::where($conditions)
            ->orderBy('id', 'DESC')
            ->get();

        $conditions_category = [];
        $conditions_unit = [];
        $conditions_category = [
            ['is_active', 'Y'],
            ['id' , '!=', Category::CAT_ALL]
        ];
        $conditions_unit = [
            ['is_active', 'Y'],
            ['id' , '!=', Unit::UNIT_ALL]
        ];
        $conditions_status = [
            ['is_active', 'Y'],
            ['id' , '!=', Newsticker::STATUS_UNPUBLISH]
        ];

        if (session('unit_id_sess') <> Unit::UNIT_ALL && $is_mcr == false)
        {
            $conditions_category[] = ['id', session('category_id_sess')];
            $conditions_unit[] = ['id', session('unit_id_sess')];
        }

        $categories = Category::where($conditions_category)->orderBy('category_name')->get();
        $units = Unit::where($conditions_unit)->orderBy('unit_name')->get();
        $statuses = Status::where($conditions_status)->orderBy('id')->get();


        $upload_active = '';
        $list_active = 'active';

        ///////Latest Data Newsticker For MCR
        $latest_data = $is_mcr == true ? $this->latestData() : null;

        ///////view 
        //$view = $is_mcr == true ? "newsticker.index_mcr" : "newsticker.index";
        $view = "rt_special.index";

        return view($view, compact(
            'newstickers',
            'categories',
            'units',
            'statuses',
            'newsticker_date_selected',
            'category_selected',
            'unit_selected',
            'status_selected',
            'is_mcr',
            'is_promo',
            'upload_active',
            'list_active',
            'latest_data'
        ));

    }

    public function latestData()
    {
        $user_auth = Auth::user();
        $unit = $user_auth->unit_id;
        $category = $user_auth->category_id;
        $is_mcr = $user_auth->is_mcr;
        $datas = [];

        if($unit == Unit::UNIT_ALL){
            $data_gtv = $this->setLatestData(Unit::UNIT_GTV);
            $data_mnctv = $this->setLatestData(Unit::UNIT_MNCTV);
            $data_rcti = $this->setLatestData(Unit::UNIT_RCTI);

            $datas[Unit::UNIT_GTV] = $data_gtv;
            $datas[Unit::UNIT_MNCTV] = $data_mnctv;
            $datas[Unit::UNIT_RCTI] = $data_rcti;
        }else{
            $data = $this->setLatestData($unit);
            $datas[$unit] = $data;
        }

        return $datas;
    }

    private function setLatestData($unit)
    {
        $data = [
            'news' => null,
            'infotainment' => null,
            'promo' => [
                'promo1' => null,
                'promo2' => null
            ]
        ];
        
        switch ($unit) {
            case Unit::UNIT_GTV:
                $news = !empty(GtvNewstickerNews::first()->content) ? GtvNewstickerNews::first()->content : '';
                $infotainment = !empty(GtvNewstickerInfotainment::first()->content) ? GtvNewstickerInfotainment::first()->content : '';
                $promo1 = !empty(GtvNewstickerPromo::first()->content) ? GtvNewstickerPromo::first()->content : '';
                $promo2 = !empty(GtvNewstickerPromo2::first()->content) ? GtvNewstickerPromo2::first()->content : '';
 
                $data['news'] = $news;
                $data['infotainment'] = $infotainment;
                $data['promo']['promo1'] = $promo1;
                $data['promo']['promo2'] = $promo2;
             break;

             case Unit::UNIT_INEWS:
                $news = !empty(InewsNewstickerNews::first()->content) ? InewsNewstickerNews::first()->content : '';
                $infotainment = !empty(InewsNewstickerInfotainment::first()->content) ? InewsNewstickerInfotainment::first()->content : '';
                $promo1 = !empty(InewsNewstickerPromo::first()->content) ? InewsNewstickerPromo::first()->content : '';
                $promo2 = !empty(InewsNewstickerPromo2::first()->content) ? InewsNewstickerPromo2::first()->content : '';
 
                $data['news'] = $news;
                $data['infotainment'] = $infotainment;
                $data['promo']['promo1'] = $promo1;
                $data['promo']['promo2'] = $promo2;
             break;

             case Unit::UNIT_MNCTV:
                $news = !empty(MnctvNewstickerNews::first()->content) ? MnctvNewstickerNews::first()->content : '';
                $infotainment = !empty(MnctvNewstickerInfotainment::first()->content) ? MnctvNewstickerInfotainment::first()->content : '';
                $promo1 = !empty(MnctvNewstickerPromo::first()->content) ? MnctvNewstickerPromo::first()->content : '';
                $promo2 = !empty(MnctvNewstickerPromo2::first()->content) ? MnctvNewstickerPromo2::first()->content : '';
 
                $data['news'] = $news;
                $data['infotainment'] = $infotainment;
                $data['promo']['promo1'] = $promo1;
                $data['promo']['promo2'] = $promo2;
             break;

             case Unit::UNIT_RCTI:
               $news = !empty(RctiNewstickerNews::first()->content) ? RctiNewstickerNews::first()->content : '';
               $infotainment = !empty(RctiNewstickerInfotainment::first()->content) ? RctiNewstickerInfotainment::first()->content : '';
               $promo1 = !empty(RctiNewstickerPromo::first()->content) ? RctiNewstickerPromo::first()->content : '';
               $promo2 = !empty(RctiNewstickerPromo2::first()->content) ? RctiNewstickerPromo2::first()->content : '';

               $data['news'] = $news;
               $data['infotainment'] = $infotainment;
               $data['promo']['promo1'] = $promo1;
               $data['promo']['promo2'] = $promo2;
             break;
            
         }

        return $data;
    }


    public function search(Request $request)
    {
        $arr = explode('-', $request->newsticker_date);
        $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        $conditions = [];
        $conditions[] = ['newsticker_date', $newsticker_date];

        if (!empty($request->list_category))
        {
            $conditions[] = ['category_id', $request->list_category];
        }
        else
        {
            $conditions[] = ['category_id', session('category_id_sess')];
        }

        if (!empty($request->list_status))
        {
            if ($request->list_status == 'Y')
            {
                $status_id = '1';
            }
            else
            {
                $status_id = '0';
            }

            $conditions[] = ['status_id', $status_id];
        }

        $newstickers = Newsticker::where($conditions)->get();

        return redirect('/newsticker', compact('newstickers'));
    }

    
    
    public function create()
    {
        $status = '';
        $message = '';

        $categories = Category::where('id', session('category_id_sess'))->orderBy('category_name')->get();
        $upload_active = 'active';
        $list_active = '';

        return view('rt_special.create', compact('categories', 'status', 'message', 'upload_active', 'list_active'));
    }



    public function store(Request $request)
    {
        $statusCode = Response::HTTP_BAD_REQUEST;
        $messages = '';
        $status = false;
        $validator = $this->getValidator('contentStore',$request);

        if($validator->fails()){
            $messages = implode(',', array_column($validator->messages()->toArray(), 0));
            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        }else{
            $storeContents = $this->runningTextInterface->storeRunningtext($request, true);

            if($storeContents !== false){
                //Create Logs
                $this->createLog(LogNewsticker::STATUS_CREATE, $storeContents);

                $statusCode = Response::HTTP_OK;
                $status = true;
            }

            $messages = "Berhasil Simpan Data";
            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        }
    }

    
    public function show(Newsticker $newsticker)
    {
        //
    }

    

    public function edit(Newsticker $newsticker)
    {
       
        $content_split_arr = NewstickerHelpers::splitContents($newsticker);

        // dd($list, $content);
        $upload_active = 'active';
        $list_active = '';

        $categories = Category::where('id', session('category_id_sess'))->orderBy('category_name')->get();

        return view('rt_special.edit', compact('newsticker', 'categories', 'upload_active', 'list_active', 'content_split_arr'));
    }

    
    
    public function update(Request $request, Newsticker $newsticker)
    {
        $statusCode = Response::HTTP_BAD_REQUEST;
        $messages = '';
        $status = false;
        $validator = $this->getValidator('contentUpdate',$request);

        if($validator->fails()){
            $messages = implode(',', array_column($validator->messages()->toArray(), 0));
            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        }else{
            $updateRunningText = $this->runningTextInterface->updateRunningText($newsticker, $request, true);

            if($updateRunningText == true){
                $messages = "Berhasil Update Data";
                $status = true;
                $statusCode = Response::HTTP_OK;
            }

            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        } 

    }


    public function publish(Request $request)
    {
        $newsticker = Newsticker::where('id', $request->newsticker_id)->first();
        dd($newsticker); //sementara belum dilanjut untuk special program
        $result = [];

        if (count($newsticker) > 0)
        {
            $result['status'] = '1';

            $category_id = $newsticker->category_id;
            $newsticker_date = $newsticker->newsticker_date;

            //Distribute data content masing2 kategori
            $this->distributeContent($category_id, Auth::user()->unit_id, $newsticker->content, $newsticker->content2);

            //Unpublish data yang lain (tanggal dan kategori yang sama pada suatu unit)
            Newsticker::where([
                ['id', '<>', $newsticker->id],
                ['newsticker_date', $newsticker_date],
                ['category_id', $category_id],
                ['status_id', '<>', Newsticker::STATUS_NONAKTIF],
                ['unit_id', Auth::user()->unit_id]
            ])->update([
                'status_id' => Newsticker::STATUS_UNPUBLISH
            ]);

            //Change status publish
            $newsticker->status_id = Newsticker::STATUS_PUBLISH;
            $newsticker->save();

            //Create Logs
            $this->createLog(LogNewsticker::STATUS_PUBLISH, $newsticker);

        }
        else
        {
            $result['status'] = '0';
        }

        return response()->json($result);

    }


    public function nonactiveData(Newsticker $data, Request $request)
    {
        if(empty($data)){
            $messages = "Gagal Non Aktifkan Data";
            $resource = Serializer::serializeItem(false,$messages);
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            return response()->json($resource,$statusCode);
        }

        DB::beginTransaction();
        try {
            $data->status_id = Newsticker::STATUS_NONAKTIF;
            $data->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        }

        $messages = "Berhasil Non Aktifkan Data";
        $resource = Serializer::serializeItem(true,$messages);
        $statusCode = Response::HTTP_OK;
        return response()->json($resource,$statusCode);
    }

    public function checkExistingDateNewsticker($date = null)
    {
        $arr = explode('-', $date);
        $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        $newsticker = Newsticker::where('newsticker_date', $newsticker_date)
            ->where('unit_id', Auth::user()->unit_id)
            ->where('rtext_type_id', RunningTextType::TYPE_SPECIAL_PROGRAM)
            ->where('category_id', Auth::user()->category_id)
            ->get();
        $res_data = [
            'data' => $newsticker,
            'count' => $newsticker->count()
        ];

        $messages = "Data ".$newsticker_date;
        $resource = Serializer::serializeItem(true, $messages, $res_data);
        $statusCode = Response::HTTP_OK;
        return response()->json($resource,$statusCode);
    }

    public function updateLine(Request $request, Newsticker $newsticker)
    {
        $is_published = ($newsticker->status_id == Newsticker::STATUS_PUBLISH) ? true : false;
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $statusRes = false;

        $contents1 = !empty($request->contentsArr1) ? $request->contentsArr1 : null;
        $contents2 = !empty($request->contentsArr2) ? $request->contentsArr2 : null;

        if(empty($contents1) && empty($contents2)){
            $messages = "Data Tidak Ada";
            $resource = Serializer::serializeItem(false,$messages);
            return response()->json($resource,$statusCode);
        }

        $updateLine = $this->runningTextInterface->updateLineRunningText($newsticker, $contents1, $contents2);
        if($updateLine == true){
            $statusCode = Response::HTTP_OK;
            $statusRes = true;
            $messages = ($is_published) ? "Berhasil update line data, Harap melakukan publish ulang!" : "Berhasil update line data.";
        }else{
            $messages = $updateLine; //response berupa message dari repository
        }

        $resource = Serializer::serializeItem($statusRes,$messages);
        return response()->json($resource,$statusCode);
    }

    public function deleteLine(Request $request, Newsticker $newsticker)
    {
        $is_published = ($newsticker->status_id == Newsticker::STATUS_PUBLISH) ? true : false;
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $statusRes = false;

        if(empty($newsticker)){
            $messages = "Tidak mendapatkan data";
            $resource = Serializer::serializeItem(false,$messages);
            return response()->json($resource,$statusCode);
        }

        $deleteLineRunningText = $this->runningTextInterface->deleteLineRunningText($newsticker, $request);
        if($deleteLineRunningText == true){
            $statusCode = Response::HTTP_OK;
            $statusRes = true;
            $messages = ($is_published) ? "Berhasil Delete line data, Harap melakukan publish ulang!" : "Berhasil Delete line data";
        }else{
            $messages = $deleteLineRunningText;
        }

        $resource = Serializer::serializeItem($statusRes,$messages);
        return response()->json($resource,$statusCode);
    }

}
