<?php


namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Blacklist;
use App\Http\Models\Domain;
use App\Http\Models\Currency;
use App\Http\Models\Customer;
use App\Http\Models\Invoices;
use App\Http\Models\Log;
use App\Http\Models\Product;
use App\Http\Models\Employees;
use App\Http\Models\Whitelist;
use App\Http\Models\WhitelistRcpt;
use App\Http\Utils\Utils;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\In;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade as PDF;

class AdminController
{
    public function index()
    {
        $user = session()->get('user');
        $user_type = session()->get('user-type');

        if (isset($user) && isset($user_type)) {
            return redirect('/dashboard');
        } else {
            return view('login');
        }
    }

    public function doLogin()
    {
        $email = request('login-username');
        $password = request('login-password');

        if (!isset($email)) {
            session()->flash('error-msg', 'Please enter valid email.');
            return redirect()->back();
        }
        if (!isset($password)) {
            session()->flash('error-msg', 'Please enter valid password.');
            return redirect()->back();
        }

        $admin = Admin::where('email', $email)->first();
        if (!isset($admin)) {

            $user = Customer::where('email', $email)->first();
            if (!isset($user)) {
                session()->flash('error-msg', 'User not found.');
                return redirect()->back();
            }

            if (!hash::check($password, $user->password)) {
                session()->flash('error-msg', 'Invalid password.');
                return redirect()->back();
            }

            session()->put('user', $user);
            session()->put('user-type', 2);
            return redirect('/dashboard');
        }

        if (!hash::check($password, $admin->password)) {
            session()->flash('error-msg', 'Invalid password.');
            return redirect()->back();
        }

        session()->put('user', $admin);
        session()->put('user-type', 1);
        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->remove('user');
        session()->remove('user-type');
        return redirect('/login');
    }

    public function showForgotPasswordPage()
    {
        return view('forgot-password');
    }

    public function resetPassword() {

        $email = request('reminder-credential');
        $new_password = $this->randomPassword();
        $data = array('password'=>$new_password);

        try {
            Mail::send(['text' => 'mail'], $data, function ($message) use ($email) {
                $message->to($email, '')->subject
                ('Recovery Password');
                $message->from('portal@cubewerk.de', 'Web Portal');
            });
        } catch (Exception $e) {
            return view('forgot-password')->with([
                'message' => 'fail'
            ]);
        }

        $update_array = array(
        );
        $update_array['password'] = hash::make($new_password);

        Customer::where('email', $email)->update($update_array);

        return view('forgot-password')->with([
            'message' => 'success'
        ]);
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function dashboard()
    {
        $current_user_id = session()->get('user')->id;
        $products = Product::where('customer_id', $current_user_id)->count();
        $domains = Domain::where('customer_id', $current_user_id)->count();
        return view('dashboard')->with([
            'products' => $products,
            'domains' => $domains,
        ]);
    }

    public function showProfilePage() {
        $user = session()->get('user');
        return view('profile')->with('user', $user);
    }

    public function editProfile() {
        $id = request('id');
        $password = request('password');

        $update_array = array(
        );

        if ($password != '') {
            $update_array['password'] = hash::make($password);
        }

        $user_type = session()->get('user-type');

        if (count($update_array) > 0) {
            if ($user_type === 1) {
                Admin::where('id', $id)->update($update_array);
                session()->put('user', Admin::where('id', $id)->first());
            } else {
                Customer::where('id', $id)->update($update_array);
                session()->put('user', Customer::where('id', $id)->first());
            }
        }

        return back()
            ->with('success', 'You have successfully updated your profile.');
    }

    public function showProductsPage()
    {
        $current_user_id = session()->get('user')->id;
        $products = Product::where('customer_id', $current_user_id)->with('domain')->get();

        for ($i = 0; $i < count($products); $i ++) {
            $used_ccount = Log::where('msg_to', 'like', '%'.$products[$i]['domain']['domain'])->count(DB::raw('DISTINCT msg_to'));
            $products[$i]['used'] = $used_ccount;
            $products[$i]['free'] = $products[$i]['alloweduser'] - $used_ccount;
            if ($products[$i]['free'] < 0)
                $products[$i]['free'] = 0;
        }

        return view('products')->with([
            'product_array' => $products,
        ]);

    }

    public function showDomainPage()
    {
        $current_user_id = session()->get('user')->id;
        $domains = Domain::where('customer_id', $current_user_id)->get();
        return view('domain')->with([
            'domain_array' => $domains
        ]);
    }

    public function showStatisticsPage() {

        $date_from = request('date_from');
        $date_to = request('date_to');
        $stats_type = request('stats_type');

        $initial_clause = '(';
        $domains = Domain::where('customer_id', session()->get('user')->id)->get();

        for($i = 0; $i < count($domains); $i ++) {
            $initial_clause .= '`msg_to` like \'%'.$domains[$i]['domain'].'\' ';
            if ($i < count($domains) - 1) {
                $initial_clause .= 'OR ';
            }
        }
        $initial_clause .= ')';


        $result = array();
        $where_date_clause = '';
        if(isset($date_from))
            $where_date_clause .= 'AND `timestamp` >= \''. $date_from .'\'';
        else $date_from = '';
        if(isset($date_to))
            $where_date_clause .= 'AND `timestamp` <= \''. $date_to .'\'';
        else $date_to = '';

        $action_array = ['sent', 'spam', 'attachment', 'virus'];
        if(isset($stats_type)) {
            if($stats_type == 1) {
                $pre_result = array();
                for($i = 0; $i < count($action_array); $i ++) {
                    $query = 'SELECT 
                                SUBSTRING( `timestamp`, 1, 10 ) time, 
                                count( * ) val
                            FROM
                                `logs` 
                            WHERE'.$initial_clause.
                        'AND action = \''.$action_array[$i].'\''.$where_date_clause.
                        'GROUP BY
                                SUBSTRING( `timestamp`, 1, 10 )';
                    $pre_result[$i] = DB::select($query);
                }

                $query = 'SELECT
                                SUBSTRING( `timestamp`, 1, 10 ) time 
                            FROM
                                `logs`
                            WHERE'.$initial_clause.$where_date_clause.'
                            GROUP BY
                            SUBSTRING( `timestamp`, 1, 10 )';
                $result1 = DB::select($query);

                $cnt = 0;
                foreach ($result1 as $one) {
                    $result[$cnt++] = array('time' => $one->time);
                }

                for($i = 0; $i < count($pre_result); $i ++) {
                    for ($j = 0; $j < count($result); $j ++) {
                        foreach ($pre_result[$i] as $pre_result_one) {
                            if ($result[$j]['time'] == $pre_result_one->time) {
                                $result[$j][$action_array[$i]] = $pre_result_one->val;
                            }
                        }
                    }

                }

            } else if ($stats_type == 2) {
                $pre_result = array();
                for($i = 0; $i < count($action_array); $i ++) {
                    $query = 'SELECT 
                                SUBSTRING( `timestamp`, 1, 7 ) time, 
                                count( * ) val
                            FROM
                                `logs` 
                            WHERE'.$initial_clause.
                        'AND action = \''.$action_array[$i].'\''.$where_date_clause.
                        'GROUP BY
                                SUBSTRING( `timestamp`, 1, 7 )';
                    $pre_result[$i] = DB::select($query);
                }

                $query = 'SELECT
                                SUBSTRING( `timestamp`, 1, 7 ) time 
                            FROM
                                `logs`
                            WHERE'.$initial_clause.$where_date_clause.'
                            GROUP BY
                            SUBSTRING( `timestamp`, 1, 7 )';
                $result1 = DB::select($query);

                $cnt = 0;
                foreach ($result1 as $one) {
                    $result[$cnt++] = array('time' => $one->time);
                }

                for($i = 0; $i < count($pre_result); $i ++) {
                    for ($j = 0; $j < count($result); $j ++) {
                        foreach ($pre_result[$i] as $pre_result_one) {
                            if ($result[$j]['time'] == $pre_result_one->time) {
                                $result[$j][$action_array[$i]] = $pre_result_one->val;
                            }
                        }
                    }

                }
            } else {
                $pre_result = array();
                for($i = 0; $i < count($action_array); $i ++) {
                    $query = 'SELECT 
                                SUBSTRING( `timestamp`, 1, 4 ) time, 
                                count( * ) val
                            FROM
                                `logs` 
                            WHERE'.$initial_clause.
                        'AND action = \''.$action_array[$i].'\''.$where_date_clause.
                        'GROUP BY
                                SUBSTRING( `timestamp`, 1, 4 )';
                    $pre_result[$i] = DB::select($query);
                }

                $query = 'SELECT
                                SUBSTRING( `timestamp`, 1, 4 ) time 
                            FROM
                                `logs`
                            WHERE'.$initial_clause.$where_date_clause.'
                            GROUP BY
                            SUBSTRING( `timestamp`, 1, 4 )';
                $result1 = DB::select($query);

                $cnt = 0;
                foreach ($result1 as $one) {
                    $result[$cnt++] = array('time' => $one->time);
                }

                for($i = 0; $i < count($pre_result); $i ++) {
                    for ($j = 0; $j < count($result); $j ++) {
                        foreach ($pre_result[$i] as $pre_result_one) {
                            if ($result[$j]['time'] == $pre_result_one->time) {
                                $result[$j][$action_array[$i]] = $pre_result_one->val;
                            }
                        }
                    }

                }
            }
        } else $stats_type = 0;

        return view('statistics')->with([
            'result' => $result,
            'stats_type' => $stats_type,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    public function showSearchPage() {

        $send_from = request('send_from');
        $send_to = request('send_to');
        $date_from = request('date_from');
        $date_to = request('date_to');
        $show_sent = request('show_sent');
        $show_spam = request('show_spam');
        $show_attachment = request('show_attachment');
        $show_virus = request('show_virus');

        $initial_clause = array();
        $domains = Domain::where('customer_id', session()->get('user')->id)->get();
        foreach ($domains as $v) {
            $initial_clause[] = ['msg_to', 'like', '%'.$v['domain']];
        }

        $search_clause = array();
        if (isset($send_from))
            $search_clause[] = array('msg_from', 'like', "%$send_from%");
        else $send_from = '';

        if (isset($send_to))
            $search_clause[] = array('msg_to', 'like', "%$send_to%");
        else $send_to = '';

        if (isset($date_from))
            $search_clause[] = ['timestamp', '>=', "$date_from"];
        else $date_from = '';

        if (isset($date_to))
            $search_clause[] = ['timestamp', '<=', "$date_to"." 23:59:59"];
        else $date_to = '';

        $message_type_clause = array();
        if (isset($show_sent) && $show_sent == 'on')
            $message_type_clause[] = ['action', 'sent'];
        else $show_sent = 'off';

        if (isset($show_spam) && $show_spam == 'on')
            $message_type_clause[] = ['action', 'spam'];
        else $show_spam = 'off';

        if (isset($show_attachment) && $show_attachment == 'on')
            $message_type_clause[] = ['action', 'attachment'];
        else $show_attachment = 'off';

        if (isset($show_virus) && $show_virus == 'on')
            $message_type_clause[] = ['action', 'virus'];
        else $show_virus = 'off';

        $search_result = Log::where($search_clause)->
        where(function ($query) use ($initial_clause) {
            if (count($initial_clause) > 0) {
                $query->where([$initial_clause[0]]);
                for ($i = 1; $i < count($initial_clause); $i++) {
                    $query->orwhere([$initial_clause[$i]]);
                }
            }
        })->where(function ($query) use ($message_type_clause) {
            if (count($message_type_clause) > 0) {
                $query->where([$message_type_clause[0]]);
                for ($i = 1; $i < count($message_type_clause); $i++) {
                    $query->orwhere([$message_type_clause[$i]]);
                }
            }
        })->orderBy('timestamp', 'desc')->get();

        return view('search')->with([
            'search_result' => $search_result,
            'send_from' => $send_from,
            'send_to' => $send_to,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'show_sent' => $show_sent,
            'show_spam' => $show_spam,
            'show_attachment' => $show_attachment,
            'show_virus' => $show_virus,
        ]);
    }

    public function showWhitelistPage() {
        $current_user_id = session()->get('user')->id;
        $whitelist_arry = array();
        if (isset($current_user_id)) {
            $whitelist_arry = Whitelist::where('customer_id', $current_user_id)->get();
        }
        return view('whitelist')->with([
            'whitelist_array' => $whitelist_arry
        ]);
    }

    public function showAddWhitelistPage() {
        return view('whitelist_add');
    }

    public function addWhitelist()
    {
        $current_user_id = session()->get('user')->id;
        $from = request('from-address');
        $rcpt = request('rcpt');

        // Check if already exist in blacklist or whitelist
        if (Blacklist::where([
            ['customer_id', $current_user_id],
            ['from', $from],
            ['rcpt', $rcpt]
        ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Blacklist.');
        }

        if (Whitelist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt]
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Whitelist.');
        }

        $whitelist = new Whitelist();
        $whitelist->customer_id = $current_user_id;
        $whitelist->from = $from;
        $whitelist->rcpt = $rcpt;
        $whitelist->is_enabled = 1;
        $whitelist->save();

        if (WhitelistRcpt::where([
                ['customer_id', $current_user_id],
                ['whitelistrcpt', $rcpt],
            ])->count() < 1) {
            $new_rcpt = new WhitelistRcpt();
            $new_rcpt->customer_id = $current_user_id;
            $new_rcpt->whitelistrcpt = $rcpt;
            $new_rcpt->save();
        }

        $this->saveBlackAndWhitelistToFile();
        $this->saveWhitelistRcptToFile();

        return back()
            ->with('success', 'You have successfully add new whitelist.');
    }

    public function showEditWhitelistPage() {
        $id = request('id');
        $current_user_id = session()->get('user')->id;
        if (isset($id) && isset($current_user_id)) {
            $result = Whitelist::where([
                ['id', $id],
                ['customer_id', $current_user_id],
            ])->first();
            if(isset($result)) {
                return view('whitelist_edit')->with([
                    'whitelist' => $result
                ]);
            }
        }
        return back();
    }

    public function editWhitelist()
    {
        $id = request('id');
        $current_user_id = session()->get('user')->id;
        $from = request('from-address');
        $rcpt = request('rcpt');

        // Check if already exist in blacklist or whitelist
        if (Blacklist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt],
                ['id', '!=' ,$id],
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Blacklist.');
        }

        if (Whitelist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt],
                ['id', '!=' ,$id],
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Whitelist.');
        }

        $original_rcpt = Whitelist::where('id', $id)->first()->rcpt;
        Whitelist::where('id', $id)->update([
            'from' => $from,
            'rcpt' => $rcpt
        ]);

        WhitelistRcpt::where([
            ['customer_id', $current_user_id],
            ['whitelistrcpt', $original_rcpt],
        ])->update([
            'whitelistrcpt' => $rcpt
        ]);

        $this->saveBlackAndWhitelistToFile();
        $this->saveWhitelistRcptToFile();

        return back()
            ->with('success', 'You have successfully update whitelist.');
    }

    public function deleteWhitelist() {
        $id = request('id');
        $current_user_id = session()->get('user')->id;

        if (isset($id) && Whitelist::where([
            ['id', $id],
            ['customer_id', $current_user_id],
            ])->count() > 0) {
            $rcpt = Whitelist::where('id', $id)->first()->rcpt;
            Whitelist::where('id', $id)->delete();
            WhitelistRcpt::where([
                ['customer_id', $current_user_id],
                ['whitelistrcpt', $rcpt],
            ])->delete();

            $this->saveBlackAndWhitelistToFile();
            $this->saveWhitelistRcptToFile();

            return Utils::makeResponse();
        }

        return Utils::makeResponse([], 'Failed');
    }

    public function toggleWhitelistEnable() {
        $id = request('id');
        $current_user_id = session()->get('user')->id;

        if (isset($id) && Whitelist::where([
            ['id', $id],
            ['customer_id', $current_user_id],
            ])->count() > 0) {

            $enable_flag = Whitelist::where('id', $id)->first()->is_enabled;
            $rcpt = Whitelist::where('id', $id)->first()->rcpt;
            if ($enable_flag != 1) {
                $enable_flag = 1;
                if (WhitelistRcpt::where([
                        ['customer_id', $current_user_id],
                        ['whitelistrcpt', $rcpt],
                    ])->count() < 1) {
                    $new_rcpt = new WhitelistRcpt();
                    $new_rcpt->customer_id = $current_user_id;
                    $new_rcpt->whitelistrcpt = $rcpt;
                    $new_rcpt->save();
                }
            } else {
                $enable_flag = 0;
                WhitelistRcpt::where([
                    ['customer_id', $current_user_id],
                    ['whitelistrcpt', $rcpt],
                ])->delete();
            }

            Whitelist::where('id', $id)->update([
                'is_enabled' => $enable_flag,
            ]);

        }

        $this->saveBlackAndWhitelistToFile();
        $this->saveWhitelistRcptToFile();

        return Utils::makeResponse([], 'Failed');
    }

    public function showBlacklistPage() {
        $current_user_id = session()->get('user')->id;
        $blacklist_arry = array();
        if (isset($current_user_id)) {
            $blacklist_arry = Blacklist::where('customer_id', $current_user_id)->get();
        }
        return view('blacklist')->with([
            'blacklist_array' => $blacklist_arry
        ]);
    }

    public function showAddBlacklistPage() {
        return view('blacklist_add');
    }

    public function addBlacklist()
    {
        $current_user_id = session()->get('user')->id;
        $from = request('from-address');
        $rcpt = request('rcpt');

        // Check if already exist in blacklist or whitelist
        if (Blacklist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt]
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Blacklist.');
        }

        if (Whitelist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt]
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Whitelist.');
        }

        $blacklist = new Blacklist();
        $blacklist->customer_id = $current_user_id;
        $blacklist->from = $from;
        $blacklist->rcpt = $rcpt;
        $blacklist->is_enabled = 1;
        $blacklist->save();

        $this->saveBlackAndWhitelistToFile();
        $this->saveWhitelistRcptToFile();

        return back()
            ->with('success', 'You have successfully add new blacklist.');
    }

    public function showEditBlacklistPage() {
        $id = request('id');
        $current_user_id = session()->get('user')->id;
        if (isset($id) && isset($current_user_id)) {
            $result = Blacklist::where([
                ['id', $id],
                ['customer_id', $current_user_id],
            ])->first();
            if(isset($result)) {
                return view('blacklist_edit')->with([
                    'blacklist' => $result
                ]);
            }
        }
        return back();
    }

    public function editBlacklist()
    {
        $id = request('id');
        $current_user_id = session()->get('user')->id;
        $from = request('from-address');
        $rcpt = request('rcpt');

        // Check if already exist in blacklist or whitelist
        if (Blacklist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt],
                ['id', '!=' ,$id],
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Blacklist.');
        }

        if (Whitelist::where([
                ['customer_id', $current_user_id],
                ['from', $from],
                ['rcpt', $rcpt],
                ['id', '!=' ,$id],
            ])->count() > 0) {
            return back()
                ->with('fail', 'You already have the list in Whitelist.');
        }

        Blacklist::where('id', $id)->update([
            'from' => $from,
            'rcpt' => $rcpt
        ]);

        $this->saveBlackAndWhitelistToFile();
        $this->saveWhitelistRcptToFile();

        return back()
            ->with('success', 'You have successfully update blacklist.');
    }

    public function deleteBlacklist() {
        $id = request('id');
        $current_user_id = session()->get('user')->id;

        if (isset($id) && Blacklist::where([
                ['id', $id],
                ['customer_id', $current_user_id],
            ])->count() > 0) {
            Blacklist::where('id', $id)->delete();

            $this->saveBlackAndWhitelistToFile();
            $this->saveWhitelistRcptToFile();

            return Utils::makeResponse();
        }

        return Utils::makeResponse([], 'Failed');
    }

    public function toggleBlacklistEnable() {
        $id = request('id');
        $current_user_id = session()->get('user')->id;

        if (isset($id) && Blacklist::where([
                ['id', $id],
                ['customer_id', $current_user_id],
            ])->count() > 0) {

            $enable_flag = Blacklist::where('id', $id)->first()->is_enabled;

            Blacklist::where('id', $id)->update([
                'is_enabled' => 1 - $enable_flag,
            ]);
        }

        $this->saveBlackAndWhitelistToFile();
        $this->saveWhitelistRcptToFile();

        return Utils::makeResponse([], 'Failed');
    }

    function saveBlackAndWhitelistToFile() {
        $black_list = Blacklist::where('is_enabled', 1)->get();
        $white_list = Whitelist::where('is_enabled', 1)->get();

        $content = "#blacklist\n\n";
        foreach ($black_list as $v) {
            $content .= "uniq-rule-id {\n";
            $content .= "from = \"" . $v->from . "\";\n";
            $content .= "rcpt = \"" . $v->rcpt . "\";\n";
            $content .= "apply {\nactions {\nreject = yes;\n}\n}\n}\n\n";
        }

        $content .= "#whitelist\n\n";
        foreach ($white_list as $v) {
            $content .= "uniq-rule-id {\n";
            $content .= "from = \"" . $v->from . "\";\n";
            $content .= "rcpt = \"" . $v->rcpt . "\";\n";
            $content .= "whitelist = yes;\n";
            $content .= "}\n\n";
        }

        $myfile = fopen("white-and-blacklist-settings.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
    }

    function saveWhitelistRcptToFile() {
        $white_list_rcpt = WhitelistRcpt::get();
        $content = "";
        foreach ($white_list_rcpt as $v) {
            $content .= $v->whitelistrcpt . "\n";
        }
        $myfile = fopen("whitelist-recipients.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
    }
}
