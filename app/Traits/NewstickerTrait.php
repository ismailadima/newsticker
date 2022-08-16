<?php
// ==========================================
// ; Title: Newsticker Trait
// ; Author: Sands - muhammad.arisandi@mncgroup.com
// ; Year:   2021
// ============== 

namespace App\Traits;

use App\Category;
use App\Helpers\NewstickerHelpers;
use App\LogNewsticker;
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
use App\MpiNewstickerNews;
use App\Newsticker;
use App\RctiNewstickerInfotainment;
use App\RctiNewstickerNews;
use App\RctiNewstickerPromo;
use App\RctiNewstickerPromo2;
use App\RctiNewstickerSergap;
use App\DeleteSchedule;
use App\Status;
use App\Unit;

trait NewstickerTrait
{
    //distribuskan data dari tabel newsticker ke tabel masing2 unit dan masing2 kategori
    public function distributeContent(
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
                switch ($unit_id){
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
                        case Unit::UNIT_GTV:
                            $content = preg_replace("/\r|\n/", "", $content);

                            GtvNewstickerNews::where('id', '<>', null)->delete();

                            GtvNewstickerNews::insert([
                                'content' => $content
                            ]);

                            break;
                        
                        case UNIT::UNIT_INEWS:
                            $content = preg_replace("/\r|\n/", "", $content);

                            InewsNewstickerNews::where('id', '<>', null)->delete();

                            InewsNewstickerNews::insert([
                                'content' => $content
                            ]);

                            break;
                        
                        case UNIT::UNIT_MNCTV:
                            $content = preg_replace("/\r|\n/", "", $content);

                            MnctvNewstickerNews::where('id', '<>', null)->delete();

                            MnctvNewstickerNews::insert([
                                'content' => $content
                            ]);

                            break;


                        case UNIT::UNIT_RCTI:
                            $content = preg_replace("/\r|\n/", "", $content);

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
                    case Unit::UNIT_GTV:
                        //2021-03-08, sands, remove new line untuk gtv
                        $content = trim(preg_replace('/\s+/', ' ', $content));
                        $content2 = trim(preg_replace('/\s+/', ' ', $content2));

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
                    
                    case Unit::UNIT_INEWS:

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
                    
                    case Unit::UNIT_MNCTV:
                         //2021-05-28, sands, remove new line untuk gtv
                        $content = preg_replace("/\r|\n/", "", $content);
                        $content2 = preg_replace("/\r|\n/", "", $content2);
                        
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


                    case Unit::UNIT_RCTI:
                        //2021-03-09, sands, remove new line untuk rcti
                        $content = trim(preg_replace('/\s+/', ' ', $content));
                        $content2 = trim(preg_replace('/\s+/', ' ', $content2));

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

             // ============================================
            // Jika Category ID = 5 atau Breaking News;
            // ============================================
            case Category::CAT_BREAKING_NEWS: 
                switch ($unit_id) {
                    case Unit::UNIT_MPI:
                        $content = preg_replace("/\r|\n/", "", $content);

                        MpiNewstickerNews::where('id', '<>', null)->delete();

                        MpiNewstickerNews::insert([
                            'content' => $content
                        ]);

                        break;
                }
            break;

            // ============================================
            // Jika Category ID = 6 atau Sergap News;
            // ============================================
            case Category::CAT_SERGAP_NEWS: 
                switch ($unit_id) {
                    case Unit::UNIT_RCTI:
                        $content = preg_replace("/\r|\n/", "", $content);

                        RctiNewstickerSergap::where('id', '<>', null)->delete();

                        RctiNewstickerSergap::insert([
                            'content' => $content
                        ]);

                        break;
                }
            break;

        }
    }


    //Get satu data newsticker by date dengan kondisi status newsticker belum di publish
    public function getFirstNewstickerUnpublishByDate(
        $date = null,
        $unit_id = null,
        $category_id = null
    ){
        $newsticker_get = Newsticker::where([
                ['newsticker_date', $date],
                ['unit_id', $unit_id],
                ['category_id', $category_id],
                ['status_id', Newsticker::STATUS_UNPUBLISH]
            ])
            ->orderBy('created_at', 'ASC')->first();

        return count($newsticker_get) > 0 ? $newsticker_get : false;
    }

    //Get satu data newsticker by date dengan kondisi status newsticker sudah di publish
    public function getFirstNewstickerPublishByDate(
        $date = null,
        $unit_id = null,
        $category_id = null
    ){
        $newsticker_get = Newsticker::where([
                ['newsticker_date', $date],
                ['unit_id', $unit_id],
                ['category_id', $category_id],
                ['status_id', Newsticker::STATUS_PUBLISH]
            ])
            ->orderBy('created_at', 'ASC')->first();

            return count($newsticker_get) > 0 ? $newsticker_get : false;
    }

    //Get satu data newsticker INEWS terbaru berdasar kategori
    public function getFirstNewstickerInewsByCategory($category_id = null){
        $newsticker = Newsticker::where('category_id', $category_id)
            ->where('status_id', Newsticker::STATUS_PUBLISH)
            ->where('unit_id', Unit::UNIT_INEWS)
            ->orderBy('created_at', 'DESC')
            ->first();
        
        if(count($newsticker) > 0){
            return $newsticker;
        }else{
            return null;
        }
    }

    //pembuatan log yang berhubungan dengan transaksi data nesticker
    public function createLog(
        $type = null,
        $data = null
    ){
        if(!empty($type) && !empty($data)){
            $data = collect($data)->toArray();

            //empty date and created
            $data['created_at'] = null;
            $data['updated_at'] = null;
            $data['created_by'] = null;
            $data['updated_by'] = null;

            $id_newdata = $data['id'];
            unset($data['id']);

            $log_newsticker = new LogNewsticker();
            $log_newsticker->action_type = $type;
            $log_newsticker->fill($data);
            $log_newsticker->newstickers_id = $id_newdata;

            $log_newsticker->save();

            return true;
        }
        return false;
    }

    public function createDeleteSchedule(
        $data = null
    ){
        // dd($data);
        if(!empty($data)){
            $content = NewstickerHelpers::splitContents($data);
            if(!empty($content['content'])){
                foreach ($content['content'] as $valueContent1) {
                    $delete_schedule = new DeleteSchedule();
                    $delete_schedule->newsticker_id = $data['id'];
                    $delete_schedule->units_id = $data['unit_id'];
                    $delete_schedule->categories_id = $data['category_id'];
                    $delete_schedule->tgl_OA = $data['newsticker_date'];
                    $delete_schedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                    $delete_schedule->rtx_data = $valueContent1;
                    $delete_schedule->type_content = DeleteSchedule::CONTENT_TYPE_1;
                    $delete_schedule->save();
                }
            }
            if(!empty($content['content2'])){
                foreach ($content['content2'] as $valueContent2) {
                    $delete_schedule = new DeleteSchedule();
                    $delete_schedule->newsticker_id = $data['id'];
                    $delete_schedule->units_id = $data['unit_id'];
                    $delete_schedule->categories_id = $data['category_id'];
                    $delete_schedule->tgl_OA = $data['newsticker_date'];
                    $delete_schedule->is_deleted = DeleteSchedule::STATUS_UNDELETED;
                    $delete_schedule->rtx_data = $valueContent2;
                    $delete_schedule->type_content = DeleteSchedule::CONTENT_TYPE_2;
                    $delete_schedule->save();
                }
            }
            return true;
        }
        return false;
    }

}