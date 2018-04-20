<?php

namespace App\Console\Commands;

use App\Helpers\AmoHelper;
use App\Models\UsersFb;
use Illuminate\Console\Command;
use AmoCRM\Client as AmoClient;

class getFlowExpiredCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:flowexpired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process customers in Bot Processing Flow column with expired time';

    protected $waitTime = 3600;

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
        $amo = new AmoClient(config('app.AMO_SUBDOMAIN'), config('app.AMO_LOGIN'), config('app.AMO_HASH'));
        sleep(1);
        $leads = $amo->lead->apiList([
            'query' => 'telegram',
            'status' => [(int)config('app.AMO_STATUSES')[1]['key']],
        ]);

        $timeFrom = time() - $this->waitTime;

        $expiredLeads = [];
        foreach ($leads as $lead) {
            if ($lead['last_modified'] < $timeFrom) {
                $expiredLeads[] = $lead;
            }
        }

        foreach ($expiredLeads as $lead) {
            $customer = UsersFb::where('amocrm_lead_id', $lead['id'])->first();

            if (!is_null($customer)) {
                $customer->lead_manager = date('Y-m-d H:i:s');
                $customer->customer_chat_status = 'chat_manager';
                $customer->save();
            }

            sleep(1);

            AmoHelper::updateLead($amo,
                ['id' => (int)$lead['id'],
                    'status_id' => (int)config('app.AMO_STATUSES')[6]['key'],
                    'tags' => array_merge(AmoHelper::parseTagsArray($lead['tags']), ['НеПрошелFlow'])
                ]);
        }
    }
}
