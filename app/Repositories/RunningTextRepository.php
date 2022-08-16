<?php

namespace App\Repositories;

use App\Category;
use App\DeleteSchedule;
use App\Helpers\GlobalHelpers;
use App\Helpers\NewstickerHelpers;
use App\Interfaces\RunningTextInterface;
use App\LogNewsticker;
use App\Newsticker;
use App\RunningTextType;
use App\Traits\NewstickerTrait;
use App\Unit;
use App\User;
use DB;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Auth;

class RunningTextRepository implements RunningTextInterface
{
    use NewstickerTrait;

    public function validatedCountContent($request = null)
    {
        $maxchar = Newsticker::MAX_CHARACTER_CONTENT;
        $file = $request->file('file_upload');
        $file2 = $request->file('file_upload2');
        $validated = true;
        
        if (!empty($file)){
            $filetxt = file_get_contents($file->getRealPath());
            if(strlen($filetxt) > $maxchar) $validated = false;
        }

        if (!empty($file2)){
            $filetxt = file_get_contents($file2->getRealPath());
            if(strlen($filetxt) > $maxchar) $validated = false;
        }

        return $validated;
    }

    public function storeRunningtext($request, $is_special_program = false)
    {
        try {
            $category_id = $request->category_id;
            $unit_id = Auth::user()->unit_id;
            $newsticker_date = date('Y-m-d', strtotime($request->newsticker_date));
            $prog_tv_name = $request->program_tv_name;
            
            $file = $request->file('file_upload');
            $file2 = $request->file('file_upload2');

            $content = '';
            $content2 = '';

            if (!empty($file))
            {
                $filetxt = file_get_contents($file->getRealPath());
                $content = NewstickerHelpers::cleanTextInput($filetxt);
            }

            if (!empty($file2) && !empty($category_id))
            {
                $mime_type_file2 = $file2->getClientMimeType();
                $filename_file2 = $file2->getClientOriginalName();
                // $this->validate($request, [
                //     'file_upload2' => 'required|mimes:txt'
                // ]);
                if(MimeType::from($filename_file2) != $mime_type_file2){
                    return false;
                }
        
                if ($category_id == Category::CAT_PROMO)
                {
                    $filetxt2 = file_get_contents($file2->getRealPath());
                    $content2 = NewstickerHelpers::cleanTextInput($filetxt2);
                }
            }

            if($is_special_program){
                $data = [
                    'content' => $content,
                    'content2' => $content2,
                    'category_id' => $category_id,
                    'newsticker_date' => $newsticker_date,
                    'status_id' => Newsticker::STATUS_UNPUBLISH,
                    'unit_id' => $unit_id,
                    'program_tv_name' => $prog_tv_name,
                    'rtext_type_id' => RunningTextType::TYPE_SPECIAL_PROGRAM
                ];
            }else{
                $data = [
                    'content' => $content,
                    'content2' => $content2,
                    'category_id' => $category_id,
                    'newsticker_date' => $newsticker_date,
                    'status_id' => Newsticker::STATUS_UNPUBLISH,
                    'unit_id' => $unit_id,
                    'rtext_type_id' => RunningTextType::TYPE_REGULAR_PROGRAM
                ];
            }

            $save_data = new Newsticker();
            $save_data->fill($data);
            $save_data->save();

            return $save_data;
        } catch(\Exception $e) {
            return false;
        }
        return false;
    }

    public function updateRunningText(
        $newsticker, 
        $request, 
        $is_special_program = false, 
        $is_have_deleted_sch = false
    ){
        DB::beginTransaction();
        try {
            $time1 = json_decode($request->timeDeleted1);
            $time2 = json_decode($request->timeDeleted2);
            // dd(!empty($time1));
            $category_id = $request->category_id;
            $unit_id = $newsticker->unit_id;
            $newsticker_date = date('Y-m-d', strtotime($request->newsticker_date));
            $prog_tv_name = $request->program_tv_name;
            $is_mcr = Auth::user()->is_mcr == User::IS_MCR ? true : false;
            $is_admin = Auth::user()->is_admin == User::IS_ADMIN ? true : false;

            $content2 = '';

            //Gathering Content
            if($request->is_upload == 'Y')
            {
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
    
            }
            else
            {
                $status_log_newsticker = LogNewsticker::STATUS_UPDATE;
                //Check Update From Line Or All VIew
                $is_split_content1 = GlobalHelpers::json_validate($request->content);
                $is_split_content2 = GlobalHelpers::json_validate($request->content2);

                if($is_split_content1){
                    $contents1_decode = json_decode($request->content);
                    $content = NewstickerHelpers::mergeContents($newsticker, $contents1_decode);
                    // dd($content);
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

            ////Update Process
            if (!empty($content) && !empty($category_id))
            {
                if($is_special_program){
                    $data = [
                        'content' => $content,
                        'content2' => $content2,
                        'newsticker_date' => $newsticker_date,
                        'unit_id' => $unit_id,
                        'category_id' => $category_id,
                        'program_tv_name' => $prog_tv_name,
                        'rtext_type_id' => RunningTextType::TYPE_SPECIAL_PROGRAM
                    ];
                }else{
                    $data = [
                        'content' => $content,
                        'content2' => $content2,
                        'newsticker_date' => $newsticker_date,
                        'unit_id' => $unit_id,
                        'category_id' => $category_id,
                        'rtext_type_id' => RunningTextType::TYPE_REGULAR_PROGRAM
                    ];
                }

                $update_data = $newsticker->fill($data);

                //Change status to unpublish
                $update_data->status_id = Newsticker::STATUS_UNPUBLISH;
                $update_data->save();

                $splitData = NewstickerHelpers::splitContents($update_data);

                if(!empty($splitData['content'])){
                    if(!empty($time1)){
                        //sands, 11-11-21, change source data delete sch data
                        // $deleteScheduleData = DeleteSchedule::where('newsticker_id',$update_data['id'])->get();
                        $deleteScheduleData = DeleteSchedule::where('newsticker_id',$update_data['id'])
                            ->where('type_content', 1) //tipe content 1
                            ->get();

                        if(!$deleteScheduleData->isEmpty()){
                            foreach($deleteScheduleData as $keySch => $valueSch){
                                $updateData = DeleteSchedule::findOrFail($valueSch->id);
                                if(empty($splitData['content'][$keySch])){
                                    continue;
                                }
                                $updateData->rtx_data = $splitData['content'][$keySch];
                                $updateData->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                if(!empty($time1[$keySch])){
                                    $updateData->time_deleted = $update_data['newsticker_date'].' '.$time1[$keySch];
                                } else {
                                    $updateData->time_deleted = $time1[$keySch];
                                }
                                $updateData->save();
                                // $updateData->time_deleted = $update_data['newsticker_date'].' '.$
                            }
                        } else{
                            foreach($splitData['content'] as $key1 => $value1){
                                $deleteSchedule = new DeleteSchedule();
                                $deleteSchedule->units_id = $update_data['unit_id'];
                                $deleteSchedule->categories_id = $update_data['category_id'];
                                $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                                $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                $deleteSchedule->newsticker_id = $update_data['id'];
                                $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                                $deleteSchedule->rtx_data = $value1;
                                if(!empty($time1[$key1])){
                                    $deleteSchedule->time_deleted = $update_data['newsticker_date'].' '.$time1[$key1];
                                } else {
                                    $deleteSchedule->time_deleted = $time1[$key1];
                                }
                                $deleteSchedule->save();
                            }
                        }
                    } else {
                        // $whereArray = array(
                        //     'newsticker_id' => $update_data['id'],
                        //     'type_content' => 1
                        // );
                        // DeleteSchedule::where($whereArray)->delete();
                        // foreach($splitData['content'] as $key1 => $value1){
                        //     $deleteSchedule = new DeleteSchedule();
                        //     $deleteSchedule->units_id = $update_data['unit_id'];
                        //     $deleteSchedule->categories_id = $update_data['category_id'];
                        //     $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                        //     $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                        //     $deleteSchedule->newsticker_id = $update_data['id'];
                        //     $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                        //     $deleteSchedule->rtx_data = $value1;
                        //     $deleteSchedule->save();
                        // }

                        $whereArray = array(
                            'newsticker_id' => $update_data['id'],
                            'type_content' => 1
                        );

                        //Jika mengunakan fitur update Content View All
                        if($is_have_deleted_sch){
                            $deleted_data_before = DeleteSchedule::where($whereArray)->get()->toArray();
                            DeleteSchedule::where($whereArray)->delete();

                            foreach($splitData['content'] as $key1 => $value1){
                                $deleteSchedule = new DeleteSchedule();
                                $deleteSchedule->units_id = $update_data['unit_id'];
                                $deleteSchedule->categories_id = $update_data['category_id'];
                                $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                                $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                $deleteSchedule->newsticker_id = $update_data['id'];
                                $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                                $deleteSchedule->rtx_data = $value1;
                                //set time deleted from before jika sama contentnya
                                foreach ($deleted_data_before as $del_data_before) {
                                    similar_text($del_data_before['rtx_data'], $value1, $percent);
                                    if($percent > 90 && !empty($del_data_before['time_deleted'])){
                                        $deleteSchedule->time_deleted = $del_data_before['time_deleted'];
                                    }
                                }

                                $deleteSchedule->save();
                            }

                        }else{
                            DeleteSchedule::where($whereArray)->delete();
                            foreach($splitData['content'] as $key1 => $value1){
                                $deleteSchedule = new DeleteSchedule();
                                $deleteSchedule->units_id = $update_data['unit_id'];
                                $deleteSchedule->categories_id = $update_data['category_id'];
                                $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                                $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                $deleteSchedule->newsticker_id = $update_data['id'];
                                $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                                $deleteSchedule->rtx_data = $value1;
                                $deleteSchedule->save();
                            }
                        }
                      
                    }
                }

                if(!empty($splitData['content2'])){
                    if(!empty($time2)){
                        //sands, 11-11-21, change source data delete sch data
                        // $deleteScheduleData = DeleteSchedule::where('newsticker_id',$update_data['id'])->get();
                        $deleteScheduleData = DeleteSchedule::where('newsticker_id',$update_data['id'])
                            ->where('type_content', 2) //tipe content 2
                            ->get();

                        if(!$deleteScheduleData->isEmpty()){
                            foreach($deleteScheduleData as $keySch => $valueSch){
                                $updateData = DeleteSchedule::findOrFail($valueSch->id);

                                if(empty($splitData['content2'][$keySch])){
                                    continue;
                                }

                                $updateData->rtx_data = $splitData['content2'][$keySch];
                                $updateData->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                if(!empty($time2[$keySch])){
                                    $updateData->time_deleted = $update_data['newsticker_date'].' '.$time2[$keySch];
                                } else {
                                    $updateData->time_deleted = $time2[$keySch];
                                }
                                $updateData->save();
                                // $updateData->time_deleted = $update_data['newsticker_date'].' '.$
                            }
                        } else{
                            foreach($splitData['content2'] as $key2 => $value2){
                                $deleteSchedule = new DeleteSchedule();
                                $deleteSchedule->units_id = $update_data['unit_id'];
                                $deleteSchedule->categories_id = $update_data['category_id'];
                                $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                                $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                $deleteSchedule->newsticker_id = $update_data['id'];
                                $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_2;
                                $deleteSchedule->rtx_data = $value2;
                                if(!empty($time1[$key2])){
                                    $deleteSchedule->time_deleted = $update_data['newsticker_date'].' '.$time2[$key2];
                                } else {
                                    $deleteSchedule->time_deleted = $time2[$key2];
                                }
                                $deleteSchedule->save();
                            }
                        }
                    } else {
                        // $whereArray = array(
                        //     'newsticker_id' => $update_data['id'],
                        //     'type_content' => 2
                        // );
                        // DeleteSchedule::where($whereArray)->delete();
                        // foreach($splitData['content2'] as $value2){
                        //     $deleteSchedule = new DeleteSchedule();
                        //     $deleteSchedule->units_id = $update_data['unit_id'];
                        //     $deleteSchedule->categories_id = $update_data['category_id'];
                        //     $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                        //     $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                        //     $deleteSchedule->newsticker_id = $update_data['id'];
                        //     $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_2;
                        //     $deleteSchedule->rtx_data = $value2;
                        //     $deleteSchedule->save();
                        // }

                        $whereArray = array(
                            'newsticker_id' => $update_data['id'],
                            'type_content' => 2
                        );

                        //Jika mengunakan fitur update Content View All
                        if($is_have_deleted_sch){
                            $deleted_data_before = DeleteSchedule::where($whereArray)->get()->toArray();
                            DeleteSchedule::where($whereArray)->delete();

                            foreach($splitData['content2'] as $key2 => $value2){
                                $deleteSchedule = new DeleteSchedule();
                                $deleteSchedule->units_id = $update_data['unit_id'];
                                $deleteSchedule->categories_id = $update_data['category_id'];
                                $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                                $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                $deleteSchedule->newsticker_id = $update_data['id'];
                                $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_2;
                                $deleteSchedule->rtx_data = $value2;
                                //set time deleted from before jika sama contentnya
                                foreach ($deleted_data_before as $del_data_before) {
                                    similar_text($del_data_before['rtx_data'], $value2, $percent);
                                    if($percent > 90 && !empty($del_data_before['time_deleted'])){
                                        $deleteSchedule->time_deleted = $del_data_before['time_deleted'];
                                    }
                                }

                                $deleteSchedule->save();
                            }
                        }else{
                            DeleteSchedule::where($whereArray)->delete();
                            foreach($splitData['content2'] as $value2){
                                $deleteSchedule = new DeleteSchedule();
                                $deleteSchedule->units_id = $update_data['unit_id'];
                                $deleteSchedule->categories_id = $update_data['category_id'];
                                $deleteSchedule->tgl_OA = $update_data['newsticker_date'];
                                $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                                $deleteSchedule->newsticker_id = $update_data['id'];
                                $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_2;
                                $deleteSchedule->rtx_data = $value2;
                                $deleteSchedule->save();
                            }
                        }
                    }
                }

                //Create Logs
                $this->createLog($status_log_newsticker, $update_data);
            }

            DB::commit();
            $response = true;
    
        } catch (\Exception $e) {
            DB::rollback();
            $response = $e->getMessage();
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $response = $e->getMessage();
            //throw $e;
        }

        return $response;
    }

    public function updateLineRunningText($newsticker, $contents1, $contents2, $request)
    {
        // dd($contents1, $contents2);
        $response = true;
        $type_content = $request->typeContent;
        $splitData = NewstickerHelpers::splitContents($newsticker);
        // dd($request->type_content);
        if($type_content == 1){
            // dd($contents1);
            $splitData['content'][$request->index] = $contents1[$request->index];
        } else if($type_content == 2){
            $splitData['content2'][$request->index] = $contents2[$request->index];
        }
        // dd($splitData);
        if(!empty($contents1)){
            $contents1_merge = NewstickerHelpers::mergeContents($newsticker, $splitData['content']);
            $newsticker->content = $contents1_merge;
        }else if(!empty($contents2)){
            $contents2_merge = NewstickerHelpers::mergeContents($newsticker, $splitData['content2']);
            $newsticker->content2 = $contents2_merge;
        }

        DB::beginTransaction();
        try {
            $newsticker->status_id = Newsticker::STATUS_UNPUBLISH;
            $newsticker->save();
            // DeleteSchedule::where("newsticker_id",$newsticker['id'])->delete();
            // update table delete_schedule
            if(!empty($request->timeDeletedValue)){
                $deleteSchedule = DeleteSchedule::find($request->idTableDelete);
                if(!empty($deleteSchedule)){
                    $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                    $deleteSchedule->time_deleted = $newsticker['newsticker_date'].' '.$request->timeDeletedValue;
                    $deleteSchedule->save();
                } else {
                    if(!empty($splitData['content'])){
                        foreach ($splitData['content'] as $key => $value) {
                            $deleteSchedule = new DeleteSchedule();
                            $deleteSchedule->units_id = $newsticker['unit_id'];
                            $deleteSchedule->categories_id = $newsticker['category_id'];
                            $deleteSchedule->tgl_OA = $newsticker['newsticker_date'];
                            $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                            $deleteSchedule->newsticker_id = $newsticker['id'];
                            $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                            $deleteSchedule->rtx_data = $value;
                            if($key == $request->index && $type_content == 1){
                                $deleteSchedule->time_deleted = $newsticker['newsticker_date'].' '.$request->timeDeletedValue;
                            }
                            $deleteSchedule->save();
                        }
                    }

                    if(!empty($splitData['content2'])){
                        foreach ($splitData['content2'] as $key => $value) {
                            $deleteSchedule = new DeleteSchedule();
                            $deleteSchedule->units_id = $newsticker['unit_id'];
                            $deleteSchedule->categories_id = $newsticker['category_id'];
                            $deleteSchedule->tgl_OA = $newsticker['newsticker_date'];
                            $deleteSchedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                            $deleteSchedule->newsticker_id = $newsticker['id'];
                            $deleteSchedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                            $deleteSchedule->rtx_data = $value;
                            if($key == $request->index && $type_content == 2){
                                $deleteSchedule->time_deleted = $newsticker['newsticker_date'].' '.$request->timeDeletedValue;
                            }
                            $deleteSchedule->save();
                        }
                    }
                }
                // $deleteSchedule->save();
            }
             //Create Logs
             $this->createLog(LogNewsticker::STATUS_UPDATE_LINE, $newsticker);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $response = $e;
            //throw $e;
        }

        return $response;
    }

    public function deleteLineRunningText($newsticker, $request)
    {
        $response = true;
        $index = $request->index;
        $content_delete = $request->content;
        $type_content = $request->typeContent;
        $content_split_arr = NewstickerHelpers::splitContents($newsticker);
        $content1_arr = $content_split_arr['content'];
        $content2_arr = $content_split_arr['content2'];
        $last_index_c1 = count($content1_arr)-1;
        $last_index_c2 = count($content2_arr)-1;
        $is_last_line_c1 = $index == $last_index_c1 ? true : false;
        $is_last_line_c2 = $index == $last_index_c2 ? true : false;

        if($type_content == 1){ //untuk konten 1
            $content_index_current = $content1_arr[$index];
            similar_text($content_delete, $content_index_current, $percent);

            if($percent > 80 || !empty($content1_arr[$index])){
                unset($content1_arr[$index]); 
            }
            $content1_arr = array_values($content1_arr);
            $contents_merge = NewstickerHelpers::mergeContents($newsticker, $content1_arr, $is_last_line_c1);
            $newsticker->content = $contents_merge;
        }else if($type_content == 2){  //untuk konten 2
            $content_index_current = $content2_arr[$index];
            similar_text($content_delete, $content_index_current, $percent);
            
            if($percent > 80 || !empty($content2_arr[$index])){
                unset($content2_arr[$index]); 
            }
            $content2_arr = array_values($content2_arr);
            $contents2_merge = NewstickerHelpers::mergeContents($newsticker, $content2_arr, $is_last_line_c2);
            $newsticker->content2 = $contents2_merge;
        }

        //handle to DB
        DB::beginTransaction();
        try {
            $newsticker->status_id = Newsticker::STATUS_UNPUBLISH;
            $newsticker->save();
            if(!empty($request->idDeleted)){
                $deleteData = DeleteSchedule::find($request->idDeleted);
                $deleteData->delete();
            }
            //Create Logs
            $this->createLog(LogNewsticker::STATUS_DELETE_LINE, $newsticker);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $response = $e;
            //throw $e;
        }

        return $response;
    }

    public function publishRunningText($newsticker)
    {
        $response = true;
        $category_id = $newsticker->category_id;
        $unit_id = $newsticker->unit_id;
        $newsticker_date = $newsticker->newsticker_date;

        //handle to DB
        DB::beginTransaction();
        try {
           //Distribute data content masing2 kategori
           //$this->distributeContent($category_id, Auth::user()->unit_id, $newsticker->content, $newsticker->content2);
           $this->distributeContent($category_id, $newsticker->unit_id, $newsticker->content, $newsticker->content2);

           //Unpublish data yang lain (tanggal dan kategori yang sama pada suatu unit)
           Newsticker::where([
               ['id', '<>', $newsticker->id],
               ['newsticker_date', $newsticker_date],
               ['category_id', $category_id],
               ['status_id', '<>', Newsticker::STATUS_NONAKTIF],
               ['unit_id', $unit_id]
           ])->update([
               'status_id' => Newsticker::STATUS_UNPUBLISH
           ]);
   
           //Change status publish
           $newsticker->status_id = Newsticker::STATUS_PUBLISH;
           $newsticker->save();
   
           //Create Logs
           $this->createLog(LogNewsticker::STATUS_PUBLISH, $newsticker);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response = $e;
            //throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            $response = $e;
            //throw $e;
        }

        return $response;
    }

}