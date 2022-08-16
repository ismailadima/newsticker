<?php

namespace App\Http\Controllers;

use App\Category;
use App\Helpers\NewstickerHelpers;
use App\Http\Library\Serializer;
use App\Interfaces\RunningTextInewsInterface;
use App\LogNewsticker;
use App\Newsticker;
use App\RunningTextType;
use App\SettingFormatInews;
use App\Traits\NewstickerTrait;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\GtvNewstickerInfotainment;
use App\GtvNewstickerNews;
use App\GtvNewstickerPromo;
use App\GtvNewstickerPromo2;
use App\InewsNewstickerAll;
use App\MnctvNewstickerInfotainment;
use App\MnctvNewstickerNews;
use App\MnctvNewstickerPromo;
use App\MnctvNewstickerPromo2;
use App\RctiNewstickerInfotainment;
use App\RctiNewstickerNews;
use App\RctiNewstickerPromo;
use App\RctiNewstickerPromo2;
use App\MpiNewstickerNews;

class NewstickerInewsController extends Controller
{
    use NewstickerTrait;

    protected $runningTextInewsInterface;
    
    /**
     * Create a new constructor for this controller
     */
    public function __construct(RunningTextInewsInterface $runningTextInewsInterface)
    {
        $this->runningTextInewsInterface = $runningTextInewsInterface;
    }

    private function getValidator($method, Request $request, $id=null)
    {
        if($method == 'contentStore'){
            return Validator::make($request->all(),[
                'category_id' => 'required',
                'file_upload' => 'required|mimes:txt',
            ]);
        }else if($method == 'contentUpdate'){
            return Validator::make($request->all(),[
                'id' => 'required',
                'category_id' => 'required',
            ]);
        }

    }

    public function index()
    {
        $is_mcr = Auth::user()->is_mcr == User::IS_MCR ? true : false;
        $is_admin = Auth::user()->is_admin == User::IS_ADMIN ? true : false;
        $is_promo = Auth::user()->category_id == Category::CAT_PROMO ? true : false;

        $user = Auth::user();
        $category_id_user = $user->category_id;

        $categories = SettingFormatInews::with('category')->get()->pluck('category');
        $newsticker_category = Newsticker::where('rtext_type_id', RunningTextType::TYPE_REGULAR_PROGRAM)
            ->where('category_id', $category_id_user)
            ->where('unit_id', Unit::UNIT_INEWS)
            ->orderBy('id', 'DESC')
            ->first();
        
        ///////Latest Data Newsticker For MCR
        $latest_data = $is_mcr == true ? $this->latestData() : null;

        return view('newsticker_inews.index', compact(
            'is_mcr',
            'is_promo',
            'is_admin',
            'user',
            'categories',
            'newsticker_category',
            'latest_data'
        ));
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
            $storeContents = $this->runningTextInewsInterface->storeRunningtext($request);
            $newsticker_saved = $storeContents['data'];
            $status_store = $storeContents['status'];

            if($status_store == true){
                //Merge With Antoher Category, tidak ada flagging publish lagi
                $publish_content = $this->runningTextInewsInterface->publishRunningText();
                $status_publish_content = $publish_content['status'];

                //build file text
                $this->runningTextInewsInterface->buildFileText();

                //Create Logs
                $this->createLog(LogNewsticker::STATUS_CREATE, $newsticker_saved);
                $this->createLog(LogNewsticker::STATUS_PUBLISH, $newsticker_saved);

                $messages = "Berhasil Simpan Data";
                $statusCode = Response::HTTP_OK;
                $status = true;
            }else{
                $messages = "Gagal Simpan Data, Pastikan upload file dengan jenis file text (.txt)";
            }

            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        }
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

        return view('newsticker_inews.edit', compact('newsticker', 'categories', 'upload_active', 'list_active', 'content_split_arr','deleted_id'));
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
            $updateRunningText = $this->runningTextInewsInterface->updateRunningText($newsticker, $request);
            $status_update = $updateRunningText['status'];

            if($status_update === true){
                //Merge With Antoher Category, tidak ada flagging publish lagi
                $publish_content = $this->runningTextInewsInterface->publishRunningText();
                $status_publish_content = $publish_content['status'];

                $messages = "Berhasil Update Data";
                $status = true;
                $statusCode = Response::HTTP_OK;

                //build file text
                $this->runningTextInewsInterface->buildFileText();

                //Create Logs
                $this->createLog(LogNewsticker::STATUS_PUBLISH, $newsticker);
            }else{
                $messages = "Gagal Update Data";
            }

            $resource = Serializer::serializeItem($status, $messages);
            return response()->json($resource,$statusCode);
        } 
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

        $updateLine = $this->runningTextInewsInterface->updateLineRunningText($newsticker, $contents1, $contents2, $request);
        $status_update = $updateLine['status'];

        if($status_update === true){
            //Merge With Antoher Category, tidak ada flagging publish lagi
            $publish_content = $this->runningTextInewsInterface->publishRunningText();
            $status_publish_content = $publish_content['status'];

            $messages = "Berhasil Update Data";
            $statusRes = true;
            $statusCode = Response::HTTP_OK;
            //build file text
            $this->runningTextInewsInterface->buildFileText();
            //Create Logs
            $this->createLog(LogNewsticker::STATUS_PUBLISH, $newsticker);
        }else{
            $messages = "Gagal Update Data";
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

        $deleteLineRunningText = $this->runningTextInewsInterface->deleteLineRunningText($newsticker, $request);
        $status_delete = $deleteLineRunningText['status'];

        if($status_delete === true){
            //Merge With Antoher Category, tidak ada flagging publish lagi
            $publish_content = $this->runningTextInewsInterface->publishRunningText();
            $status_publish_content = $publish_content['status'];

            $messages = "Berhasil Delete Data";
            $statusRes = true;
            $statusCode = Response::HTTP_OK;
            
            //build file text
            $this->runningTextInewsInterface->buildFileText();
            //Create Logs
            $this->createLog(LogNewsticker::STATUS_PUBLISH, $newsticker);
        }else{
            $messages = "Gagal Delete Data";
        }


        $resource = Serializer::serializeItem($statusRes,$messages);
        return response()->json($resource,$statusCode);
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
            'infomercial' => null,
            'promo' => [
                'promo1' => null,
                'promo2' => null
            ],
            'all_merge' => null
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
                $all_merge = !empty(InewsNewstickerAll::first()->content) ? InewsNewstickerAll::first()->content : '';

                $data['all_merge'] = $all_merge;
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

}

