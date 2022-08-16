<?php

namespace App\Console\Commands;

use App\Category;
use App\Interfaces\RunningTextCommandInterface;
use App\LogNewsticker;
use App\Newsticker;
use App\Traits\NewstickerTrait;
use App\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class RunningTextAutoPublishMnctv extends Command
{
    protected $runningTextCommandInterface;
    use NewstickerTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopublish:mnctv';

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
    public function __construct(RunningTextCommandInterface $runningTextCommandInterface)
    {
        $this->runningTextCommandInterface = $runningTextCommandInterface;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo "Auto Publish Running............. ";
        $autoPublishContentToday = $this->runningTextCommandInterface->autoPublishContentToday(Unit::UNIT_MNCTV);
        if($autoPublishContentToday == true){
            $message_command = "Auto Publish Succes";
        }else{
            $message_command = $autoPublishContentToday;
        }

        echo $message_command;
    }
}
