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

class RunningTextAutoDeleteLine extends Command
{
    protected $runningTextCommandInterface;
    use NewstickerTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autodeleteline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command untuk auto delete newsticker line';

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
        echo "Auto Delete Line Running............. ";
        $autoDeleteToday = $this->runningTextCommandInterface->autoDeleteLineToday();
        if($autoDeleteToday == true){
            $message_command = "Auto Delete Success";
        }else{
            $message_command = $autoDeleteToday;
        }

        echo $message_command;
    }
}
