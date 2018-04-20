<?php

namespace App\Http\Controllers;

use App\Helpers\BillingHelper;
use App\Models\BillingCurrency;
use App\Models\BillingPlan;
use App\Models\UsersFb;
use App\Models\UsersVip;
use Illuminate\Http\Request;
use stdClass, DB, DateTime, DateInterval, Exception;

class CustomersController extends Controller
{

    private $defaultStatsStart = '-6 days';
    private $defaultStatsEnd = 'now';
    private $defaultVip = 'all';
    private $defaultBlocked = 'all';
    private $defaultCustomerFrom = 'all';
    private $defaultSource = 'all';

    private $defaultVipStart = '-1 year';
    private $defaultVipEnd = 'now';

    private $start = 0;
    private $length = 10;

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
        $customer = new UsersFb();
        $query = $customer->newQuery();

        $title = '';

        if ($request->has('lead_created')) {
            $timeFrom = strtotime($request->input('lead_created'));
            $query->where('lead_created', '>=', date('Y-m-d', $timeFrom));
            $query->where('lead_created', '<', date('Y-m-d', $timeFrom + 24*60*60));

            $title .= ' wrote on: '. $request->input('lead_created');
        }

        if ($request->has('lead_manager')) {
            $timeFrom = strtotime($request->input('lead_manager'));
            $query->where('lead_manager', '>=', date('Y-m-d', $timeFrom));
            $query->where('lead_manager', '<', date('Y-m-d', $timeFrom + 24*60*60));

            $title .= ' to manager inbox: '. $request->input('lead_manager');
        }

        if ($request->has('lead_completed')) {
            $timeFrom = strtotime($request->input('lead_completed'));
            $query->where('lead_completed', '>=', date('Y-m-d', $timeFrom));
            $query->where('lead_completed', '<', date('Y-m-d', $timeFrom + 24*60*60));

            $title .= ' completed on: '. $request->input('lead_completed');
        }

        if ($request->has('from')) {
            $query->where('customer_from', $request->input('from'));

            $title .= ' from: '. $request->input('from');
        }

        if (!empty($title)) {
            $title = '-' . $title;
        }

        $customers = $query->get();

        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                $customer->id,
                $customer->first_name . ' ' . $customer->last_name,
                (!is_null($customer->customer_from) ? $customer->customer_from : 'NOT RECOGNIZED'),
                (!empty($customer->amocrm_contact_id) ? '<a href="https://topico.amocrm.ru/contacts/detail/' . $customer->amocrm_contact_id . '" target="blank">' . $customer->amocrm_contact_id . '</a>' : ''),
                (!empty($customer->amocrm_lead_id) ? '<a href="https://topico.amocrm.ru/leads/detail/' . $customer->amocrm_lead_id . '" target="blank">' . $customer->amocrm_lead_id . '</a>' : ''),
                $customer->customer_chat_status,
                (!is_null($customer->lead_created) ? date_format(date_create($customer->lead_created), 'Y-m-d H:i:s') : ''),
                (!is_null($customer->lead_manager) ? date_format(date_create($customer->lead_manager), 'Y-m-d H:i:s') : ''),
                (!is_null($customer->lead_completed) ? date_format(date_create($customer->lead_completed), 'Y-m-d H:i:s') : '')
            ];
        }

        return view('customers/index', ['data' => $data, 'title' => $title]);
    }

    public function indexList(Request $request)
    {
        $title = '';

        $filters = new stdClass();
        $filters->start             = ($request->has('start') && !empty($request->input('start'))) ? $request->input('start') : $this->defaultStatsStart;
        $filters->end               = ($request->has('end') && !empty($request->input('end'))) ? $request->input('end') : $this->defaultStatsEnd;
        $filters->is_vip            = ($request->has('is_vip') && !empty($request->input('is_vip'))) ? $request->input('is_vip') : $this->defaultVip;
        $filters->is_blocked        = ($request->has('is_blocked') && !empty($request->input('is_blocked'))) ? $request->input('is_blocked') : $this->defaultBlocked;
        $filters->customer_from     = ($request->has('customer_from') && !empty($request->input('customer_from'))) ? $request->input('customer_from') : $this->defaultCustomerFrom;
        $filters->source            = ($request->has('source') && !empty($request->input('source'))) ? $request->input('source') : $this->defaultSource;

        $refs = UsersFb::selectRaw('distinct utm_source as source')->whereNotNull('utm_source')->orderBy('source')->get();

        return view('customers/index-list', ['title' => $title, 'filters' => $filters, 'refs' => $refs]);
    }

    public function getListData(Request $request)
    {
        $data = [];

        $start = $request->has('start') ? $request->input('start') : $this->start;
        $length = $request->has('length') ? $request->input('length') : $this->length;

        $customer = new UsersFb();
        $query = $customer->newQuery();

        if ($request->has('search')) {
            $search = $request->input('search')['value'];
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('utm_source', 'like', '%' . $search . '%')
                        ->orWhere('date_registered', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_from', 'like', '%' . $search . '%');
                });
            }
        }

        $filters = [];
        if ($request->has('filters')) {
            $filters = $request->input('filters');
        }
        if (!isset($filters['start'])) {
            $filters['start'] = $this->defaultStatsStart;
        }
        if (!isset($filters['end'])) {
            $filters['end'] = $this->defaultStatsEnd;
        }
        foreach ($filters as $filter => $value) {
            switch ($filter) {
                case 'is_vip':
                    if (in_array($value, ['0', '1'])) {
                        $query->where('is_vip', $value);
                    }
                    break;
                case 'is_blocked':
                    if (in_array($value, ['0', '1'])) {
                        $query->where('is_blocked', $value);
                    }
                    break;
                case 'customer_from':
                    if (in_array($value, ['telegram', 'facebook'])) {
                        $query->where('customer_from', $value);
                    }
                    if ($value === '0') {
                        $query->whereNull('customer_from');
                    }
                    break;
                case 'source':
                    if (!empty($value) && $value !== 'all') {
                        $query->where('utm_source', $value);
                    }
                    break;
                case 'start':
                    if (!empty($value)) {
                        $timeFrom = strtotime($value);
                        $query->where('date_registered', '>=', date('Y-m-d', $timeFrom));
                    }
                    break;
                case 'end':
                    if (!empty($value)) {
                        $timeFrom = strtotime($value);
                        $query->where('date_registered', '<', date('Y-m-d', $timeFrom + 24*60*60));
                    }
                    break;
            }
        }

        $queryTotal = $query;
        $total = $queryTotal->count();

        $orderDirection = 'desc';
        $orderColumn = 'id';
        if ($request->has('order')) {
            if (isset($request->input('order')[0])) {
                $order = $request->input('order')[0];

                $orderDirection = (isset($order['dir']) && $order['dir'] === 'desc') ? 'desc' : 'asc';
                if (!isset($order['column'])) {
                    $order['column'] = '';
                }
                switch ($order['column']) {
                    case 0:
                        $orderColumn = 'id';
                        break;
                    case 1:
                        $orderColumn = 'is_vip';
                        break;
                    case 2:
                        $orderColumn = 'utm_source';
                        break;
                    case 3:
                        $orderColumn = 'lead_created';
                        break;
                    case 4:
                        $orderColumn = 'first_name';
                        break;
                    case 5:
                        $orderColumn = 'customer_from';
                        break;
                    case 6:
                        $orderColumn = 'is_blocked';
                        break;
                    default:
                        $orderColumn = 'id';
                        $orderDirection = 'desc';
                        break;
                }
            }
        }
        $query->orderBy($orderColumn, $orderDirection);

        $query->offset($start)->limit($length);

        $customers = $query->get();
        foreach ($customers as $customer) {
            $data[] = [
                $customer->id,
                (!is_null($customer->is_vip) && $customer->is_vip == 1 ? 'Yes' : 'No'),
                $customer->utm_source,
                (!is_null($customer->lead_created) ? date_format(date_create($customer->lead_created), 'Y-m-d H:i:s') : ''),
                "<a href='#' class='lnk-customer-details' data-id='" . $customer->id . "'>" . $customer->first_name . ' ' . $customer->last_name . "</a>",
                $customer->customer_from,
                (!is_null($customer->is_blocked) && $customer->is_blocked == 1 ? 'Yes' : 'No'),
            ];
        }

        $response = new stdClass();
        $response->draw = ($request->has('draw') ? $request->input('draw') : 0);
        $response->recordsTotal = $total;
        $response->recordsFiltered = $total;
        $response->data = $data;

        return response()->json($response);
    }

    public function getCustomerDetails(Request $request, int $id = 0)
    {
        $customer = DB::table('users_fb')
            ->selectRaw('users_fb.*, billing_currencies.code as code, billing_plans.plan_name as plan_name, billing_orders.address as address, ' .
                ' billing_orders.date_created as date_created, billing_orders.status as billing_status')
            ->where('users_fb.id', $id)
            ->leftJoin('billing_customers', 'users_fb.customer_id', '=', 'billing_customers.id')
            ->leftJoin('billing_orders', 'users_fb.customer_id', '=', 'billing_orders.customer_id')
            ->leftJoin('billing_plans', 'billing_plans.id', '=', 'billing_orders.plan_id')
            ->leftJoin('billing_currencies', 'billing_currencies.id', '=', 'billing_orders.plan_id')
            ->first();

        $customer->billing_status = BillingHelper::getStatusByCode($customer->billing_status);

        return view('customers/customer-details', ['customer' => $customer]);
    }

    public function indexVip(Request $request)
    {
        $title = '';

        $filters = new stdClass();
        $filters->start           = ($request->has('start') && !empty($request->input('start'))) ? $request->input('start') : $this->defaultVipStart;
        $filters->end             = ($request->has('end') && !empty($request->input('end'))) ? $request->input('end') : $this->defaultVipEnd;

        $filters->show_ok         = ($request->has('show_ok') && !empty($request->input('show_ok')));
        $filters->show_expired    = ($request->has('show_expired') && !empty($request->input('show_expired')));
        $filters->show_admins     = ($request->has('show_admins') && !empty($request->input('show_admins')));
        $filters->show_banned     = ($request->has('show_banned') && !empty($request->input('show_banned')));
        $filters->show_unknown    = ($request->has('show_unknown') && !empty($request->input('show_unknown')));

        if (!$filters->show_ok
            && !$filters->show_expired
            && !$filters->show_admins
            && !$filters->show_banned
            && !$filters->show_unknown) {
            $filters->show_ok  = true;
            $filters->show_expired  = true;
            $filters->show_admins  = true;
            $filters->show_banned  = true;
            $filters->show_unknown  = true;
        }

        return view('customers/vip-list', ['filters' => $filters]);
    }

    public function getVipDetails(Request $request, int $id = 0)
    {
        $customer = UsersVip::find($id);

        $currencies = BillingCurrency::all();
        $plans = BillingPlan::all();

        return view('customers/vip-details',
            [
                'customer' => $customer,
                'currencies' => $currencies,
                'plans' => $plans,
            ]
        );
    }

    public function saveVip(Request $request)
    {
        $response = new stdClass();

        try {
            $id = $request->input('id');

            $user = UsersVip::find($id);
            $user->billing_plan_id = !empty($request->input('billing_plan_id')) ? $request->input('billing_plan_id') : null;
            $user->amocrm_contact_id = !empty($request->input('amocrm_contact_id')) ? $request->input('amocrm_contact_id') : null;
            $user->amocrm_lead_id = !empty($request->input('amocrm_lead_id')) ? $request->input('amocrm_lead_id') : null;
            $user->first_name = !empty($request->input('first_name')) ? $request->input('first_name') : null;
            $user->last_name = !empty($request->input('last_name')) ? $request->input('last_name') : null;
            $user->telegram_username = !empty($request->input('telegram_username')) ? $request->input('telegram_username') : null;
            $user->telegram_role = !empty($request->input('telegram_role')) ? $request->input('telegram_role') : null;
            $user->vip_from = !empty($request->input('vip_from')) ? strtotime($request->input('vip_from')) : null;
            $user->vip_till = !empty($request->input('vip_till')) ? strtotime($request->input('vip_till')) : null;
            $user->transaction_id = !empty($request->input('transaction_id')) ? $request->input('transaction_id') : null;
            $user->currency_id = !empty($request->input('currency_id')) ? $request->input('currency_id') : null;
            $user->customer_from = !empty($request->input('customer_from')) ? $request->input('customer_from') : null;

            if ($user->vip_from > 2147483647) {
                $user->vip_from = 2147483647;
            }
            if ($user->vip_till > 2147483647) {
                $user->vip_till = 2147483647;
            }

            $user->save();

            $response->result = true;
            $response->message = 'Information saved';
        } catch (Exception $e) {
            $response->result = false;
            $response->message = $e->getMessage();
        }

        return response()->json($response);
    }

    public function getVipTill(Request $request)
    {
        $response = new stdClass();
        $response->result = '';

        $billing_plan_id = $request->has('billing_plan_id') ? $request->input('billing_plan_id') : 0;
        $vip_from = $request->has('vip_from') ? (int)strtotime($request->input('vip_from')) : 0;

        if ($billing_plan_id > 0 && $vip_from > 0) {
            $plan = BillingPlan::find($billing_plan_id);
            if (!is_null($plan)) {
                $date = DateTime::createFromFormat('U', $vip_from);
                $date->add(new DateInterval('P' . intval($plan->prolong_years) . 'Y' . intval($plan->prolong_months) . 'M' . intval($plan->prolong_days) . 'DT' . intval($plan->prolong_hours) . 'H'));

                $response->result = $date->format('Y-m-d');
            }
        }

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVipListData(Request $request)
    {
        $data = [];

        $start = $request->has('start') ? $request->input('start') : $this->start;
        $length = $request->has('length') ? $request->input('length') : $this->length;

        $customer = new UsersVip();
        $query = $customer->newQuery();

        if ($request->has('search')) {
            $search = $request->input('search')['value'];
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('telegram_username', 'like', '%' . $search . '%');
                });
            }
        }

        $filters = [];
        if ($request->has('filters')) {
            $filters = $request->input('filters');
        }
        if (!isset($filters['start'])) {
            $filters['start'] = $this->defaultVipStart;
        }
        if (!isset($filters['end'])) {
            $filters['end'] = $this->defaultVipEnd;
        }

        foreach ($filters as $filter => $value) {
            switch ($filter) {
                case 'start':
                    if (!empty($value)) {
                        $time = strtotime($value);
                        $query->where('vip_from', '>=', $time);
                    }
                    break;
                case 'end':
                    if (!empty($value)) {
                        $time = strtotime($value);
                        $query->where('vip_from', '<', $time + 24*60*60);
                    }
                    break;
            }
        }

        $show = [
            'ok' => isset($filters['show_ok']) && $filters['show_ok'] === 'on',
            'expired' => isset($filters['show_expired']) && $filters['show_expired'] === 'on',
            'admins' => isset($filters['show_admins']) && $filters['show_admins'] === 'on',
            'banned' => isset($filters['show_banned']) && $filters['show_banned'] === 'on',
            'unknown' => isset($filters['show_unknown']) && $filters['show_unknown'] === 'on',
        ];

        $query->where(function ($query) use ($show) {
            $query->where('id', '<', 0);
            foreach ($show as $key => $val) {
                if ($val === true) {
                    switch ($key) {
                        case 'ok':
                            $query->orWhere('vip_till', '>=', time());
                            break;
                        case 'admins':
                            $query->orWhere('telegram_role', '=', 'admin');
                            break;
                        case 'expired':
                            $query->orWhere('vip_till', '<', time() - 24*60*60);
                            break;
                        case 'banned':
                            $query->orWhere('telegram_role', '=', 'banned');
                            break;
                        case 'unknown':
                            $query->orWhereNull('vip_till');
                            break;

                    }
                }
            }
       });

        $queryTotal = $query;
        $total = $queryTotal->count();

        $orderDirection = 'desc';
        $orderColumn = 'id';
        if ($request->has('order')) {
            if (isset($request->input('order')[0])) {
                $order = $request->input('order')[0];

                $orderDirection = (isset($order['dir']) && $order['dir'] === 'desc') ? 'desc' : 'asc';
                if (!isset($order['column'])) {
                    $order['column'] = '';
                }
                switch ($order['column']) {
                    case 0:
                        $orderColumn = 'id';
                        break;
                    case 1:
                        $orderColumn = 'first_name';
                        break;
                    case 2:
                        $orderColumn = 'telegram_role';
                        break;
                    case 3:
                        $orderColumn = 'vip_from';
                        break;
                    case 4:
                        $orderColumn = 'vip_till';
                        break;
                    case 5:
                        $orderColumn = 'amocrm_contact_id';
                        break;
                    case 6:
                        $orderColumn = 'transaction_id';
                        break;
                    default:
                        $orderColumn = 'id';
                        $orderDirection = 'desc';
                        break;
                }
            }
        }
        $query->orderBy($orderColumn, $orderDirection);

        $query->offset($start)->limit($length);

        $customers = $query->get();
        foreach ($customers as $user) {
            $data[] = [
                $user->id,
                $user->first_name . (!is_null($user->last_name) ? ' ' . $user->last_name : '') . (!is_null($user->telegram_username) ? ' @' . $user->telegram_username : ''),
                $user->telegram_role,
                (!is_null($user->vip_from) ? date('Y-m-d', $user->vip_from) : ''),
                (!is_null($user->vip_till) ? date('Y-m-d', $user->vip_till) : ''),
                (!empty($user->amocrm_contact_id) ? '<a href="https://topico.amocrm.ru/contacts/detail/' . $user->amocrm_contact_id . '" target="blank">' . $user->amocrm_contact_id . '</a>' : '')
                . ((!empty($user->amocrm_contact_id) && !empty($user->amocrm_lead_id)) ? ' / ' : '')
                . (!empty($user->amocrm_lead_id) ? '<a href="https://topico.amocrm.ru/leads/detail/' . $user->amocrm_lead_id . '" target="blank">' . $user->amocrm_lead_id . '</a>' : ''),
                (!is_null($user->transaction_id) ? 'Yes' : ''),
                '<a href="#" class="btn btn-primary lnk-vip-details" title="Edit"
                   data-name="' . $user->first_name . (!is_null($user->last_name) ? ' ' . $user->last_name : '') . (!is_null($user->telegram_username) ? ' @' . $user->telegram_username : '') . '"
                   data-id="' . $user->id . '"><i class="fa fa-pencil"></i></a>'
            ];
        }

        $response = new stdClass();
        $response->draw = ($request->has('draw') ? $request->input('draw') : 0);
        $response->recordsTotal = $total;
        $response->recordsFiltered = $total;
        $response->data = $data;

        return response()->json($response);
    }

    public function cancelVip(Request $request, int $id = 0)
    {
        $customer = UsersVip::find($id);
        $customer->telegram_role = 'canceled';
        $customer->save();

        return response()->json(['success' => 'success'], 200);
    }

}
