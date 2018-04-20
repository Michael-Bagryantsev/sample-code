<?php

namespace App\Http\Controllers;

use App\Models\{UsersFb, StatsPerDay};
use Illuminate\Http\Request;
use stdClass;
use DB;

class HomeController extends Controller
{

    private $defaultStatsStart = '-6 days';
    private $defaultStatsEnd = 'now';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stats = new stdClass();
        $stats->telegramTodayNew = UsersFb::whereNotNull('amocrm_lead_id')->where([
            ['lead_created', '>', strtotime('midnight')],
            ['customer_from', '=', 'telegram']
        ])->count();
        $stats->facebookTodayNew = UsersFb::whereNotNull('amocrm_lead_id')->where([
            ['lead_created', '>', strtotime('midnight')],
            ['customer_from', '=', 'facebook']
        ])->count();
        $stats->telegramTodayCompleted = UsersFb::whereNotNull('amocrm_lead_id')->where([
            ['lead_completed', '>', strtotime('midnight')],
            ['customer_from', '=', 'telegram']
        ])->count();
        $stats->facebookTodayCompleted = UsersFb::whereNotNull('amocrm_lead_id')->where([
            ['lead_completed', '>', strtotime('midnight')],
            ['customer_from', '=', 'facebook']
        ])->count();

        $stats->start = $request->has('stats_start') ? $request->input('stats_start') : $this->defaultStatsStart;
        $stats->end = $request->has('stats_end') ? $request->input('stats_end') : $this->defaultStatsEnd;

        $statsPerDay = StatsPerDay::where([
            ['day','>=',date('Y-m-d', strtotime($stats->start))],
            ['day','<=',date('Y-m-d', strtotime($stats->end))],
        ])->orderBy('day')->get();
        $stats->statsPerDay = new stdClass();
        $stats->statsPerDay->dates = [];
        $stats->statsPerDay->new_leads_manager_telegram = [];
        $stats->statsPerDay->new_leads_manager_facebook = [];
        $stats->statsPerDay->leads_completed_telegram = [];
        $stats->statsPerDay->leads_completed_facebook = [];
        $stats->statsPerDay->new_leads_bot_telegram = [];
        $stats->statsPerDay->new_leads_bot_facebook = [];
        foreach ($statsPerDay as $dayInfo) {
            $stats->statsPerDay->dates[] = $dayInfo->day->format('Y-m-d');
            $stats->statsPerDay->new_leads_manager_telegram[] = $dayInfo->new_leads_manager_telegram;
            $stats->statsPerDay->new_leads_manager_facebook[] =  $dayInfo->new_leads_manager_facebook;
            $stats->statsPerDay->leads_completed_telegram[] = $dayInfo->leads_completed_telegram;
            $stats->statsPerDay->leads_completed_facebook[] =  $dayInfo->leads_completed_facebook;
            $stats->statsPerDay->new_leads_bot_telegram[] = $dayInfo->new_leads_bot_telegram;
            $stats->statsPerDay->new_leads_bot_facebook[] =  $dayInfo->new_leads_bot_facebook;
        }

        return view('home', ['stats' => $stats]);
    }
}
