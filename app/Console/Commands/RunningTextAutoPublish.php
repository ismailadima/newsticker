<?php

namespace App\Console\Commands;

use App\Category;
use App\LogNewsticker;
use App\Newsticker;
use App\Traits\NewstickerTrait;
use App\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class RunningTextAutoPublish extends Command
{
    use NewstickerTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsticker:autopublish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command untuk auto publish newsticker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = date("Y-m-d");
        $units = Unit::where('id', '<>', Unit::UNIT_ALL)->get();
        $categories = Category::where('id', '<>', Category::CAT_ALL)->get();

        foreach($units as $unit){
            $unit_id = $unit->id;
            foreach($categories as $category){
                $category_id = $category->id;
                $today_first_newsticker = $this->getFirstNewstickerUnpublishByDate($today, $unit_id, $category_id);
                dd(array('sandi' => 'sands'));
                if(!empty($today_first_newsticker)){ 
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
            }
        }

        echo "Auto Publish Runned";
    }
}
