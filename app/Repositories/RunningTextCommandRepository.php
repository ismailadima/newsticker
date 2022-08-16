<?php

namespace App\Repositories;

use App\Category;
use App\DeleteSchedule;
use App\Helpers\GlobalHelpers;
use App\Helpers\NewstickerHelpers;
use App\Interfaces\RunningTextCommandInterface;
use App\LogNewsticker;
use App\Newsticker;
use App\RunningTextType;
use App\Traits\NewstickerTrait;
use App\Unit;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;

class RunningTextCommandRepository implements RunningTextCommandInterface
{
    use NewstickerTrait;

    public function autoPublishContentToday($unit_id)
    {
        $response = true;
        $today = date("Y-m-d");
        $categories = Category::where('id', '<>', Category::CAT_ALL)->get();

        foreach ($categories as $category) {
            //handle to DB
            DB::beginTransaction();
            try {
                $category_id = $category->id;
                $today_first_newsticker = $this->getFirstNewstickerUnpublishByDate($today, $unit_id, $category_id);

                if (!empty($today_first_newsticker)) {

                    //Distribute data content masing2 kategori
                    $this->distributeContent($category_id, $unit_id, $today_first_newsticker->content, $today_first_newsticker->content2);

                    //Unpublish data yang lain (tanggal dan kategori yang sama pada suatu unit)
                    Newsticker::where([
                        ['id', '<>', $today_first_newsticker->id],
                        ['newsticker_date', $today_first_newsticker->newsticker_date],
                        ['category_id', $category_id],
                        ['status_id', '<>', Newsticker::STATUS_NONAKTIF],
                        ['unit_id', $unit_id]
                    ])->update([
                        'status_id' => Newsticker::STATUS_UNPUBLISH
                    ]);

                    //Change status publish
                    $today_first_newsticker->status_id = Newsticker::STATUS_PUBLISH;
                    $today_first_newsticker->save();

                    //Create Logs
                    $this->createLog(LogNewsticker::STATUS_PUBLISH_AUTO, $today_first_newsticker);
                }

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
        }

        return $response;
    }

    public function autoDeleteLineToday()
    {
        date_default_timezone_set('Asia/Jakarta');

        $response = true;
        $today = date("Y-m-d");
        $categories = Category::where('id', '<>', Category::CAT_ALL)->get();
        $units = Unit::where('is_active', 'Y')->get();
        $content1 = '';
        $content2 = '';
        $buildContent1 = '';
        $buildContent2 = '';
        $buildContent1_arr = [];
        $buildContent2_arr = [];
        $getContent1 = [];
        $getContent2 = [];

        foreach ($categories as $category) {
            //handle to DB
            foreach ($units as $unit) {
                $content1 = '';
                $content2 = '';
                $buildContent1 = '';
                $buildContent2 = '';
                $buildContent1_arr = [];
                $buildContent2_arr = [];

                DB::beginTransaction();
                try {
                    $category_id = $category->id;
                    $unit_id = $unit->id;
                    $today_first_newsticker = $this->getFirstNewstickerPublishByDate($today, $unit_id, $category_id);
                    $time = date('Y-m-d H:i:s');

                    if (!empty($today_first_newsticker)) {
                        // Find data in deleted_schedule
                        $qDeleteSch = DeleteSchedule::where([
                            ['newsticker_id', $today_first_newsticker->id],
                            ['time_deleted', '<', $time],
                            ['is_deleted', DeleteSchedule::STATUS_UNDELETED]
                        ])->whereNotNull('time_deleted');
                        $checkDeleteSch = $qDeleteSch->get();

                        if (!$checkDeleteSch->isEmpty()) {
                            foreach ($checkDeleteSch as $data) {
                                $data->is_deleted = DeleteSchedule::STATUS_DELETED;
                                $data->save();
                            }

                            //2021-12-25, sands, comment is_deleted conditiion agar bisa didelete permanent untuk mnctv
                            //2022-07-19, sands, buka ALL unit
                            $tempContent1 = DeleteSchedule::where([
                                ['newsticker_id', $today_first_newsticker->id],
                                // ['is_deleted',DeleteSchedule::STATUS_UNDELETED],
                                ['type_content', DeleteSchedule::CONTENT_TYPE_1]
                            ])->get();
                            $tempContent2 = DeleteSchedule::where([
                                ['newsticker_id', $today_first_newsticker->id],
                                // ['is_deleted',DeleteSchedule::STATUS_UNDELETED],
                                ['type_content', DeleteSchedule::CONTENT_TYPE_2]
                            ])->get();

                            if (!$tempContent1->isEmpty()) {
                                foreach ($tempContent1 as $value) {
                                    //2021-12-25, sands, jika MNCTV delete permanent utk flag is_deleted =  Y 
                                    // $buildContent1 .= $value->rtx_data;
                                    //2022-07-19, sands, buka ALL unit
                                    // if($value->units_id == Unit::UNIT_MNCTV && $value->is_deleted == DeleteSchedule::STATUS_DELETED){
                                    if ($value->is_deleted == DeleteSchedule::STATUS_DELETED) {
                                        $log_delete = $today_first_newsticker->toArray();
                                        $log_delete['content'] = $value->rtx_data;
                                        $log_delete['content2'] = "";
                                        $this->createLog(LogNewsticker::STATUS_DELETED_LINE_BY_AUTODELETE, $log_delete);
                                        $value->delete();
                                    } else if ($value->is_deleted == DeleteSchedule::STATUS_UNDELETED) {
                                        // $buildContent1 .= $value->rtx_data;
                                        $buildContent1_arr[] = $value->rtx_data;
                                    }
                                }

                                // $buildContent = NewstickerHelpers::splitContentsforDelete($today_first_newsticker, $buildContent1);
                                // $content1 = NewstickerHelpers::mergeContents($today_first_newsticker, $buildContent['content']);
                                $content1 = NewstickerHelpers::mergeContents($today_first_newsticker, $buildContent1_arr);
                            }
                            if (!$tempContent2->isEmpty()) {
                                foreach ($tempContent2 as $value) {
                                    //2021-12-25, sands, jika MNCTV delete permanent utk flag is_deleted =  Y 
                                    // $buildContent2 .= $value->rtx_data;
                                    //2022-07-19, sands, buka ALL unit
                                    // if($value->units_id == Unit::UNIT_MNCTV && $value->is_deleted == DeleteSchedule::STATUS_DELETED){
                                    if ($value->is_deleted == DeleteSchedule::STATUS_DELETED) {
                                        $log_delete = $today_first_newsticker;
                                        $log_delete->content = "";
                                        $log_delete->content2 = $value->rtx_data;
                                        $this->createLog(LogNewsticker::STATUS_DELETED_LINE_BY_AUTODELETE, $log_delete);
                                        $value->delete();
                                    } else if ($value->is_deleted == DeleteSchedule::STATUS_UNDELETED) {
                                        // $buildContent2 .= $value->rtx_data;
                                        $buildContent2_arr[] = $value->rtx_data;

                                    }
                                }

                                // $buildContent = NewstickerHelpers::splitContentsforDelete($today_first_newsticker, $buildContent2);
                                // $content2 = NewstickerHelpers::mergeContents($today_first_newsticker, $buildContent['content']);
                                $content2 = NewstickerHelpers::mergeContents($today_first_newsticker, $buildContent2_arr);
                            }

                            //2021-12-25, sands, jika MNCTV maka konten di newstickers di update, sesuai konten yang sudah di delete
                            //2022-07-19, sands, buka ALL
                            // if($unit_id == Unit::UNIT_MNCTV){
                            if (!empty($content1)) {
                                $content1_clean = NewstickerHelpers::cleanTextInput($content1);
                                $today_first_newsticker->content = $content1_clean;
                            }

                            if (!empty($content2)) {
                                $content2_clean = NewstickerHelpers::cleanTextInput($content2);
                                $today_first_newsticker->content2 = $content2_clean;
                            }

                            $today_first_newsticker->save();

                            //Create Logs
                            $this->createLog(LogNewsticker::STATUS_UPDATE_BY_AUTODELETE, $today_first_newsticker);

                            //Distribute data content masing2 kategori
                            $this->distributeContent($category_id, $unit_id, $content1, $content2);
                            $this->createLog(LogNewsticker::STATUS_PUBLISH_BY_AUTODELETE, $today_first_newsticker);
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    $response = $e;
                    // throw $e;
                } catch (\Throwable $e) {
                    DB::rollback();
                    $response = $e;
                    // throw $e;
                }
            }
        }

        return $response;
    }
}
