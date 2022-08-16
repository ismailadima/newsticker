<?php

namespace App\Repositories;

use App\Category;
use App\DeleteSchedule;
use App\Helpers\GlobalHelpers;
use App\Helpers\NewstickerHelpers;
use App\Http\Library\Serializer;
use App\InewsNewstickerAll;
use App\Interfaces\RunningTextInewsInterface;
use App\LogNewsticker;
use App\Newsticker;
use App\RunningTextType;
use App\SettingFormatInews;
use App\Traits\NewstickerTrait;
use App\Unit;
use App\User;
use DB;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Auth;
use File;

class RunningTextInewsRepository implements RunningTextInewsInterface
{
    use NewstickerTrait;

    public function storeRunningtext($request)
    {
        $status = false;
        $messages = '';
        
        DB::beginTransaction();
        try {
            $category_id = $request->category_id;
            $unit_id = Auth::user()->unit_id;
            
            $file = $request->file('file_upload');
            $content = '';

            if (!empty($file))
            {
                $filetxt = file_get_contents($file->getRealPath());
                $content = NewstickerHelpers::cleanTextInput($filetxt);
            }

            $data = [
                'content' => $content,
                'category_id' => $category_id,
                'status_id' => Newsticker::STATUS_PUBLISH,
                'unit_id' => $unit_id,
                'rtext_type_id' => RunningTextType::TYPE_REGULAR_PROGRAM
            ];

            $save_data = new Newsticker();
            $save_data->fill($data);
            $save_data->save();
            
            DB::commit();
            $status = true;
            $messages = "Sukses Save Data";

        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        }

        $resource = Serializer::serializeItem($status, $messages, $save_data);
        return $resource;
    }

    public function updateRunningText($newsticker, $request) 
    {
        $status = false;
        $messages = '';

        DB::beginTransaction();
        try {
            $time1 = json_decode($request->timeDeleted1);
            $time2 = json_decode($request->timeDeleted2);

            $category_id = $request->category_id;
            $unit_id = $newsticker->unit_id;
            $newsticker_date = date('Y-m-d', strtotime($request->newsticker_date));
            $prog_tv_name = $request->program_tv_name;
            $is_mcr = Auth::user()->is_mcr == User::IS_MCR ? true : false;
            $is_admin = Auth::user()->is_admin == User::IS_ADMIN ? true : false;
    
            $content2 = '';

            if($request->is_upload == 'Y'){
                $status_log_newsticker = LogNewsticker::STATUS_UPDATE_UPLOAD;
                $file = $request->file('file_upload');
                $file2 = $request->file('file_upload2');
    
                if (!empty($file))
                {
                    $filetxt = file_get_contents($file->getRealPath());
                    $content = NewstickerHelpers::cleanTextInput($filetxt);
                }
    
                if (!empty($file2))
                {
                    $filetxt = file_get_contents($file2->getRealPath());
                    $content2 = NewstickerHelpers::cleanTextInput($filetxt);
                }else{
                    $content2 = $newsticker->content2;
                }
    
            }else{
                $status_log_newsticker = LogNewsticker::STATUS_UPDATE;

                //Check Update From Line Or All VIew
                $is_split_content1 = GlobalHelpers::json_validate($request->content);
                $is_split_content2 = GlobalHelpers::json_validate($request->content2);

                if($is_split_content1){
                    $contents1_decode = json_decode($request->content);
                    $content = NewstickerHelpers::mergeContentsInewsNoFormat($contents1_decode);
                }else{
                    $content = NewstickerHelpers::cleanTextInput($request->content);
                }
    
                if($is_split_content2){
                    $contents2_decode = json_decode($request->content2);
                    $content2 = NewstickerHelpers::mergeContentsInewsNoFormat($contents2_decode);
                }else{
                    $content2 = NewstickerHelpers::cleanTextInput($request->content2);
                }
            }

            if (!empty($content) && !empty($category_id)){
                $data = [
                    'content' => $content,
                    'content2' => $content2,
                    'unit_id' => $unit_id,
                    'category_id' => $category_id,
                    'rtext_type_id' => RunningTextType::TYPE_REGULAR_PROGRAM
                ];

                $update_data = $newsticker->fill($data);
                $update_data->save();

                //Create Logs
                $this->createLog($status_log_newsticker, $update_data);
            }

            DB::commit();
            $status = true;
            $messages = 'Sukses Update';
    
        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e->getMessage();
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e->getMessage();
            //throw $e;
        }

        $resource = Serializer::serializeItem($status, $messages);
        return $resource;
    }

    public function updateLineRunningText($newsticker, $contents1, $contents2, $request)
    {
        $status = false;
        $messages = '';

        DB::beginTransaction();
        try {
            $type_content = $request->typeContent;
            $splitData = NewstickerHelpers::splitContents($newsticker);

            if($type_content == 1){
                $splitData['content'][$request->index] = $contents1[$request->index];
            } else if($type_content == 2){
                $splitData['content2'][$request->index] = $contents2[$request->index];
            }

            if(!empty($contents1)){
                $contents1_merge = NewstickerHelpers::mergeContentsInewsNoFormat($splitData['content']);
                $newsticker->content = $contents1_merge;
            }else if(!empty($contents2)){
                $contents2_merge = NewstickerHelpers::mergeContentsInewsNoFormat($splitData['content2']);
                $newsticker->content2 = $contents2_merge;
            }

            $newsticker->save();

            //Create Logs
            $this->createLog(LogNewsticker::STATUS_UPDATE_LINE, $newsticker);

            DB::commit();
            $status = true;
            $messages = "Berhasil Update Line";
        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e;
        }
  
        $resource = Serializer::serializeItem($status, $messages);
        return $resource;
    }

    public function deleteLineRunningText($newsticker, $request)
    {
        $status = false;
        $messages = '';

        //handle to DB
        DB::beginTransaction();
        try {
            $index = $request->index;
            $content_delete = $request->content;
            $type_content = $request->typeContent;
            $content_split_arr = NewstickerHelpers::splitContents($newsticker);
            $content1_arr = $content_split_arr['content'];
            $content2_arr = $content_split_arr['content2'];

            if($type_content == 1){ //untuk konten 1
                $content_index_current = $content1_arr[$index];
                similar_text($content_delete, $content_index_current, $percent);

                if($percent > 88 || !empty($content1_arr[$index])){
                    unset($content1_arr[$index]); 
                }
                $content1_arr = array_values($content1_arr);
                $contents_merge = NewstickerHelpers::mergeContentsInewsNoFormat($content1_arr);

                $newsticker->content = $contents_merge;
            }else if($type_content == 2){  //untuk konten 2
                $content_index_current = $content2_arr[$index];
                similar_text($content_delete, $content_index_current, $percent);
                
                if($percent > 88 || !empty($content2_arr[$index])){
                    unset($content2_arr[$index]); 
                }
                $content2_arr = array_values($content2_arr);
                $contents_merge = NewstickerHelpers::mergeContentsInewsNoFormat($content2_arr);
                $newsticker->content2 = $contents2_merge;
            }

            $newsticker->save();

            //Create Logs
            $this->createLog(LogNewsticker::STATUS_DELETE_LINE, $newsticker);

            DB::commit();
            $status = true;
            $messages = 'Sukses Delete Data';

        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        }

           
        $resource = Serializer::serializeItem($status, $messages);
        return $resource;
    }

    public function joinAllCategoryInewsContent()
    {
        $contents_join = "";
        $pemisah_line = SettingFormatInews::whereNull('category_id')
            ->first()
            ->code_format;
        $categories = SettingFormatInews::orderBy('index', 'ASC')
            ->whereNotNull('category_id')
            ->get();

        //Get Last Newsticker Masing2 Kategori
        foreach($categories as $category){
            $category_id = $category->category_id;
            $code_format = $category->code_format;
            $get_content = $this->getFirstNewstickerInewsByCategory($category_id);
            if(!empty($get_content)){
                $split_content = NewstickerHelpers::splitContents($get_content);
                //get content 1 saja
                if(!empty($split_content['content'])){
                    $merge_content_str = NewstickerHelpers::mergeContentsV2($get_content, $split_content['content'], $code_format, $pemisah_line);
                    $contents_join .= $merge_content_str;
                }
            }
        }

        return trim($contents_join);
    }

    public function publishRunningText()
    {
        $status = false;
        $messages = '';

        //handle to DB
        DB::beginTransaction();
        try {
            //Build Contents All Category
            $content_merged = self::joinAllCategoryInewsContent();

            //Distribute data content (semua kategori jadi satu)
            $delete_inews_newsticker = InewsNewstickerAll::truncate();

            $inews_newsticker = new InewsNewstickerAll();
            $inews_newsticker->content = $content_merged;
            $inews_newsticker->save();

            DB::commit();
            $status = true;

        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        }

        $resource = Serializer::serializeItem($status, $messages);
        return $resource;
    }

    public function buildFileText()
    {
        //Build Contents All Category
        $content_merged = self::joinAllCategoryInewsContent();

        $status = false;
        $messages = '';

        try {
            $path_txt_build = public_path().'/build_runningtext';

            if (!File::isDirectory($path_txt_build)) {
                File::makeDirectory($path_txt_build, 0777, true);
            }

            $filename = "iNewsNewsticker.txt";
            $store_txt_build = public_path("build_runningtext/".$filename);

            //Store in the filesystem.
            $fp = fopen($store_txt_build, "w");
            fwrite($fp, $content_merged);
            fclose($fp);

            $status = true;
            $messages = "Sukses build text";

        } catch (\Exception $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $messages = $e;
            //throw $e;
        }

        $resource = Serializer::serializeItem($status, $messages);
        return $resource;

    }
}