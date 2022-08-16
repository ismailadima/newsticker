<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelpers;
use App\Helpers\NewstickerHelpers;
use App\LogNewsticker;
use App\User;
use Auth;
use App\Category;
use App\DeleteSchedule;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\RunningTextInterface;
use App\MpiNewstickerNews;
use App\RctiNewstickerSergap;
use App\RunningTextType;

class NewstickerController extends Controller
{
    protected $deletedSchedule = null; 

    protected $runningTextInterface;
    
    use NewstickerTrait;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(
        RunningTextInterface $runningTextInterface,
        DeleteSchedule $deletedSchedule
    ){
        $this->runningTextInterface = $runningTextInterface;
        $this->deletedSchedule = $deletedSchedule;
    }

    private function getValidator($method, Request $request, $id=null)
    {
        if($method == 'contentStore'){
            return Validator::make($request->all(),[
                'category_id' => 'required',
                'newsticker_date' => 'required',
                'file_upload' => 'required|mimes:txt',
            ]);
        }else if($method == 'contentUpdate'){
            return Validator::make($request->all(),[
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
            ->where(function ($query) {
                $query->where('rtext_type_id', RunningTextType::TYPE_REGULAR_PROGRAM)
                    ->orWhereNull('rtext_type_id');
            })
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

        if (session('unit_id_sess') <> Unit::UNIT_ALL)
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
        // $view = $is_mcr == true ? "newsticker.index_mcr" : "newsticker.index";
        $view = "newsticker.index";

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
            $data_mpi = $this->setLatestData(Unit::UNIT_MPI);

            $datas[Unit::UNIT_GTV] = $data_gtv;
            $datas[Unit::UNIT_MNCTV] = $data_mnctv;
            $datas[Unit::UNIT_RCTI] = $data_rcti;
            $datas[Unit::UNIT_MPI] = $data_mpi;
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
               $sergap = !empty(RctiNewstickerSergap::first()->content) ? RctiNewstickerSergap::first()->content : '';

               $data['news'] = $news;
               $data['infotainment'] = $infotainment;
               $data['promo']['promo1'] = $promo1;
               $data['promo']['promo2'] = $promo2;
               $data['sergap'] = $sergap;
             break;

             
             case Unit::UNIT_MPI:
                $news = !empty(MpiNewstickerNews::first()->content) ? MpiNewstickerNews::first()->content : '';
 
                $data['news'] = $news;
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

        return view('newsticker.create', compact('categories', 'status', 'message', 'upload_active', 'list_active'));
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
            //SEMENTARA OFF
            // $validatedCountContent = $this->runningTextInterface->validatedCountContent($request); 
            // if($validatedCountContent == false){
            //     $messages = "Panjang karakter tidak boleh lebih dari 2000 karakter";
            //     $resource = Serializer::serializeItem(false, $messages);
            //     return response()->json($resource,$statusCode);
            // }

            $storeContents = $this->runningTextInterface->storeRunningtext($request, false);

            if($storeContents !== false){
                //Create Logs
                $this->createLog(LogNewsticker::STATUS_CREATE, $storeContents);
                //Create Delete Schedule
                $this->createDeleteSchedule($storeContents);
                $messages = "Berhasil Simpan Data";
                $statusCode = Response::HTTP_OK;
                $status = true;
            }else{
                $messages = "Gagal Simpan Data, Harap upload file dengan jenis text (.txt)";
            }

            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        }
    }

    public function storeOLD(Request $request)
    {
        $category_id = $request->category_id;
        
        $arr = explode('-', $request->newsticker_date);
        $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        $unit_id = session('unit_id_sess');

        $this->validate($request, [
            'file_upload' => 'required|mimes:txt'
        ]);

        $file = $request->file('file_upload');
        $file2 = $request->file('file_upload2');

        $content = '';
        $content2 = '';

        if (!empty($file))
        {
            $filetxt = file_get_contents($file->getRealPath());

        //    $content = htmlspecialchars(preg_replace("/[\x93|\x94]/", "\x22", $filetxt), ENT_QUOTES);
        //    $content = htmlspecialchars(preg_replace("/[\xEF|\xBB|\xBF]/", "", $content), ENT_QUOTES);
            $content = NewstickerHelpers::cleanTextInput($filetxt);
        }

        if (!empty($file2) && !empty($category_id))
        {
            $this->validate($request, [
                'file_upload2' => 'required|mimes:txt'
            ]);
    
            if ($category_id == '3')
            {
                $filetxt2 = file_get_contents($file2->getRealPath());

            //    $content2 = htmlspecialchars(preg_replace("/[\x93|\x94]/", "\x22", $filetxt2), ENT_QUOTES);
            //    $content2 = htmlspecialchars(preg_replace("/[\xEF|\xBB|\xBF]/", "", $content2), ENT_QUOTES);
                $content2 = NewstickerHelpers::cleanTextInput($filetxt2);
            }
        }

            //     Newsticker::where([
            //        ['unit_id', $unit_id],
            //        ['category_id', $category_id],
            //        ['newsticker_date', $newsticker_date]
            //    ])->delete();

            //    $created = date('Y-m-d H:i:s');
            //    $data = Newsticker::insert([
            //        'content' => $content,
            //        'content2' => $content2,
            //        'category_id' => $category_id,
            //        'newsticker_date' => $newsticker_date,
            //        'status_id' => '0',
            //        'unit_id' => $unit_id,
            //        'created_at' => $created,
            //        'created_by' => session('user_id_sess')
            //    ]);

        $data = [
            'content' => $content,
            'content2' => $content2,
            'category_id' => $category_id,
            'newsticker_date' => $newsticker_date,
            'status_id' => Newsticker::STATUS_UNPUBLISH,
            'unit_id' => $unit_id,
        ];

        $save_data = new Newsticker();
        $save_data->fill($data);
        $save_data->save();

        //Create Logs
        $this->createLog(LogNewsticker::STATUS_CREATE, $save_data);

        $result['status'] = 1;

        return response()->json($result);
    }

    
    public function show(Newsticker $newsticker)
    {
        //
    }

    

    public function edit(Newsticker $newsticker)
    {
        $content_split_arr = NewstickerHelpers::splitContents($newsticker);
        $deleted_id = NewstickerHelpers::buildDeletedContents($newsticker);

        $upload_active = 'active';
        $list_active = '';
        $is_mcr = Auth::user()->is_mcr == User::IS_MCR ? true : false;
        $is_admin = Auth::user()->is_admin == User::IS_ADMIN ? true : false;

        if($is_admin || $is_mcr){
            $categories = Category::orderBy('category_name')->get();
        }else{
            $categories = Category::where('id', session('category_id_sess'))->orderBy('category_name')->get();
        }

        return view('newsticker.edit', compact('newsticker', 'categories', 'upload_active', 'list_active', 'content_split_arr','deleted_id'));
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
            $isNewstickerHaveTimeDeleted = $this->deletedSchedule->isNewstickerHaveTimeDeleted($newsticker->id);
            $updateRunningText = $this->runningTextInterface->updateRunningText($newsticker, $request, false, $isNewstickerHaveTimeDeleted);

            if($updateRunningText === true){
                $messages = "Berhasil Update Data";
                $status = true;
                $statusCode = Response::HTTP_OK;
            }else{
                $messages = $updateRunningText;
            }

            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        } 
    }
    
    public function updateOLD(Request $request, Newsticker $newsticker)
    {
        if (!empty($request->id))
        {
            $is_mcr = Auth::user()->is_mcr == User::IS_MCR ? true : false;
            $is_admin = Auth::user()->is_admin == User::IS_ADMIN ? true : false;
            $category_id = session('category_id_sess');

            $arr = explode('-', $request->newsticker_date);
            $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

            $unit_id = session('unit_id_sess');

            $content2 = '';

            if($request->is_upload == 'Y')
            {
                $status_log_newsticker = LogNewsticker::STATUS_UPDATE_UPLOAD;
                $file = $request->file('file_upload');
                $file2 = $request->file('file_upload2');

                if (!empty($file))
                {
                    // $content = htmlspecialchars(file_get_contents($file->getRealPath()), ENT_QUOTES);

                    $filetxt = file_get_contents($file->getRealPath());

                    //$content = htmlspecialchars(preg_replace("/[\x93|\x94]/", "\x22", $filetxt), ENT_QUOTES);
                    //$content = htmlspecialchars(preg_replace("/[\xEF|\xBB|\xBF]/", "", $content), ENT_QUOTES);

                    $content = NewstickerHelpers::cleanTextInput($filetxt);
                }

                if (!empty($file2))
                {
                    // $content2 = htmlspecialchars(file_get_contents($file2->getRealPath()), ENT_QUOTES);

                    $filetxt = file_get_contents($file2->getRealPath());

                    //$content = htmlspecialchars(preg_replace("/[\x93|\x94]/", "\x22", $filetxt), ENT_QUOTES);
                    //$content2 = htmlspecialchars(preg_replace("/[\xEF|\xBB|\xBF]/", "", $content), ENT_QUOTES);

                    $content2 = NewstickerHelpers::cleanTextInput($filetxt);
                }

            }
            else
            {
                $status_log_newsticker = LogNewsticker::STATUS_UPDATE;

                // $content = htmlspecialchars($request->content, ENT_QUOTES);
                // $content2 = htmlspecialchars($request->content2, ENT_QUOTES);

                //$content = htmlspecialchars(preg_replace("/[\x93|\x94]/", "\x22", $request->content), ENT_QUOTES);
                //$content = htmlspecialchars(preg_replace("/[\xEF|\xBB|\xBF]/", "", $content), ENT_QUOTES);

                //$content2 = htmlspecialchars(preg_replace("/[\x93|\x94]/", "\x22", $request->content2), ENT_QUOTES);
                //$content2 = htmlspecialchars(preg_replace("/[\xEF|\xBB|\xBF]/", "", $content2), ENT_QUOTES);

                //Check Update From Line Or All VIew
                $is_split_content1 = GlobalHelpers::json_validate($request->content);
                $is_split_content2 = GlobalHelpers::json_validate($request->content2);

                if($is_split_content1){
                    $contents1_decode = json_decode($request->content);
                    $content = NewstickerHelpers::mergeContents($newsticker, $contents1_decode);
                }else{
                    $content = NewstickerHelpers::cleanTextInput($request->content);
                }

                if($is_split_content2){
                    $contents2_decode = json_decode($request->content2);
                    $content2 = NewstickerHelpers::mergeContents($newsticker, $contents2_decode);
                }else{
                    $content2 = NewstickerHelpers::cleanTextInput($request->content2);
                }
            }


            if (!empty($content) && !empty($category_id))
            {
                $data = [
                    'content' => $content,
                    'content2' => $content2,
                    'newsticker_date' => $newsticker_date,
                    'unit_id' => Auth::user()->unit_id
                ];

                if(!$is_mcr && !$is_admin){
                    $data['category_id'] = $category_id;
                }

                $update_data = $newsticker->fill($data);

                //Change status to unpublish
                $update_data->status_id = Newsticker::STATUS_UNPUBLISH;

                $update_data->save();

                // $newstickers = Newsticker::where([
                //    ['category_id', $category_id],
                //    ['newsticker_date', $newsticker_date],
                //    ['unit_id', session('unit_id_sess')]
                // ])->get();

                //Create Logs
                $this->createLog($status_log_newsticker, $update_data);

                $result['status'] = 1;

                return response()->json($result);
            }
            else
            {
                $result['status'] = 2;

                return response()->json($result);
            }
                
            

        }

        

    }


    public function insert_content(
        $category_id = null,
        $unit_id = null,
        $content = null,
        $content2= null
    ){
        $content = NewstickerHelpers::decodeHtmlSpecialChars($content);
        $content2 = NewstickerHelpers::decodeHtmlSpecialChars($content2);

        switch ($category_id)
        {
            // ============================================
            // Jika Category ID = 1 atau Infotainment;
            // ============================================
            case Category::CAT_INFOTAINMENT:


            switch ($unit_id)
                {
                    case '1':

                        GtvNewstickerInfotainment::where('id', '<>', null)->delete();

                        GtvNewstickerInfotainment::insert([
                            'content' => $content
                        ]);

                        break;
                    
                    case '2':

                        InewsNewstickerInfotainment::where('id', '<>', null)->delete();

                        InewsNewstickerInfotainment::insert([
                            'content' => $content
                        ]);

                        break;
                    
                    case '3':

                        MnctvNewstickerInfotainment::where('id', '<>', null)->delete();

                        MnctvNewstickerInfotainment::insert([
                            'content' => $content
                        ]);

                        break;


                    case '4':

                        RctiNewstickerInfotainment::where('id', '<>', null)->delete();

                        RctiNewstickerInfotainment::insert([
                            'content' => $content
                        ]);

                        break;
                }
                
                
                break;
            

            
            // ============================================
            // Jika Category ID = 2 atau News;
            // ============================================

            case Category::CAT_NEWS:

            switch ($unit_id) {
                    case '1':

                        GtvNewstickerNews::where('id', '<>', null)->delete();

                        GtvNewstickerNews::insert([
                            'content' => $content
                        ]);

                        break;
                    
                    case '2':

                        InewsNewstickerNews::where('id', '<>', null)->delete();

                        InewsNewstickerNews::insert([
                            'content' => $content
                        ]);

                        break;
                    
                    case '3':

                        MnctvNewstickerNews::where('id', '<>', null)->delete();

                        MnctvNewstickerNews::insert([
                            'content' => $content
                        ]);

                        break;


                    case '4':

                        RctiNewstickerNews::where('id', '<>', null)->delete();

                        RctiNewstickerNews::insert([
                            'content' => $content
                        ]);

                        break;
                }
                
                break;


            // ============================================
            // Jika Category ID = 3 atau Promo;
            // ============================================
            case Category::CAT_PROMO:
                switch ($unit_id) {
                    case '1':

                        GtvNewstickerPromo::where('id', '<>', null)->delete();

                        GtvNewstickerPromo::insert([
                            'content' => $content
                        ]);

                        if (!empty($content2))
                        {
                            GtvNewstickerPromo2::where('id', '<>', null)->delete();

                            GtvNewstickerPromo2::insert([
                                'content' => $content2
                            ]);

                        }

                        break;
                    
                    case '2':

                        InewsNewstickerPromo::where('id', '<>', null)->delete();

                        InewsNewstickerPromo::insert([
                            'content' => $content
                        ]);

                        if (!empty($content2))
                        {
                            InewsNewstickerPromo2::where('id', '<>', null)->delete();

                            InewsNewstickerPromo2::insert([
                                'content' => $content2
                            ]);

                        }

                        break;
                    
                    case '3':

                        MnctvNewstickerPromo::where('id', '<>', null)->delete();

                        MnctvNewstickerPromo::insert([
                            'content' => $content
                        ]);

                        if (!empty($content2))
                        {
                            MnctvNewstickerPromo2::where('id', '<>', null)->delete();

                            MnctvNewstickerPromo2::insert([
                                'content' => $content2
                            ]);

                        }

                        break;


                    case '4':

                        RctiNewstickerPromo::where('id', '<>', null)->delete();

                        RctiNewstickerPromo::insert([
                            'content' => $content
                        ]);

                        if (!empty($content2))
                        {
                            RctiNewstickerPromo2::where('id', '<>', null)->delete();

                            RctiNewstickerPromo2::insert([
                                'content' => $content2
                            ]);

                        }


                        break;
                }
                
                
                break;

        }
    }

    public function publish(Request $request)
    {
        $statusCode = Response::HTTP_BAD_REQUEST;
        $messages = '';
        $status = false;

        $newsticker = Newsticker::where('id', $request->newsticker_id)->first();
        if(count($newsticker) < 1){
            $messages = "Data tidak ditemukan";
            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        }

        $publish_runningtext_repository = $this->runningTextInterface->publishRunningText($newsticker);
        if($publish_runningtext_repository === true){
            $messages = "Berhasil Update Data";
            $status = true;
            $statusCode = Response::HTTP_OK;
        }else{
            $messages = $publish_runningtext_repository;
        }

        $resource = Serializer::serializeItem($status, $messages);
        return response()->json($resource,$statusCode);
    }

    public function publishOLD(Request $request)
    {
        $newsticker = Newsticker::where('id', $request->newsticker_id)->first();

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


    public function publish_today(Request $request)
    {
        $newsticker = Newsticker::where([
            ['newsticker_date', date('Y-m-d')],
            ['category_id', session('category_id_sess')],
            ['unit_id', session('unit_id_sess')]
        ])->first();

        $result = [];

        if (count($newsticker) > 0)
        {
            //Distribute data content masing2 kategori
            $this->insert_content($newsticker->category_id, $newsticker->unit_id, $newsticker->content, $newsticker->content2);
            
            $result['status'] = '1';
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
            ->where('rtext_type_id', RunningTextType::TYPE_REGULAR_PROGRAM)
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

        $updateLine = $this->runningTextInterface->updateLineRunningText($newsticker, $contents1, $contents2, $request);
        if($updateLine === true){
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
        if($deleteLineRunningText === true){
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
