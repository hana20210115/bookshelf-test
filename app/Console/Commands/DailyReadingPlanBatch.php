<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DailyBatchService;

class DailyReadingPlanBatch extends Command
{
    /**
     * コマンドの呼び出し
     * 
     * @var string
     */
    protected $signature = 'batch:reading-plans';

    /**
     * コマンドの説明
     * 
     * @var string
     */
    protected $description = '読書計画の自動失効処理とリマインダー通知の作成を行います';

    /**
     * @var DailyBatchService
     */
    protected $batchService;

    /**
     * コンストラクタ
     * 
     *　@param DailyBatchService $batchService
     */
    public function __construct(DailyBatchService $batchService)
    {
        parent::__construct();
        $this->batchService = $batchService;
    }

    /**
     * バッチ処理のメインロジック
     *
     * @return void
     */
    public function handle():void
    {
        $this->batchService->executeDailyBatch();

        $this->info('日付バッチ処理(自動失効・リマインダー)が完了しました');

    }
}
