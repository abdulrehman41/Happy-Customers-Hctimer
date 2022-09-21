<?php

namespace App\Http\Controllers;

use App\Jobs\GeneratePDF;
use App\Models\User;
use App\Mail\TestMail;
use App\Models\Bonuse;
use App\Mail\PaySlipMail;
use App\Models\Proceed;
use App\Models\Holiday;
use App\Models\Deduction;
use App\Models\SickLeave;
use App\Models\VacationLeave;
use App\Models\Maternity;
use App\Models\Threshold;
use App\Models\Accumulate;
use App\Models\Attendence;
use App\Models\Department;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\PayrollStart;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Deppermissinons;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\EmailAll;
use PDF;



class PayrollController extends Controller
{
        public function getIp(Request $request)
        {
            dd($request->ip());
        }
        public function getMac(Request $request)
        {
            $shellexec = exec('getmac'); 
            dd($shellexec);
        }
        public function DailyPrint(Request $request)
        {
            $pdfData = explode(':',$request->pdfdata);
            $user_id = $pdfData[0];
            $no_days = $pdfData[1];
            $start_date = $pdfData[2];
            $end_date = $pdfData[3];
            $cycle = $pdfData[4];
            if($cycle=='14')
            {
                $cycle = 'Fortnightly';
            }
            else {
                $cycle = 'Monthly';
            }
            $user_email = User::select('email','daily_pay')->where('id',$user_id)->get()->toArray();
            $basic_hour = '';
            $holiday_hour = '';
            if(count($user_email)>0) {
                $print_rec = Proceed::where('start_date',$start_date)->where('end_date', $end_date)
                    ->where('user_id',$user_id)->where('type','Daily')->where('cycle',$cycle)->where('no_days',$no_days)->get()->toArray();
                if (count($print_rec) > 0) {
                    $basic_days = $print_rec[0]['no_days'];
                    $basic_pay = $print_rec[0]['basic_pay'];
                    $daily_rate = $user_email[0]['daily_pay'];

                    $sick_days = $print_rec[0]['sick_hour'];
                    $sick_pay = $print_rec[0]['sick_pay'];

                    $vacation_days = $print_rec[0]['vacation_hour'];
                    $vacation_pay = $print_rec[0]['vacation_pay'];

                    $maternity_days = $print_rec[0]['maternity_hour'];
                    $maternity_pay = $print_rec[0]['maternity_pay'];

                    $holiday_days = $print_rec[0]['holiday_hour'];
                    $holiday_pay = $print_rec[0]['holiday_pay'];

                    $bonus = Bonuse::where('start_date',$start_date)->where('end_date',$end_date)
                        ->where('user_id',$user_id)->where('bonus','>',0)->get()->toArray();

                    $bonus_name = [];
                    $bonus_p = [];
                    foreach($bonus as $b)
                    {

                        array_push($bonus_name,$b['bonus_name']);
                        array_push($bonus_p,$b['bonus']);
                    }
                    //Bonus Pay
                    $bonusPay = $print_rec[0]['bonus'];

                    //NIS
                    $nis_ded = $print_rec[0]['nis'];
                    $nht_ded = $print_rec[0]['nht'];
                    $edtax_ded = $print_rec[0]['edtax'];

                    //One Time Deduction Name and Values

                    $onetime_id = $print_rec[0]['onetime_id'];
                    $onetime_name = [];
                    $onetime_pay = [];

                    if(Str::contains($onetime_id,','))
                    {
                        $replaced = Str::replaceFirst(',', '', $onetime_id);
                        $onetime_arr = explode(',',$replaced);
                        foreach($onetime_arr as $one)
                        {
                            $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$one)->get()->toArray();
                            array_push($onetime_name,$onetimeData[0]->deduction_name);
                            array_push($onetime_pay,$onetimeData[0]->amount);
                        }
                    }
                    else if($onetime_id=='')
                    {

                    }
                    else {
                        $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$onetime_id)->get()->toArray();
                        array_push($onetime_name,$onetimeData[0]->deduction_name);
                        array_push($onetime_pay,$onetimeData[0]->amount);
                    }
                    $onetimePay = $print_rec[0]['one_time'];


                    //Continuous
                    $continuous_id = $print_rec[0]['continuous_id'];
                    $continuous_name = [];
                    $continuous_pay = [];
                    if(Str::contains($continuous_id,','))
                    {
                        $replaced = Str::replaceFirst(',', '', $continuous_id);
                        $continuous_arr = explode(',',$replaced);
                        foreach($continuous_arr as $continuous)
                        {
                            $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous)->get()->toArray();
                            array_push($continuous_name,$continuousData[0]->deduction_name);
                            array_push($continuous_pay,$continuousData[0]->amount);
                        }
                    }
                    else if($continuous_id=='')
                    {

                    }
                    else {
                        $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous_id)->get()->toArray();
                        array_push($continuous_name,$continuousData[0]->deduction_name);
                        array_push($continuous_pay,$continuousData[0]->amount);
                    }
                    $continuous_amount = $print_rec[0]['continuous'];

                    //Loan
                    $periodic_id = $print_rec[0]['periodic_id'];
                    $periodic_name = [];
                    $periodic_pay = [];
                    if(Str::contains($periodic_id,','))
                    {
                        $replaced = Str::replaceFirst(',', '', $periodic_id);
                        $periodic_arr = explode(',',$replaced);
                        foreach($periodic_arr as $periodic)
                        {
                            $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic)->get()->toArray();
                            array_push($periodic_name,$periodicData[0]->deduction_name);
                            array_push($periodic_pay,$periodicData[0]->amount);
                        }
                    }
                    else if($periodic_id=='')
                    {

                    }
                    else {
                        $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic_id)->get()->toArray();
                        array_push($periodic_name,$periodicData[0]->deduction_name);
                        array_push($periodic_pay,$periodicData[0]->amount);
                    }
                    $periodic_amount = $print_rec[0]['periodic'];

                    $user = User::where('id',$user_id)->get()->toArray();

                    $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(nht) as nht_total, SUM(edtax) as edtax_total'))->where("user_id", $user_id)->get()->toarray();

                    $details = [
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'nis' => $user[0]["nis"],
                        'trn' => $user[0]["trn"],
                        'employee_id' => $user[0]["employee_id"],
                        'daily_rate' => $daily_rate,
                        'name' => $user[0]["first_name"].' '.$user[0]["last_name"],
                        'date' => Carbon::now()->format('Y-m-d'),
                        'basic_days' => $basic_days,
                        'basic_pay' => $basic_pay,
                        'sick_days' => $sick_days,
                        'sick_pay' => $sick_pay,
                        'vacation_days' => $vacation_days,
                        'vacation_pay' => $vacation_pay,
                        'maternity_days' => $maternity_days,
                        'maternity_pay' => $maternity_pay,
                        'holiday_days' => $holiday_days,
                        'holiday_pay' => $holiday_pay,
                        'bonus_name' => $bonus_name,
                        'bonus_p' => $bonus_p,
                        'bonusPay' => $bonusPay,
                        'nis_ded' => $nis_ded,
                        'nht_ded' => $nht_ded,
                        'edtax_ded' => $edtax_ded,
                        'onetime_name' => $onetime_name,
                        'onetime_pay' => $onetime_pay,
                        'onetimePay' => $onetimePay,
                        'continuous_name' => $continuous_name,
                        'continuous_pay' => $continuous_pay,
                        'continuous_amount' => $continuous_amount,
                        'periodic_name' => $periodic_name,
                        'periodic_pay' => $periodic_pay,
                        'periodic_amount' => $periodic_amount,
                        'process' => $process

                    ];
                    return view('Admin.daily_print',$details);
                }
            }
            else {
                return redirect()->back()->with('message','Payrol Not Procees');
            }



        }
        public function DailySingleEmail(Request $request)
        {
            $user_id = $request->user_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $no_days = $request->no_days;
            $cycle = $request->cycle;
            if($cycle=='14')
            {
                $cycle = 'Fortnightly';
            }
            else {
                $cycle = 'Monthly';
            }
            $user_email = User::select('email','daily_pay')->where('id',$user_id)->get()->toArray();
            $basic_hour = '';
            $holiday_hour = '';
            if(count($user_email)>0) {
                $print_rec = Proceed::where('start_date',$start_date)->where('end_date', $end_date)
                    ->where('user_id',$user_id)->where('type','Daily')->where('cycle',$cycle)->where('no_days',$no_days)->get()->toArray();
                if (count($print_rec) > 0) {
                    $basic_days = $print_rec[0]['no_days'];
                    $basic_pay = $print_rec[0]['basic_pay'];
                    $daily_rate = $user_email[0]['daily_pay'];

                    $sick_days = $print_rec[0]['sick_hour'];
                    $sick_pay = $print_rec[0]['sick_pay'];

                    $vacation_days = $print_rec[0]['vacation_hour'];
                    $vacation_pay = $print_rec[0]['vacation_pay'];

                    $maternity_days = $print_rec[0]['maternity_hour'];
                    $maternity_pay = $print_rec[0]['maternity_pay'];

                    $holiday_days = $print_rec[0]['holiday_hour'];
                    $holiday_pay = $print_rec[0]['holiday_pay'];

                    $bonus = Bonuse::where('start_date',$start_date)->where('end_date',$end_date)
                        ->where('user_id',$user_id)->where('bonus','>',0)->get()->toArray();

                    $bonus_name = [];
                    $bonus_p = [];
                    foreach($bonus as $b)
                    {

                        array_push($bonus_name,$b['bonus_name']);
                        array_push($bonus_p,$b['bonus']);
                    }
                    //Bonus Pay
                    $bonusPay = $print_rec[0]['bonus'];

                    //NIS
                    $nis_ded = $print_rec[0]['nis'];
                    $nht_ded = $print_rec[0]['nht'];
                    $edtax_ded = $print_rec[0]['edtax'];

                    //One Time Deduction Name and Values

                    $onetime_id = $print_rec[0]['onetime_id'];
                    $onetime_name = [];
                    $onetime_pay = [];

                    if(Str::contains($onetime_id,','))
                    {
                        $replaced = Str::replaceFirst(',', '', $onetime_id);
                        $onetime_arr = explode(',',$replaced);
                        foreach($onetime_arr as $one)
                        {
                            $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$one)->get()->toArray();
                            array_push($onetime_name,$onetimeData[0]->deduction_name);
                            array_push($onetime_pay,$onetimeData[0]->amount);
                        }
                    }
                    else if($onetime_id=='')
                    {

                    }
                    else {
                        $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$onetime_id)->get()->toArray();
                        array_push($onetime_name,$onetimeData[0]->deduction_name);
                        array_push($onetime_pay,$onetimeData[0]->amount);
                    }
                    $onetimePay = $print_rec[0]['one_time'];


                    //Continuous
                    $continuous_id = $print_rec[0]['continuous_id'];
                    $continuous_name = [];
                    $continuous_pay = [];
                    if(Str::contains($continuous_id,','))
                    {
                        $replaced = Str::replaceFirst(',', '', $continuous_id);
                        $continuous_arr = explode(',',$replaced);
                        foreach($continuous_arr as $continuous)
                        {
                            $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous)->get()->toArray();
                            array_push($continuous_name,$continuousData[0]->deduction_name);
                            array_push($continuous_pay,$continuousData[0]->amount);
                        }
                    }
                    else if($continuous_id=='')
                    {

                    }
                    else {
                        $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous_id)->get()->toArray();
                        array_push($continuous_name,$continuousData[0]->deduction_name);
                        array_push($continuous_pay,$continuousData[0]->amount);
                    }
                    $continuous_amount = $print_rec[0]['continuous'];

                    //Loan
                    $periodic_id = $print_rec[0]['periodic_id'];
                    $periodic_name = [];
                    $periodic_pay = [];
                    if(Str::contains($periodic_id,','))
                    {
                        $replaced = Str::replaceFirst(',', '', $periodic_id);
                        $periodic_arr = explode(',',$replaced);
                        foreach($periodic_arr as $periodic)
                        {
                            $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic)->get()->toArray();
                            array_push($periodic_name,$periodicData[0]->deduction_name);
                            array_push($periodic_pay,$periodicData[0]->amount);
                        }
                    }
                    else if($periodic_id=='')
                    {

                    }
                    else {
                        $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic_id)->get()->toArray();
                        array_push($periodic_name,$periodicData[0]->deduction_name);
                        array_push($periodic_pay,$periodicData[0]->amount);
                    }
                    $periodic_amount = $print_rec[0]['periodic'];

                    $user = User::where('id',$user_id)->get()->toArray();

                    $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(nht) as nht_total, SUM(edtax) as edtax_total'))->where("user_id", $user_id)->get()->toarray();

                    $details = [
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'nis' => $user[0]["nis"],
                        'trn' => $user[0]["trn"],
                        'employee_id' => $user[0]["employee_id"],
                        'daily_rate' => $daily_rate,
                        'name' => $user[0]["first_name"].' '.$user[0]["last_name"],
                        'date' => Carbon::now()->format('Y-m-d'),
                        'basic_days' => $basic_days,
                        'basic_pay' => $basic_pay,
                        'sick_days' => $sick_days,
                        'sick_pay' => $sick_pay,
                        'vacation_days' => $vacation_days,
                        'vacation_pay' => $vacation_pay,
                        'maternity_days' => $maternity_days,
                        'maternity_pay' => $maternity_pay,
                        'holiday_days' => $holiday_days,
                        'holiday_pay' => $holiday_pay,
                        'bonus_name' => $bonus_name,
                        'bonus_p' => $bonus_p,
                        'bonusPay' => $bonusPay,
                        'nis_ded' => $nis_ded,
                        'nht_ded' => $nht_ded,
                        'edtax_ded' => $edtax_ded,
                        'onetime_name' => $onetime_name,
                        'onetime_pay' => $onetime_pay,
                        'onetimePay' => $onetimePay,
                        'continuous_name' => $continuous_name,
                        'continuous_pay' => $continuous_pay,
                        'continuous_amount' => $continuous_amount,
                        'periodic_name' => $periodic_name,
                        'periodic_pay' => $periodic_pay,
                        'periodic_amount' => $periodic_amount,
                        'process' => $process

                    ];
                    $pdf = PDF::loadView('dailyPaySlipMail', $details);

                    Mail::send('dailyPaySlipMail', $details, function($message)use($details,$user_email, $pdf,$user) {
                        $message->to($user_email[0]["email"], $user_email[0]["email"])
                            ->subject("Mail From HCTIMER")
                            ->attachData($pdf->output(), $user[0]["first_name"] . " " . $user[0]["last_name"] . ".pdf");
                    });
                    return response()->json([
                        'status' => 3
                    ]);
                }
                else {
                    return response()->json([
                        'status' => 2
                    ]);
                }
            }
            else {
                return response()->json([
                    'status' =>1
                ]);
            }
        }
        public function email_all(Request $request)
        {
            if($request->department=="-99")
            {
                $users = User::select('id')->get();
                $cycle = '';
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                if($request->cycle=='14')
                {
                    $cycle = 'Fortnightly';
                }
                else
                {
                    $cycle = 'Monthly';
                }
                $processed_count = Proceed::where('start_date',$request->start_date)->where('type','Hourly')->where('end_date',$request->end_date)->where('cycle',$cycle)->count();

            }
            else {
                $users = User::select('id','department')->where('department',$request->department)->get();
                $dep_name = Department::select('department')->where('id',$users[0]["department"])->get()->toArray();
                $cycle = '';
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                if($request->cycle=='14')
                {
                    $cycle = 'Fortnightly';
                }
                else
                {
                    $cycle = 'Monthly';
                }
                $processed_count = Proceed::where('start_date',$request->start_date)->where('type','Hourly')->where('end_date',$request->end_date)->where('dept',$dep_name[0]["department"])->where('cycle',$cycle)->count();

            }
            if(count($users)!=$processed_count)
            {
                return response()->json(
                    [
                        'status' => 2
                    ]
                    );
            }
            else 
            {
                foreach($users as $us)
                {
                    $user_id = $us["id"];
                    $user_email = User::select('email')->where('id',$user_id)->get()->toArray();
                    $basic_hour = '';
                    $holiday_hour = '';
                    if(count($user_email)>0)
                    {
                        $print_rec = Proceed::where('start_date',$start_date)->where('end_date',$end_date)->where('user_id',$user_id)->where('cycle',$cycle)->get()->toArray();
                        if(count($print_rec)>0)
                        {
                            //Basic Hour
                            $tempB = $print_rec[0]['basic_hour'];
                            if(Str::contains($tempB,":"))
                            {
                                $b_h = explode(":",$tempB);
                                $basic_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempB,"."))
                            {
                                $basic_hour = $tempB;
                            }
                            else {
                                $basic_hour = $tempB.'.0';
                            }
                            //Basic Pay
                            $tempP = $print_rec[0]['basic_pay'];
                            $basic_pay = $tempP;

                            //Attendance Incentive Hour
                            $tempA = $print_rec[0]['atten_hour'];
                            $atten_hour = '';
                            if(Str::contains($tempA,":"))
                            {
                                $b_h = explode(":",$tempA);
                                $atten_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempA,"."))
                            {
                                $atten_hour = $tempA;
                            }
                            else {
                                $atten_hour = $tempA.'.0';
                            }
                            //Attendance Incentive Pay
                            $tempP = $print_rec[0]['atten_pay'];
                            $atten_pay = $tempP;

                            //Sick Hour
                            $tempS = $print_rec[0]['sick_hour'];
                            $sick_hour = '';
                            if(Str::contains($tempS,":"))
                            {
                                $b_h = explode(":",$tempS);
                                $sick_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempS,"."))
                            {
                                $sick_hour = $tempS;
                            }
                            else {
                                $sick_hour = $tempS.'.0';
                            }
                            //Sick Pay
                            $tempS = $print_rec[0]['sick_pay'];
                            $sick_pay = $tempS;

                            //Vacation Hour
                            $tempS = $print_rec[0]['vacation_hour'];
                            $vacation_hour = '';
                            if(Str::contains($tempS,":"))
                            {
                                $b_h = explode(":",$tempS);
                                $vacation_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempS,"."))
                            {
                                $vacation_hour = $tempS;
                            }
                            else {
                                $vacation_hour = $tempS.'.0';
                            }
                            //Vacation Pay
                            $tempS = $print_rec[0]['vacation_pay'];
                            $vacation_pay = $tempS;

                            //Maternity Hour
                            $tempS = $print_rec[0]['maternity_hour'];
                            $maternity_hour = '';
                            if(Str::contains($tempS,":"))
                            {
                                $b_h = explode(":",$tempS);
                                $maternity_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempS,"."))
                            {
                                $maternity_hour = $tempS;
                            }
                            else {
                                $maternity_hour = $tempS.'.0';
                            }
                            //Maternity Pay
                            $tempS = $print_rec[0]['maternity_pay'];
                            $maternity_pay = $tempS;
                            //Holiday Hour
                            $tempS = $print_rec[0]['holiday_hour'];
                            if(Str::contains($tempS,":"))
                            {
                                $b_h = explode(":",$tempS);
                                $holiday_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempS,"."))
                            {
                                $holiday_hour = $tempS;
                            }
                            else {
                                $holiday_hour = $tempS.'.0';
                            }
                            //Holiday Pay
                            $tempS = $print_rec[0]['holiday_pay'];
                            $holiday_pay = $tempS;
                            //Overtime Hour
                            $tempS = $print_rec[0]['overtime_hour'];
                            $overtime_hour = '';
                            if(Str::contains($tempS,":"))
                            {
                                $b_h = explode(":",$tempS);
                                $overtime_hour = $b_h[0].".".$b_h[1];
                            }
                            else if(Str::contains($tempS,"."))
                            {
                                $overtime_hour = $tempS;
                            }
                            else {
                                $overtime_hour = $tempS.'.0';
                            }
                            //Overtime Pay
                            $tempS = $print_rec[0]['overtime_pay'];
                            $overtime_pay = $tempS;


                            //Bonus Names and Bonus Values
                            $bonus = Bonuse::where('start_date',$start_date)->where('end_date',$end_date)->where('user_id',$user_id)->where('bonus','>',0)->get()->toArray();
                            $bonus_name = [];
                            $bonus_p = [];
                            foreach($bonus as $b)
                            {
                                array_push($bonus_name,$b['bonus_name']);
                                array_push($bonus_p,$b['bonus']);
                            }
                            //Bonus Pay
                            $bonusPay = $print_rec[0]['bonus'];

                            //NIS
                            $nis_ded = $print_rec[0]['nis'];
                            $nht_ded = $print_rec[0]['nht'];
                            $edtax_ded = $print_rec[0]['edtax'];

                            //One Time Deduction Name and Values

                            $onetime_id = $print_rec[0]['onetime_id'];
                            $onetime_name = [];
                            $onetime_pay = [];

                            if(Str::contains($onetime_id,','))
                            {
                                $replaced = Str::replaceFirst(',', '', $onetime_id);
                                $onetime_arr = explode(',',$replaced);
                                foreach($onetime_arr as $one)
                                {
                                    $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$one)->get()->toArray();
                                    array_push($onetime_name,$onetimeData[0]->deduction_name);
                                    array_push($onetime_pay,$onetimeData[0]->amount);
                                }
                            }
                            else if($onetime_id=='')
                            {

                            }
                            else {
                                $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$onetime_id)->get()->toArray();
                                    array_push($onetime_name,$onetimeData[0]->deduction_name);
                                    array_push($onetime_pay,$onetimeData[0]->amount);
                            }
                            $onetimePay = $print_rec[0]['one_time'];


                            //Continuous
                            $continuous_id = $print_rec[0]['continuous_id'];
                            $continuous_name = [];
                            $continuous_pay = [];
                            if(Str::contains($continuous_id,','))
                            {
                                $replaced = Str::replaceFirst(',', '', $continuous_id);
                                $continuous_arr = explode(',',$replaced);
                                foreach($continuous_arr as $continuous)
                                {
                                    $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous)->get()->toArray();
                                    array_push($continuous_name,$continuousData[0]->deduction_name);
                                    array_push($continuous_pay,$continuousData[0]->amount);
                                }
                            }
                            else if($continuous_id=='')
                            {

                            }
                            else {
                                $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous_id)->get()->toArray();
                                    array_push($continuous_name,$continuousData[0]->deduction_name);
                                    array_push($continuous_pay,$continuousData[0]->amount);
                            }
                            $continuous_amount = $print_rec[0]['continuous'];

                            //Loan
                            $periodic_id = $print_rec[0]['periodic_id'];
                            $periodic_name = [];
                            $periodic_pay = [];
                            if(Str::contains($periodic_id,','))
                            {
                                $replaced = Str::replaceFirst(',', '', $periodic_id);
                                $periodic_arr = explode(',',$replaced);
                                foreach($periodic_arr as $periodic)
                                {
                                    $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic)->get()->toArray();
                                    array_push($periodic_name,$periodicData[0]->deduction_name);
                                    array_push($periodic_pay,$periodicData[0]->amount);
                                }
                            }
                            else if($periodic_id=='')
                            {

                            }
                            else {
                                $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic_id)->get()->toArray();
                                    array_push($periodic_name,$periodicData[0]->deduction_name);
                                    array_push($periodic_pay,$periodicData[0]->amount);
                            }
                            $periodic_amount = $print_rec[0]['periodic'];

                            $user = User::where('id',$user_id)->get()->toArray();

                            $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(nht) as nht_total, SUM(edtax) as edtax_total'))->where("user_id", $user_id)->get()->toarray();

                            $details = [
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                            'nis' => $print_rec[0]['nis'],
                            'trn' => $user[0]["trn"],
                            'employee_id' => $user[0]["employee_id"],
                            'hourly_rate' => $user[0]["hourly_rate"],
                            'attn_inc_rate' => $user[0]["attn_inc_rate"],
                            'overtime_rate' => $user[0]["ot_rate"],
                            'name' => $user[0]["first_name"].' '.$user[0]["last_name"],
                            'date' => Carbon::now(),
                            'basic_hour' => $basic_hour,
                            'basic_pay' => $basic_pay,
                            'atten_hour' => $atten_hour,
                            'atten_pay' => $atten_pay,
                            'sick_hour' => $sick_hour,
                            'sick_pay' => $sick_pay,
                            'vacation_hour' => $vacation_hour,
                            'vacation_pay' => $vacation_pay,
                            'maternity_hour' => $maternity_hour,
                            'maternity_pay' => $maternity_pay,
                            'holiday_hour' => $holiday_hour,
                            'holiday_pay' => $holiday_pay,
                            'overtime_hour' => $overtime_hour,
                            'overtime_pay' => $overtime_pay,
                            'bonus_name' => $bonus_name,
                            'bonus_p' => $bonus_p,
                            'bonusPay' => $bonusPay,
                            'nis_ded' => $nis_ded,
                            'nht_ded' => $nht_ded,
                            'edtax_ded' => $edtax_ded,
                            'onetime_name' => $onetime_name,
                            'onetime_pay' => $onetime_pay,
                            'onetimePay' => $onetimePay,
                            'continuous_name' => $continuous_name,
                            'continuous_pay' => $continuous_pay,
                            'continuous_amount' => $continuous_amount,
                            'periodic_name' => $periodic_name,
                            'periodic_pay' => $periodic_pay,
                            'periodic_amount' => $periodic_amount,
                            'process' => $process

                            ];
                            dispatch(new EmailAll($details,$user_email[0]["email"],$user[0]["first_name"],$user[0]["last_name"]));
                        }
                    }
                }
                return response()->json([
                        'status'=>1
                    ]
                );

            }
        }
        public function email_pass(Request $request)
        {
            $email = User::select('email')->where('id',$request->user_id)->get()->toArray();
            return response()->json(
                [
                    'email_' => $email[0]["email"]
                ]
                );
        }
        public function SingleEmail(Request $request)
        {
            $user_id = $request->email_u_id;
            $user_email = User::select('email')->where('id',$user_id)->get()->toArray();
            $basic_hour = '';
            $holiday_hour = '';
            $cycle = $request->print_cycle;
            if($cycle=='14')
            {
                $cycle = 'Fortnightly';
            }
            else {
                $cycle = 'Monthly';
            }
            if(count($user_email)>0)
            {
                    $print_rec = Proceed::where('start_date',$request->print_s_d)->where('type','Hourly')->where('end_date',$request->print_e_d)->where('cycle',$cycle)->where('user_id',$request->print_u_id)->get()->toArray();
                    dd($print_rec);
            if(count($print_rec)>0)
            {
                //Basic Hour
                $tempB = $print_rec[0]['basic_hour'];
                if(Str::contains($tempB,":"))
                {
                    $b_h = explode(":",$tempB);
                    $basic_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempB,"."))
                {
                    $basic_hour = $tempB;
                }
                else {
                    $basic_hour = $tempB.'.0';
                }
                //Basic Pay
                $tempP = $print_rec[0]['basic_pay'];
                $basic_pay = $tempP;
                
                //Attendance Incentive Hour
                $tempA = $print_rec[0]['atten_hour'];
                $atten_hour = '';
                if(Str::contains($tempA,":"))
                {
                    $b_h = explode(":",$tempA);
                    $atten_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempA,"."))
                {
                    $atten_hour = $tempA;
                }
                else {
                    $atten_hour = $tempA.'.0';
                }
                //Attendance Incentive Pay
                $tempP = $print_rec[0]['atten_pay'];
                $atten_pay = $tempP;
            
                //Sick Hour
                $tempS = $print_rec[0]['sick_hour'];
                $sick_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $sick_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $sick_hour = $tempS;
                }
                else {
                    $sick_hour = $tempS.'.0';
                }
                //Sick Pay
                $tempS = $print_rec[0]['sick_pay'];
                $sick_pay = $tempS;
                
                //Vacation Hour
                $tempS = $print_rec[0]['vacation_hour'];
                $vacation_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $vacation_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $vacation_hour = $tempS;
                }
                else {
                    $vacation_hour = $tempS.'.0';
                }
                //Vacation Pay
                $tempS = $print_rec[0]['vacation_pay'];
                $vacation_pay = $tempS;
                
                //Maternity Hour
                $tempS = $print_rec[0]['maternity_hour'];
                $maternity_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $maternity_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $maternity_hour = $tempS;
                }
                else {
                    $maternity_hour = $tempS.'.0';
                }
                //Maternity Pay
                $tempS = $print_rec[0]['maternity_pay'];
                $maternity_pay = $tempS;
                //Holiday Hour
                $tempS = $print_rec[0]['holiday_hour'];
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $holiday_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $holiday_hour = $tempS;
                }
                else {
                    $holiday_hour = $tempS.'.0';
                }
                //Holiday Pay
                $tempS = $print_rec[0]['holiday_pay'];
                $holiday_pay = $tempS;
                //Overtime Hour
                $tempS = $print_rec[0]['overtime_hour'];
                $overtime_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $overtime_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $overtime_hour = $tempS;
                }
                else {
                    $overtime_hour = $tempS.'.0';
                }
                //Overtime Pay
                $tempS = $print_rec[0]['overtime_pay'];
                $overtime_pay = $tempS;
                
                
                
                //Bonus Names and Bonus Values
                $bonus = Bonuse::where('start_date',$request->print_s_d)->where('end_date',$request->print_e_d)->where('user_id',$request->print_u_id)->where('bonus','>',0)->get()->toArray();
                $bonus_name = [];
                $bonus_p = [];
                foreach($bonus as $b)
                {
                    array_push($bonus_name,$b['bonus_name']);
                    array_push($bonus_p,$b['bonus']);
                }
                //Bonus Pay
                $bonusPay = $print_rec[0]['bonus'];
                
                //NIS
                $nis_ded = $print_rec[0]['nis'];
                $nht_ded = $print_rec[0]['nht'];
                $edtax_ded = $print_rec[0]['edtax'];
                
                //One Time Deduction Name and Values
                
                $onetime_id = $print_rec[0]['onetime_id'];
                $onetime_name = [];
                $onetime_pay = [];
                
                if(Str::contains($onetime_id,','))
                {
                    $replaced = Str::replaceFirst(',', '', $onetime_id);
                    $onetime_arr = explode(',',$replaced);
                    foreach($onetime_arr as $one)
                    {
                        $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$one)->get()->toArray();
                        array_push($onetime_name,$onetimeData[0]->deduction_name);
                        array_push($onetime_pay,$onetimeData[0]->amount);
                    }
                }
                else if($onetime_id=='')
                {
                    
                }
                else {
                    $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$onetime_id)->get()->toArray();
                        array_push($onetime_name,$onetimeData[0]->deduction_name);
                        array_push($onetime_pay,$onetimeData[0]->amount);
                }
                $onetimePay = $print_rec[0]['one_time'];
                
                
                //Continuous
                $continuous_id = $print_rec[0]['continuous_id'];
                $continuous_name = [];
                $continuous_pay = [];
                if(Str::contains($continuous_id,','))
                {
                    $replaced = Str::replaceFirst(',', '', $continuous_id);
                    $continuous_arr = explode(',',$replaced);
                    foreach($continuous_arr as $continuous)
                    {
                        $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous)->get()->toArray();
                        array_push($continuous_name,$continuousData[0]->deduction_name);
                        array_push($continuous_pay,$continuousData[0]->amount);
                    }
                }
                else if($continuous_id=='')
                {
                    
                }
                else {
                    $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous_id)->get()->toArray();
                        array_push($continuous_name,$continuousData[0]->deduction_name);
                        array_push($continuous_pay,$continuousData[0]->amount);
                }
                $continuous_amount = $print_rec[0]['continuous'];
                
                //Loan
                $periodic_id = $print_rec[0]['periodic_id'];
                $periodic_name = [];
                $periodic_pay = [];
                if(Str::contains($periodic_id,','))
                {
                    $replaced = Str::replaceFirst(',', '', $periodic_id);
                    $periodic_arr = explode(',',$replaced);
                    foreach($periodic_arr as $periodic)
                    {
                        $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic)->get()->toArray();
                        array_push($periodic_name,$periodicData[0]->deduction_name);
                        array_push($periodic_pay,$periodicData[0]->amount);
                    }
                }
                else if($periodic_id=='')
                {
                    
                }
                else {
                    $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic_id)->get()->toArray();
                        array_push($periodic_name,$periodicData[0]->deduction_name);
                        array_push($periodic_pay,$periodicData[0]->amount);
                }
                $periodic_amount = $print_rec[0]['periodic'];
                
                $user = User::where('id',$user_id)->get()->toArray();
                
                $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(nht) as nht_total, SUM(edtax) as edtax_total'))->where("user_id", $request->print_u_id)->get()->toarray();
                
                $details = [
                'start_date' => $request->print_s_d,
                'end_date' => $request->print_e_d,
                'nis' => $print_rec[0]['nis'],
                'trn' => $user[0]["trn"],
                'employee_id' => $user[0]["employee_id"],
                'hourly_rate' => $user[0]["hourly_rate"],
                'attn_inc_rate' => $user[0]["attn_inc_rate"],
                'overtime_rate' => $user[0]["ot_rate"],
                'name' => $user[0]["first_name"].' '.$user[0]["last_name"],
                'date' => Carbon::now(),
                'basic_hour' => $basic_hour,
                'basic_pay' => $basic_pay,
                'atten_hour' => $atten_hour,
                'atten_pay' => $atten_pay,
                'sick_hour' => $sick_hour,
                'sick_pay' => $sick_pay,
                'vacation_hour' => $vacation_hour,
                'vacation_pay' => $vacation_pay,
                'maternity_hour' => $maternity_hour,
                'maternity_pay' => $maternity_pay,
                'holiday_hour' => $holiday_hour,
                'holiday_pay' => $holiday_pay,
                'overtime_hour' => $overtime_hour,
                'overtime_pay' => $overtime_pay,
                'bonus_name' => $bonus_name,
                'bonus_p' => $bonus_p,
                'bonusPay' => $bonusPay,
                'nis_ded' => $nis_ded,
                'nht_ded' => $nht_ded,
                'edtax_ded' => $edtax_ded,
                'onetime_name' => $onetime_name,
                'onetime_pay' => $onetime_pay,
                'onetimePay' => $onetimePay,
                'continuous_name' => $continuous_name,
                'continuous_pay' => $continuous_pay,
                'continuous_amount' => $continuous_amount,
                'periodic_name' => $periodic_name,
                'periodic_pay' => $periodic_pay,
                'periodic_amount' => $periodic_amount,
                'process' => $process
                
            ];
            $pdf = PDF::loadView('paySlipMail', $details);
            $path = Storage::put('public/storage/uploads/'.'-'.rand().'_'.time().'.'.'pdf',$pdf->output());
            Mail::send('paySlipMail', $details, function($message)use($details,$user_email, $pdf,$path,$user) {
                $message->to($user_email[0]["email"], $user_email[0]["email"])
                ->subject("Mail From HCTIMER");
                $message->attachData($pdf->output(),$path, [
                    'mime' => 'application/pdf',
                    'as' => $user[0]["first_name"].' '.$user[0]["last_name"].'.'.'pdf'
                ]);
            });
            
            return redirect()->route('admin-payroll')->with('message','Email Sent Successfully!!');
            
            }
            else {
                return response()->json([
                    'status'=>'1'    
                ]
                );
            }
            
            }
            return response()->json(
                [
                    'status'=>1
                ]
                );
        }
        public function HourlyPrint(Request $request)
        {
            $print_rec = Proceed::where('start_date',$request->print_s_d)
            ->where('end_date',$request->print_e_d)
            ->where('user_id',$request->print_u_id)->get()->toArray();
            $basic_hour = '';
            $holiday_hour = '';
            if(count($print_rec)>0)
            {
                //Basic Hour
                $tempB = $print_rec[0]['basic_hour'];
                if(Str::contains($tempB,":"))
                {
                    $b_h = explode(":",$tempB);
                    $basic_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempB,"."))
                {
                    $basic_hour = $basic_hour;
                }
                else {
                    $basic_hour = $basic_hour.'.0';
                }
                //Basic Pay
                $tempP = $print_rec[0]['basic_pay'];
                $basic_pay = $tempP;
                
                //Attendance Incentive Hour
                $tempA = $print_rec[0]['atten_hour'];
                $atten_hour = '';
                if(Str::contains($tempA,":"))
                {
                    $b_h = explode(":",$tempA);
                    $atten_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempA,"."))
                {
                    $atten_hour = $atten_hour;
                }
                else {
                    $atten_hour = $atten_hour.'.0';
                }
                //Attendance Incentive Pay
                $tempP = $print_rec[0]['atten_pay'];
                $atten_pay = $tempP;
            
                //Sick Hour
                $tempS = $print_rec[0]['sick_hour'];
                $sick_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $sick_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $sick_hour = $sick_hour;
                }
                else {
                    $sick_hour = $sick_hour.'.0';
                }
                
                //Sick Pay
                $tempS = $print_rec[0]['sick_pay'];
                $sick_pay = $tempS;
                
                //Vacation Hour
                $tempS = $print_rec[0]['vacation_hour'];
                $vacation_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $vacation_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $vacation_hour = $vacation_hour;
                }
                else {
                    $vacation_hour = $vacation_hour.'.0';
                }
                //Vacation Pay
                $tempS = $print_rec[0]['vacation_pay'];
                $vacation_pay = $tempS;
                
                //Maternity Hour
                $tempS = $print_rec[0]['maternity_hour'];
                $maternity_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $maternity_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $maternity_hour = $maternity_hour;
                }
                else {
                    $maternity_hour = $maternity_hour.'.0';
                }
                //Maternity Pay
                $tempS = $print_rec[0]['maternity_pay'];
                $maternity_pay = $tempS;
                //Holiday Hour
                $tempS = $print_rec[0]['holiday_hour'];
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $holiday_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $holiday_hour = $holiday_hour;
                }
                else {
                    $holiday_hour = $holiday_hour.'.0';
                }
                //Holiday Pay
                $tempS = $print_rec[0]['holiday_pay'];
                $holiday_pay = $tempS;
                //Overtime Hour
                $tempS = $print_rec[0]['overtime_hour'];
                $overtime_hour = '';
                if(Str::contains($tempS,":"))
                {
                    $b_h = explode(":",$tempS);
                    $overtime_hour = $b_h[0].".".$b_h[1];
                }
                else if(Str::contains($tempS,"."))
                {
                    $overtime_hour = $overtime_hour;
                }
                else {
                    $overtime_hour = $overtime_hour.'.0';
                }
                //Overtime Pay
                $tempS = $print_rec[0]['overtime_pay'];
                $overtime_pay = $tempS;
                
                
                
                //Bonus Names and Bonus Values
                $bonus = Bonuse::where('start_date',$request->print_s_d)->where('end_date',$request->print_e_d)->where('user_id',$request->print_u_id)->where('bonus','>',0)->get()->toArray();
                $bonus_name = [];
                $bonus_p = [];
                foreach($bonus as $b)
                {
                    array_push($bonus_name,$b['bonus_name']);
                    array_push($bonus_p,$b['bonus']);
                }
                //Bonus Pay
                $bonusPay = $print_rec[0]['bonus'];
                
                //NIS
                $nis_ded = $print_rec[0]['nis'];
                $nht_ded = $print_rec[0]['nht'];
                $edtax_ded = $print_rec[0]['edtax'];
                
                //One Time Deduction Name and Values
                
                $onetime_id = $print_rec[0]['onetime_id'];
                $onetime_name = [];
                $onetime_pay = [];
                
                if(Str::contains($onetime_id,','))
                {
                    $replaced = Str::replaceFirst(',', '', $onetime_id);
                    $onetime_arr = explode(',',$replaced);
                    foreach($onetime_arr as $one)
                    {
                        $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$one)->get()->toArray();
                        array_push($onetime_name,$onetimeData[0]->deduction_name);
                        array_push($onetime_pay,$onetimeData[0]->amount);
                    }
                }
                else if($onetime_id=='')
                {
                    
                }
                else {
                    $onetimeData = DB::table('one_time_deduction')->select('deduction_name','amount')->where('id',$onetime_id)->get()->toArray();
                        array_push($onetime_name,$onetimeData[0]->deduction_name);
                        array_push($onetime_pay,$onetimeData[0]->amount);
                }
                $onetimePay = $print_rec[0]['one_time'];
                
                
                //Continuous
                $continuous_id = $print_rec[0]['continuous_id'];
                $continuous_name = [];
                $continuous_pay = [];
                if(Str::contains($continuous_id,','))
                {
                    $replaced = Str::replaceFirst(',', '', $continuous_id);
                    $continuous_arr = explode(',',$replaced);
                    foreach($continuous_arr as $continuous)
                    {
                        $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous)->get()->toArray();
                        array_push($continuous_name,$continuousData[0]->deduction_name);
                        array_push($continuous_pay,$continuousData[0]->amount);
                    }
                }
                else if($continuous_id=='')
                {
                    
                }
                else {
                    $continuousData = DB::table('continuous_deduction')->select('deduction_name','amount')->where('id',$continuous_id)->get()->toArray();
                        array_push($continuous_name,$continuousData[0]->deduction_name);
                        array_push($continuous_pay,$continuousData[0]->amount);
                }
                $continuous_amount = $print_rec[0]['continuous'];
                
                //Loan
                $periodic_id = $print_rec[0]['periodic_id'];
                $periodic_name = [];
                $periodic_pay = [];
                if(Str::contains($periodic_id,','))
                {
                    $replaced = Str::replaceFirst(',', '', $periodic_id);
                    $periodic_arr = explode(',',$replaced);
                    foreach($periodic_arr as $periodic)
                    {
                        $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic)->get()->toArray();
                        array_push($periodic_name,$periodicData[0]->deduction_name);
                        array_push($periodic_pay,$periodicData[0]->amount);
                    }
                }
                else if($periodic_id=='')
                {
                    
                }
                else {
                    $periodicData = DB::table('loan')->select('deduction_name','amount')->where('id',$periodic_id)->get()->toArray();
                        array_push($periodic_name,$periodicData[0]->deduction_name);
                        array_push($periodic_pay,$periodicData[0]->amount);
                }
                $periodic_amount = $print_rec[0]['periodic'];
                
                $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(nht) as nht_total, SUM(edtax) as edtax_total'))->where("user_id", $request->print_u_id)->get()->toarray();
                
            }
            else {
                return redirect()->back()->with('error',"Payroll Not Process Yet!!");
            }
            
            
            
            $data_time = Carbon::now();
            $start_period = $request->print_s_d;
            $end_period = $request->print_e_d;
            $user_id = $request->print_u_id;
            $user = User::where('id',$user_id)->get()->toArray();
            // return redirect('hourly_print', compact('basic_hour', 'basic_pay'));
            return view('Admin.hourly_print',get_defined_vars());
        }
        public function temp_deduction(Request $request,$id)
        {
            $cycle = $request->cycle;
            $total = $request->total;
            $start_date = $request->s_date;
            $end_date = $request->e_date;
            $deduction = Deduction::get()->toarray();
            $input_val = $request->input_val;
            $hou_r = $request->hourly_rate;
            $overtime_r = $request->overtime_rate;
            $atten_r = $request->atten_rate;
            $bonus_pay = $request->bonus_pay;
            
            
            
            //Holiday
            $str_res = Str::contains($input_val,'.');
            if(!$str_res)
            {
                $input_val = $input_val.".0";
            }
            
            $total_hours = explode(".",$input_val);
            $basic_hour;
            $basic_min;
            $basic_time;
            $overtime_hour;
            $overtime_min;
            $overtime_time;
            if($total_hours[0]>80)
            {
                    $basic_hour = 80;
                    $basic_min = 0;
                    $basic_time = 80.0;
                    
                    $overtime_hour = $total_hours[0]-80;
                    $overtime_min = $total_hours[1];
                    $overtime_time = $overtime_hour.'.'.$total_hours[1];
            }
            else if($total_hours[0]==80 && $total_hours[1] > 0) {
                    $basic_hour = 80;
                    $basic_min = 0;
                    $basic_time = 80.0;
                    
                    $overtime_hour = $total_hours[0]-80;
                    $overtime_min = $total_hours[1];
                    $overtime_time = $overtime_hour.'.'.$total_hours[1];
            }
            else {
                    $basic_hour = $total_hours[0];
                    $basic_min = $total_hours[1];
                    $basic_time = $input_val;
                    
                    $overtime_hour = 0;
                    $overtime_min = 0;
                    $overtime_time = 0.0;
            }

            $holiday_time = DB::table('holiday_pays')->selectRaw('SUM(total_time) as t_t')->where('user_id',$id)->where('date','>=',$start_date)->where('date','<=',$end_date)->get();
            
            
            $holiday_hour = intval(intval($holiday_time[0]->t_t)/3600);
            $temp = $holiday_hour*3600;
            $temp2 = $holiday_time[0]->t_t - $temp;
            $holiday_min = intval($temp2/60);
            $holiday_min = floatval($holiday_min/60);
            $temp_holiday = explode(".",$holiday_min);
            
            $holiday_h;
            if(count($temp_holiday)==1)
            {
                $holiday_h = $holiday_hour.'.'.$temp_holiday[0];
                
            }
            else {
                $holiday_h = $holiday_hour.'.'.$temp_holiday[1];
            }
            

            
            //sick leave
            $sick_data = 0;
            $sick_count = SickLeave::where('user_id',$id)->where('leave_date','>=',$start_date)->where('leave_date','<=',$end_date)->where('status','1')->count();
            if($sick_count>0)
            {
                
                // $sick_data = DB::table('attendences')->where('attendences.user_id',$id)->where('date','>=',$start_date)->where('date','<=',$end_date)->join('sick_leaves',function($join){
                //     $join->on('sick_leaves.leave_date','>=','attendences.date');
                //     $join->on('sick_leaves.leave_date','<=','attendences.date');
                // })->where('sick_leaves.user_id',$id)->selectRaw('SUM(attendences.total_hours) as t_h')->get();
                
                $sick_data = 28800*$sick_count;
                
            }
            $sick_hour = intval(intval($sick_data)/3600);
            $temp = $sick_hour*3600;
            $temp2 = $sick_data - $temp;
            $sick_min = intval($temp2/60);
            $sick_min = floatval($sick_min/60);
            $temp_sick = explode(".",$sick_min);
            
            $sick_time;
            if(count($temp_sick)==1)
            {
                $sick_time = $sick_hour.'.'.$temp_sick[0];
                
            }
            else {
                $sick_time = $sick_hour.'.'.$temp_sick[1];
            }
            
            
            
            
            $vacation_data = 0;
            $vacation_count = VacationLeave::where('user_id',$id)->where('leave_date','>=',$start_date)->where('leave_date','<=',$end_date)->where('status','1')->count();
            
            if($vacation_count>0)
            {
                // $vacation_data = DB::table('attendences')->where('attendences.user_id',$id)->where('date','>=',$start_date)->where('date','<=',$end_date)->join('vacation_leaves',function($join){
                //     $join->on('vacation_leaves.leave_date','>=','attendences.date');
                //     $join->on('vacation_leaves.leave_date','<=','attendences.date');
                // })->where('vacation_leaves.user_id',$id)->selectRaw('SUM(attendences.total_hours) as t_h')->get();
                $vacation_data = 28800*$vacation_count;
            }
            
            
            
            $vacation_hour = intval($vacation_data)/3600;
            $temp = $vacation_hour*3600;
            $temp2 = $vacation_data - $temp;
            $vacation_min = intval($temp2/60);
            $vacation_min = floatval($sick_min/60);
            
            $vacationTime;
            $temp_sick = explode(".",$vacation_min);
            if(count($temp_sick)==1)
            {
                $vacationTime = $vacation_hour.'.'.$temp_sick[0];
                
            }
            else {
                $vacationTime = $vacation_hour.'.'.$temp_sick[1];
            }
            
            
            // $basic_time = round(floatval($basic_time) - floatval($vacationTime),2);
            
            
            $maternity_date = Maternity::where('user_id',$id)->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)->get()->toArray();
                if(count($maternity_date)==0)
                {
                    $maternity_date = Maternity::where('user_id',$id)->where('start_date','<=',$start_date)->where('end_date','<=',$end_date)->get()->toArray();
                }
                $tempCount = 0;
                $total_time = 0;
                $user_time = '';
                if(count($maternity_date)>0)
                {
                    $temp_s = $start_date;
                    $temp_e = $end_date;
                    
                    $s_temp = $maternity_date[0]['start_date'];
                    $e_temp = $maternity_date[0]['end_date'];
                    

                
                    while($temp_s<=$temp_e)
                    {
                        if($temp_s>=$s_temp && $temp_s<=$e_temp)
                        {
                            $tempCount++;
                            $user_time = DB::table('attendences')->select('work_and_overtime')->where('user_id',$id)->where('date',$temp_s)->get()->toArray();
                            if(count($user_time)>0)
                            {
                                $total_time = $total_time+intval($user_time[0]->work_and_overtime);
                                $date = strtotime($temp_s);
                                $date = strtotime("+1 day", $date);
                                $date = date('Y-m-d', $date);
                                $temp_s = $date;
                            }
                            else{
                                $date = strtotime($temp_s);
                                $date = strtotime("+1 day", $date);
                                $date = date('Y-m-d', $date);
                                $temp_s = $date;
                            }
                                
                        }
                        else{
                            $date = strtotime($temp_s);
                            $date = strtotime("+1 day", $date);
                            $date = date('Y-m-d', $date);
                            $temp_s = $date;
                        }
                    }
                }
                $maternityHour = $total_time/3600;
                $maternityMin = $total_time-($maternityHour*3600);
                $maternityPay = round(($maternityHour*$hou_r)+(round(($maternityMin/60),2)*$hou_r),2);
                
                
            
            
            $b_p = floatval($basic_time*$hou_r);
            $atten_pay = floatval($basic_time*$atten_r);
            $o_p = floatval($overtime_time*$overtime_r);
            $sick_pay = floatval($sick_time*$hou_r);
            $vacation_pay = floatval($vacationTime*$hou_r);
            $holiday_pay = floatval($holiday_h*$hou_r);
            
            $total_pay = floatval($b_p)+floatval($atten_pay)+floatval($o_p)+floatval($sick_pay)+floatval($vacation_pay)+floatval($holiday_pay)+floatval($bonus_pay);
            
            $total = $total_pay;
            
            //nis
            $nis_value_percentage = $deduction[0]['nis_fix_value'];
            $nis_limit_value = $deduction[0]['nis'];
            $Nis = (round($total,2) / 100) * floatval($nis_value_percentage);
            if ($Nis > $nis_limit_value) {
                $Nis = floatval($nis_limit_value);
            }
            //nht
            $nht_value_percentage = $deduction[1]['nis_fix_value'];
            $Nht = round((round($total, 2) / 100) * floatval($nht_value_percentage), 2);
            //edtax
            $edtax_value_percentage = $deduction[2]['nis_fix_value'];
            $EdTax = round(((round($total, 2) - $Nis) / 100) * floatval($edtax_value_percentage), 2);
            
            $heart_value_percentage = $deduction[5]['nis_fix_value'];
            $heart = round((round($total, 2) / 100) * floatval($heart_value_percentage), 2);
            
            $acc_flag = false;
            $inc_tax = 0;
                if(intval($cycle)==14)
                {
                    $acc = Accumulate::where('start_date',$start_date)->where('end_date',$end_date)->get('accumalative_payrol_value')->toArray();
                    if(count($acc)==0)
                    {
                        return response()->json([
                            'status'=>2
                            ]
                            );
                    }
                    $acc_flag = false;
                }
                else {
                    $acc = DB::table('accumulates_monthly')->where('start_date',$start_date)->where('end_date',$end_date)->get('accumulate_value')->toArray();
                    $acc_flag = true;
                }
                if(!$acc_flag)
                {
                    $acc_val = intval($acc[0]['accumalative_payrol_value']);
                }
                else {
                    $acc_val = intval($acc[0]->accumulate_value);
                }
                $process_count = Proceed::where("user_id", $id)->count();
               
                $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(income) as income_tax'))->where("user_id", $id)->where('start_date','<=',$start_date)->where('end_date','<=',$end_date)->get()->toarray();
                
                $check_status = Proceed::where("user_id", $id)->where('start_date', $start_date)
                        ->where('end_date', $end_date)->count();
                $incomeTaxThreshold = Deduction::select('nis_fix_value')->where('name', 'income tax')->first();
                $incomeTaxPercentage = intval($incomeTaxThreshold->nis_fix_value);
                if ($process_count == 0) {
                        $tot_sal = $total;
                        $tot_nis = intval($Nis);
                } else {
                    // dd($process[0]['total_pay']);
                        $tot_sal = $process[0]['total_pay'] + $total;
                        $tot_nis = $process[0]['nis_total'] + $Nis;
                }
                if ($tot_sal > $acc_val) {
                        $income = floatval(($tot_sal - $tot_nis - $acc_val));
                        $inc_tax = floatval(($income / 100) * $incomeTaxPercentage);
                        if($inc_tax < 0)
                        {
                            $inc_tax = 0;
                        }
                        $inc_tax = $inc_tax - $process[0]['income_tax'];
                        
                } else {
                        $inc_tax = 0;
                        if($process_count>0)
                        {
                            $inc_tax = $inc_tax - $process[0]['income_tax'];
                        }
                }
                $taxStatus = User::select('statutory_deductions')->where('id', $id)->first();
                if (strcmp($taxStatus->statutory_deductions, "not applicable") == 0) {
                        $inc_tax = 0;
                        $Nis = 0;
                        $Nht = 0;
                        $EdTax = 0;
                }
                
                
                $one_time_deduction = DB::table('one_time_deduction')->where('employee',$user_id)->where('salary_base','0')->where('start_period',$start_date)->where('status','0')->where('cycle',$cycle)->get()->toArray();
                $one_time_name = '';
                $one_time_id = '';
                $one_time_value = 0;
                
                foreach($one_time_deduction as $onetime)
                {
                    if(count($one_time_deduction)>1)
                    {
                    $one_time_name = $one_time_name.','.$onetime->deduction_name;
                    $one_time_id = $one_time_id.','.$onetime->id;
                    $one_time_value = floatval($one_time_value)+floatval($onetime->amount);
                    }
                    else {
                         $one_time_name = $onetime->deduction_name;
                         $one_time_id = $onetime.id;
                        $one_time_value = floatval($onetime->amount);
                    }
                }
                
                $continuous_name = '';
                $continuous_id = '';
                $continuous_value = 0.0;
                $continuous_deduction = DB::table('continuous_deduction')->where('user_id',$user_id)->where('next_period',$start_date)->where('action','0')->where('cycle',$cycle)->get()->toarray();
                foreach($continuous_deduction as $continuous)
                {
                    if(count($continuous_deduction)>1)
                    {
                    $continuous_name = $continuous_name.','.$continuous->deduction_name;
                    $continuous_id = $continuous_id.','.$continuous->id;
                    $continuous_value = floatval($continuous_value)+floatval($continuous->amount);
                    }
                    else {
                         $continuous_name = $continuous->deduction_name;
                         $continuous_id = $continuous->id;
                        $continuous_value = floatval($continuous->amount);
                    }
                }
                
                
                
                $loan_amount = 0;
                $ins_val = 0;
                $loan_flag = false;
                $pause = -1;
                $periodic_name = '';
                $periodic_id = '';
                $periodic_value = 0.0;
                $laon_data = DB::table('loan')->where('user_id',$user_id)->where('cycle',$cycle)->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)->where('remaning_period','>','0')->where('salary_base','0')->where('status','0')->where('stop','0')->get()->toArray();
                $status = count($laon_data);
                
                foreach($laon_data as $laon)
                {
                    if(count($laon_data)>1)
                    {
                    $periodic_name = $periodic_name.','.$laon->deduction_name;
                    $periodic_id = $periodic_id.','.$laon->id;
                    $periodic_value = floatval($periodic_value)+floatval($laon->amount);
                    }
                    else {
                         $periodic_name = $laon->deduction_name;
                         $periodic_id = $laon->id;
                        $periodic_value = floatval($laon->amount);
                    }
                }
            
            return response()->json([
                'status'=>1,
                'nis'=>round($Nis,2),
                'nht'=>round($Nht,2),
                'edtax'=>round($EdTax,2),
                'income_tax'=>round($inc_tax,2),
                'basic_hour'=>$basic_hour,
                'basic_min'=>$basic_min,
                'basic_time'=>floatval($basic_time),
                'overtime_hour'=>$overtime_hour,
                'overtime_min'=>$overtime_min,
                'overtime_time'=>floatval($overtime_time),
                'holiday_hour'=>$holiday_hour,
                'holiday_min'=>$holiday_min,
                'holiday_h'=>$holiday_h,
                'sick_hour'=>$sick_hour,
                'sick_min'=>$sick_min,
                'sick_time'=>$sick_time,
                'vacation_hour'=>$vacation_hour,
                'vacation_min'=>$vacation_min,
                'vacationTime'=>$vacationTime,
                'heart'=>$heart,
                'maternityHour'=>$maternityHour,
                'maternityMin'=>$maternityMin,
                'maternityPay'=>$maternityPay,
                'one_time_name'=>$one_time_name,
                'one_time_value'=>$one_time_value,
                'continuous_name'=>$continuous_name,
                'continuous_value'=>$continuous_value,
                'periodic_name'=>$periodic_name,
                'periodic_value'=>$periodic_value,
                'one_time_id'=>$one_time_id,
                'continuous_id'=>$continuous_id,
                'periodic_id'=>$periodic_id
                ]);
                
            
        }
        public function payroll(Request $request)
        {
                $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'payrol'])->count();
                if ($permision == 0) {
                        return back()->with('error', 'This Feature is restricted For You !');
                } else {
                        $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'payrol'])->first()->toarray();
                        // dd($Employepermision); die;
                }
                $threshold = Threshold::select('cycle', 'days')->distinct()->get();
                $department = Department::select('department', 'id')->get();
                $users = User::where('user_role', 'user')->select('first_name', 'id')->where('add_attendance', 1)->get();
                return view('Admin/payroll', get_defined_vars());
        }
        public function daily_payroll()
        {
            $threshold  = Threshold::get();
            $department = Department::get();
            $users = '';
            $start_date = '';
            $end_date = '';
            $cyc = '';
            $dep = '';
            $temp_check = "0";
            return view('Admin.daily_payroll',get_defined_vars());
        }
        public function daily_atten_get(Request $request)
        {
            $user_id = $request->user_id;
            $no_days = $request->no_days;
            $cycle = $request->cycle;
            $user = User::where('id',$user_id)->get()->toArray();
            $dailyPay = $user[0]["daily_pay"];
            $income_tax = 0;
            $inc_nis = 0;
            $inc_sal = 0;
            $inc_thres = 0;
            $inc_perc = 0;


            $maternity_date = Maternity::where('user_id',$user_id)->where('start_date','<=',$request->s_date)->where('end_date','>=',$request->e_date)->get()->toArray();
            if(count($maternity_date)==0)
            {
                $maternity_date = Maternity::where('user_id',$user_id)->where('start_date','<=',$request->s_date)->where('end_date','<=',$request->e_date)->get()->toArray();
            }
            $tempCount = 0;
            $total_time = 0;
            $user_time = '';
            if(count($maternity_date)>0)
            {
                $temp_s = $request->s_date;
                $temp_e = $request->e_date;

                $s_temp = $maternity_date[0]['start_date'];
                $e_temp = $maternity_date[0]['end_date'];


                while($temp_s<=$temp_e)
                {
                    if($temp_s>=$s_temp && $temp_s<=$e_temp)
                    {
                        $user_time = Attendence::where('user_id',$user_id)->where('date',$temp_s)->count();
                        if($user_time>0)
                        {
                            $tempCount++;
                            $date = strtotime($temp_s);
                            $date = strtotime("+1 day", $date);
                            $date = date('Y-m-d', $date);
                            $temp_s = $date;
                        }
                        else{
                            $date = strtotime($temp_s);
                            $date = strtotime("+1 day", $date);
                            $date = date('Y-m-d', $date);
                            $temp_s = $date;
                        }

                    }
                    else{
                        $date = strtotime($temp_s);
                        $date = strtotime("+1 day", $date);
                        $date = date('Y-m-d', $date);
                        $temp_s = $date;
                    }
                }
            }

            $no_days = $no_days - $tempCount;
            $maternity_pay = $dailyPay*$tempCount;


            $vacation_date = VacationLeave::where('user_id',$user_id)->where('leave_date','>=',$request->s_date)->where('leave_date','<=',$request->e_date)->count();

            $vacation_pay = $vacation_date*$dailyPay;
            $no_days = $no_days-$vacation_date;

            $sick_data = SickLeave::where('user_id',$user_id)->where('leave_date','>=',$request->s_date)->where('leave_date','<=',$request->e_date)->count();


            $sick_pay = $sick_data*$dailyPay;
            $no_days = $no_days-$sick_data;

            $holiday = Holiday::get();
            $holiday_flag = false;
            $counter = 0;
            foreach($holiday as $h)
            {
                if (($h["holiday_date"] >= $request->s_date) && ($h["holiday_date"] <= $request->e_date)){
                    $holiday_flag = true;
                    $holiday_time = DB::table('holiday_pays')->select('total_time')->where('user_id',$user_id)->where('date',$h["holiday_date"])->get()->toArray();
                    if(count($holiday_time)>0)
                    {
                        $counter+=1;
                        $holiday_flag = true;
                    }
                }else{
                    $holiday_flag = false;
                }
            }
            $user_rate = DB::table('users')->select('hourly_rate')->where('id',$user_id)->get()->toArray();

            $rate = $user_rate[0]->hourly_rate;
            $min_rate = $rate/60;
            $holiday_total = $counter*$dailyPay;



            $dep_name = DB::table('departments')->select('department')->where('id',$user[0]["department"])->get()->toArray();

            $user_bonus = DB::table('bonuses')->select('bonus','bonus_name')->where('user_id',$user_id)->where('start_date',$request->s_date)->where('end_date',$request->e_date)->get()->toArray();
            $user_bonus_val;


            $bonus_name = '';
            $bonus = 0;


            if(count($user_bonus)>0)
            {
                foreach($user_bonus as $b_data)
                {
                    $bonus = $bonus+intval($b_data->bonus);
                    $temp = $b_data->bonus_name;
                    $bonus_name = $bonus_name.','.$b_data->bonus_name;
                }
                $bonus_name = Str::replaceFirst(",","",$bonus_name);


                $reg_pay = $dailyPay*$no_days+intval($bonus)+$holiday_total;
                $user_bonus_val = $bonus;


            }
            else {
                $reg_pay = $dailyPay*$no_days+$holiday_total;
                $user_bonus_val = 0;
            }
            $reg_pay = $reg_pay+$maternity_pay+$vacation_pay+$sick_pay;


            $onetime_deduction = DB::table('one_time_deduction')->where('cycle',$cycle)->where('employee',$user_id)->where('start_period',$request->s_date)->where('salary_base','1')->where('end_period',$request->e_date)->get();

            $onetime_name = '';
            $one_time_id = '';
            $onetime_amount = 0.0;

            foreach($onetime_deduction as $onetime)
            {
                if(count($onetime_deduction)>1)
                {
                    $onetime_name = $onetime_name.','.$onetime->deduction_name;
                    $one_time_id = $one_time_id.','.$onetime->id;
                    $onetime_amount = floatval($onetime_amount)+floatval($onetime->amount);
                }
                else {
                    $onetime_name = $onetime->deduction_name;
                    $one_time_id = $onetime.id;
                    $onetime_amount = floatval($onetime->amount);
                }
            }

            $continuous_deduction = DB::table('continuous_deduction')->where('cycle',$cycle)->where('user_id',$user_id)->where('next_period',$request->s_date)->where('salary_base','1')->get();

            $continuous_name = '';
            $continuous_id = '';
            $continuous_val = '';
            foreach($continuous_deduction as $continuous)
            {
                if(count($continuous_deduction)>1)
                {
                    $continuous_name = $continuous_name.','.$continuous->deduction_name;
                    $continuous_id = $continuous_id.','.$continuous->id;
                    $continuous_val = floatval($continuous_val)+floatval($continuous->amount);
                }
                else {
                    $continuous_name = $continuous->deduction_name;
                    $continuous_id = $continuous->id;
                    $continuous_val = floatval($continuous->amount);
                }
            }

            $periodic_name = '';
            $periodic_value = 0.0;
            $laon_data = DB::table('loan')->where('user_id',$user_id)->where('cycle',$cycle)->where('start_date','<=',$request->s_date)->where('end_date','>=',$request->e_date)->where('remaning_period','>','0')->where('salary_base','1')->where('status','0')->where('stop','0')->get()->toArray();
            $status = count($laon_data);
            $periodic_id = '';
            foreach($laon_data as $laon)
            {
                if(count($laon_data)>1)
                {
                    $periodic_name = $periodic_name.','.$laon->deduction_name;
                    $periodic_id = $periodic_id.','.$laon->id;
                    $periodic_value = floatval($periodic_value)+floatval($laon->amount);
                }
                else {
                    $periodic_name = $laon->deduction_name;
                    $periodic_id = $laon->id;
                    $periodic_value = floatval($laon->amount);
                }
            }

            $nis_value_percentage = DB::table('deductions')->select('nis_fix_value','nis')->where('name','nis')->where('type_deduction','employe_decduction')->get()->toArray();
            $Nis = ($reg_pay / 100) * floatval($nis_value_percentage[0]->nis_fix_value);
            if ($Nis > floatval($nis_value_percentage[0]->nis)) {
                $Nis = floatval($nis_value_percentage[0]->nis);
            }

            $nis_value_percentage = DB::table('deductions')->select('nis_fix_value','nis')->where('name','Heart')
                ->where('type_deduction','employe_contribition')->get()->toArray();
            $Heart = ($reg_pay / 100) * floatval($nis_value_percentage[0]->nis_fix_value);


            $nht_value_percentage = DB::table('deductions')->select('nis_fix_value')->where('name','nht')->where('type_deduction','employe_decduction')->get()->toArray();
            $Nht = round((round($reg_pay, 2) / 100) * intval($nht_value_percentage[0]->nis_fix_value), 2);

            $edtax_value_percentage = DB::table('deductions')->select('nis_fix_value')->where('name','edtax')->where('type_deduction','employe_decduction')->get()->toArray();
            $edtax = round(((round($reg_pay, 2) - $Nis) / 100) * floatval($edtax_value_percentage[0]->nis_fix_value), 2);


            $is_applicable = DB::table('users')->select('statutory_deductions')->where('id',$user_id)->get();
            $cmp = strcmp($is_applicable[0]->statutory_deductions,"applicable");
            if($cmp==0)
            {
                $count = Proceed::where('user_id',$user_id)->count();
                if($count==0)
                {
                    $temp = Threshold::where('days',$request->cycle)->get()->toArray();
                    $temp2 = DB::table('deductions')->select('nis_fix_value')->where('name','income tax')->where('type_deduction','employe_decduction')->get()->toArray();
                    $temp3 = DB::table('accumulates')->get()->last();
                    if($reg_pay > $temp3->accumalative_payrol_value)
                    {
                        $inc_nis = $Nis;
                        $inc_sal = $reg_pay;
                        $inc_thres = $temp[0]["amount"];
                        $inc_perc = $temp2[0]->nis_fix_value;

                        $income_tax = (($inc_sal - $inc_nis - $inc_thres )/100)*$inc_perc;
                    }
                    else{
                        $income_tax = 0;
                    }

                }
                else {
                    $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total '))->where("user_id",$user_id)->get()->toarray();
                    $temp = Threshold::where('days',$request->cycle)->get()->toArray();
                    $temp2 = DB::table('deductions')->select('nis_fix_value')->where('name','income tax')->where('type_deduction','employe_decduction')->get()->toArray();
                    $acc_flag = false;
                    $acc = '';
                    if(intval($cycle)==14)
                    {
                        $acc = Accumulate::get('accumalative_payrol_value')->last();
                        $acc_flag = false;
                    }
                    else {
                        $acc = DB::table('accumulates_monthly')->get('accumulate_value')->last();
                        $acc_flag = true;
                    }

                    if(!$acc_flag)
                    {
                        $acc_val = intval($acc['accumalative_payrol_value']);
                    }
                    else {
                        $acc_val = intval($acc->accumulate_value);
                    }
                    if($reg_pay > intval($temp[0]["amount"]))
                    {
                        $inc_nis = $Nis+$process[0]["nis_total"];
                        $inc_sal = $reg_pay+$process[0]["total_pay"];
                        $inc_thres = $acc_val;
                        $inc_perc = intval($temp2[0]->nis_fix_value);

                        $income_tax = (($inc_sal - $inc_nis - $inc_thres )/100)*$inc_perc;
                    }
                    else{
                        $income_tax = 0;
                    }
                }
            }
            else
            {
                $Nis = 0;
                $Nht = 0;
                $edtax = 0;
                $income_tax = 0;
            }
            if($continuous_val=='')
            {
                $continuous_val = 0;
            }
            if($onetime_amount=='')
            {
                $onetime_amount = 0;
            }
            if($periodic_value == '')
            {
                $periodic_value = 0;
            }
            return response()->json([
                'dep' => $dep_name[0]->department,'daily_pay' => $user[0]["daily_pay"],
                'name' => $user[0]["first_name"]." ".$user[0]["last_name"],
                'trn' =>$user[0]["trn"], 'nis_val' =>$user[0]["nis"], 'reg_pay' => $reg_pay, 'nis' => $Nis,
                'nht' => $Nht, 'edtax' => $edtax,'income_tax' => round($income_tax,2),
                'bonus' => $user_bonus_val,'cycle' => $cycle,'counter' => $counter,
                'h_t'=>round($holiday_total,2),'m_days' => $tempCount, 'm_pay' => $maternity_pay,
                'v_pay' => $vacation_pay, 'v_days' => $vacation_date,'no_days' => $no_days,'s_pay' => $sick_pay,
                's_days' => $sick_data, 'bonus_name' => $bonus_name,'onetime_name'=>$onetime_name,
                'onetime_amount'=>$onetime_amount,'continuous_name'=>$continuous_name,
                'continuous_val'=>$continuous_val,'periodic_name'=>$periodic_name,
                'periodic_value'=>$periodic_value,
                'heart'=>$Heart,'one_time_id'=>$one_time_id,
                'continuous_id'=>$continuous_id,
                'periodic_id'=>$periodic_id
            ]);
        }

        public function daily_proceed(Request $request)
        {
            $id = $request->user_id;
            $s_date = $request->start_date;
            $e_date = $request->end_date;
            $cycle = $request->cycle;
            $daily_rate = $request->daily_rate;
            $nis = $request->nis;
            $trn = $request->trn;
            $bonus = $request->bonus_pay;
            $name = $request->emp_name;
            $dep = $request->dept;
            $no_days = $request->no_days;
            $total_daily = $request->total_reg;
            $bonus_name = $request->bonus_name;
            $sick_days = $request->sick_day;
            $sick_pay = $request->sick_total;
            $vacation_days = $request->vacation_day;
            $vacation_pay = $request->vacation_total;
            $maternity_days = $request->maternity_day;
            $maternity_pay = $request->maternity_total;
            $holiday_days = $request->holiday_day;
            $holiday_pay = $request->holiday_total;
            $ded_nis = $request->ded_nis;
            $ded_nht = $request->ded_nht;
            $ed_tax = $request->ed_tax;
            $income_tax = $request->income_tax;
            $one_time_name = $request->one_time_name;
            $one_time_ded = $request->one_time_ded;
            $continuous_name = $request->continuous_name;
            $continuous_ded = $request->continuous_ded;
            $periodic_name = $request->periodic_name;
            $periodic_ded = $request->periodic_ded;
            $total_deduction = $request->total_deduction;
            $netPay = $request->netPay;
            $gross_pay = $request->gross_pay;


            $p_d_c = DB::table('loan')->where('user_id',$id)
                ->where('start_date','<=',$s_date)->where('end_date','>=',$e_date)
                ->where('remaning_period','>','0')->where('status','0')->where('stop','0')
                ->where('salary_base','1')->where('cycle',$cycle)->get()->toArray();
            if(count($p_d_c)>0)
            {
                $temp_rem = intval($p_d_c[0]->remaning_period);
                $temp_rem = $temp_rem-1;
                DB::table('loan')->where('user_id',$id)->where('start_date','<=',$s_date)
                    ->where('end_date','>=',$e_date)->where('salary_base','1')
                    ->where('status','0')->where('stop','0')->update([
                    'remaning_period'=>$temp_rem
                ]);
            }

            $o_t_c = DB::table('one_time_deduction')->where('status','0')
                ->where('start_period',$s_date)->where('salary_base','1')
                ->where('employee',$id)->where('cycle',$cycle)->count();
            if($o_t_c>0)
            {
                DB::table('one_time_deduction')->where('status','0')
                    ->where('start_period',$s_date)->where('salary_base','1')
                    ->where('employee',$id)->where('cycle',$cycle)
                    ->update(['status'=>'1']);
            }

            $c_d_c = DB::table('continuous_deduction')->where('user_id',$id)
                ->where('next_period',$s_date)->where('salary_base','1')->where('cycle',$cycle)
                ->where('action','0')->count();
            if($c_d_c>0)
            {
                $temp_d = $s_date;
                $t_d = strtotime($temp_d);
                $temp_d = strtotime("+14 day",$t_d);

                $temp1 = strtotime($s_date);
                $date = strtotime("+14 day", $temp1);
                $temp_d = date('Y-m-d',$date);
                DB::table('continuous_deduction')->where('user_id',$id)
                    ->where('next_period',$s_date)->where('cycle',$cycle)
                    ->where('action','0')->where('salary_base','1')
                    ->update(['next_period'=>$temp_d,'temp_start'=>$s_date]);
            }

            $count = Proceed::where('start_date', $s_date)->where('end_date', $e_date)->where('user_id', $id)->count();
            if ($count > 0) {
                return 1;
            } else {
                $success = 'Successfully  payroll ADD';

                $c_year = Carbon::now()->year;
                $proceed = new Proceed();
                $proceed->user_id = $id;
                $proceed->start_date = $s_date;
                $proceed->end_date = $e_date;
                $proceed->nis = $ded_nis;
                $proceed->nht = $ded_nht;
                $proceed->edtax = $ed_tax;
                $gross_pay = Str::replace("$","",$gross_pay);
                $proceed->total_pay = $gross_pay;
                $proceed->net_pay = $netPay;
                $proceed->total_deduction = $total_deduction;
                $proceed->status = 1;
                $proceed->income = $income_tax;
                $proceed->year = $c_year;
                $bonus = Str::replace("$","",$bonus);
                $proceed->bonus = $bonus;
                $proceed->no_days = $no_days;
                $proceed->dept = $dep;
                $proceed->emp_name = $name;
                $proceed->type = "Daily";
                $proceed->one_time = $one_time_ded;
                $proceed->continuous = $continuous_ded;
                $proceed->periodic = $periodic_ded;
                $proceed->work_hours = null;
                $proceed->basic_hour = null;
                $total_daily = Str::replace("$","",$total_daily);
                $proceed->basic_pay = $total_daily;
                $proceed->atten_hour = null;
                $proceed->atten_pay = null;
                $proceed->sick_hour = $sick_days;
                $sick_pay = Str::replace("$","",$sick_pay);
                $proceed->sick_pay = $sick_pay;
                $proceed->vacation_hour = $vacation_days;
                $vacation_pay = Str::replace("$","",$vacation_pay);
                $proceed->vacation_pay = $vacation_pay;
                $proceed->maternity_hour = $maternity_days;
                $maternity_pay = Str::replace("$","",$maternity_pay);
                $proceed->maternity_pay = $maternity_pay;
                $proceed->holiday_hour = $holiday_days;
                $holiday_pay = Str::replace("$","",$holiday_pay);
                $proceed->holiday_pay = $holiday_pay;
                $proceed->overtime_hour = null;
                $proceed->overtime_pay = null;
                $proceed->bonus_name = $bonus_name;
                $proceed->onetime_name = $one_time_name;
                $proceed->continuous_name = $continuous_name;
                $proceed->onetime_id = $request->one_time_id;
                $proceed->continuous_id = $request->continuous_id;
                $proceed->periodic_id = $request->loan_id;
                $proceed->cont_nis = $request->contr_nis;
                $proceed->cont_nht = $request->contr_nht;
                $proceed->cont_edtax = $request->contr_edtax;
                $proceed->cont_heart = $request->heart;
                $proceed->periodic_name = $periodic_name;
                if(intval($cycle)==14)
                {
                    $proceed->cycle = "Fortnightly";
                }
                else {
                    $proceed->cycle = "Monthly";
                }

                $proceed->save();
                return 0;
            }
        }
        public function daily_processed_atten_get(Request $request)
        {
            $user_info = User::where('id',$request->user_id)->select('trn','nis')->get()->toArray();
            $cycle = '';
            if(intval($request->cycle)==14)
            {
                $cycle = 'Fortnightly';
            }
            else {
                $cycle = 'Monthly';
            }
            $process_date = Proceed::where('start_date',$request->s_date)->where('end_date',$request->e_date)->where('cycle',$cycle)
                ->where('user_id',$request->user_id)->get()->toArray();
            return response()->json(
                [
                    'process_data'=>$process_date,
                    'user_info'=>$user_info
                ]
            );
        }
        public function daily_search(Request $request)
        {
            $users = '';
            $temp_check = "1";
            $threshold  = Threshold::get();
            $department = Department::get();
            
            if($request->cycle && $request->start_date && $request->end_date)
            {
                $cycle = $request->cycle;
                $s_date = $request->start_date;
                $e_date = $request->end_date;
                // $users = DB::table('users')
                // ->join('attendences', function ($join) use($s_date, $e_date){
                // $join->on('users.id', '=', 'attendences.user_id')
                // ->where('attendences.date', '>=', $s_date)->where('attendences.date', '<=', $e_date);})->get()->toArray();
                
                
                $user = DB::table('users')->select('id')->where('salary_base','Daily')->get()->toArray();
                $users = array();
                foreach($user as $u)
                {
                    $temp = Attendence::where('user_id',$u->id)->where('date', '>=', $s_date)->where('date','<=',$e_date)->get()->toArray();
                    if(count($temp)>0)
                        array_push($users,$temp);   
                }
                
            }
            if($request->cycle && $request->start_date && $request->end_date && $request->DEPARTMENT)
            {
                
                $cycle = $request->cycle;
                $s_date = $request->start_date;
                $e_date = $request->end_date;
                $dep = $request->DEPARTMENT;
                // $users = DB::table('users')
                // ->join('attendences', function ($join) use($s_date, $e_date, $dep){
                // $join->on('users.id', '=', 'attendences.user_id')->where('users.department','=',$dep)->where('attendences.date', '>=', $s_date)->where('attendences.date', '<=', $e_date);})->get()->toArray();
                
                $user = DB::table('users')->select('id')->where('department',$dep)->where('salary_base','Daily')->get()->toArray();
                $users = array();
                foreach($user as $u)
                {
                    $temp = Attendence::where('user_id',$u->id)->where('date', '>=', $s_date)->where('date','<=',$e_date)->get()->toArray(); 
                    if(count($temp)>0)
                        array_push($users,$temp);   
                }
            }
            if($request->start_date)
            {
                $start_date = $request->start_date;
            }
            else {
                $start_date = '';
            }
            if($request->end_date)
            {
                $end_date = $request->end_date;
            }
            else {
                $end_date = '';
            }
            if($request->cycle)
            {
                $cyc = $request->cycle;
            }
            else {
                $cyc = '';
            }
            if($request->DEPARTMENT)
            {
                $dep = $request->DEPARTMENT;
            }
            else {
                $dep = '';
            }
            return view('Admin.daily_payroll',get_defined_vars());
            
        }
        public function search(Request $request)
        {
            $contains = Str::contains($request->cycle, '14');
            $cycle = $request->cycle;
            $dep = $request->DEPARTMENT;
            if($contains)
            {
                $min = $request->cycle - 1;
                $end_date = $request->end_date;
                $startdate = $request->start_date;
                $threshold = Threshold::select('cycle', 'days')->distinct()->get();
                $department = Department::select('department', 'id')->get();
                $users = User::where('user_role', "!=", 'admin')->select('first_name', 'id')->where('salary_base',"Hourly")->get();


                if ($request->cycle && $request->start_date && $request->DEPARTMENT && $request->end_date && $dep!="-99") {
                        // Add days to date and display it
                        $min = $request->cycle - 1;
                        $end_date = $request->end_date;
                        $startdate = $request->start_date;
                        $enddate = $end_date;

                        $userData = User::where("user_role", "!=", 'admin')->where("department",$request->DEPARTMENT)->where('salary_base',"Hourly")->get();
                        
                        $userArray = [];
                        $index = 0;
                        foreach ($userData as $data) {
                                $userId = $data->id;
                                $userName = $data->first_name." ".$data->last_name;
                                $sick_dates = [];
                                $vacation_dates = [];
                                $sick_data = SickLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$enddate)->get()->toArray();
                                
                                $vacation_data = VacationLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$enddate)->get()->toArray();
                                
                                

                                foreach($sick_data as $sick_d)
                                {
                                    array_push($sick_dates,$sick_d["leave_date"]);
                                }
                                foreach($vacation_data as $vacation_d)
                                {
                                    array_push($vacation_dates,$vacation_d["leave_date"]);
                                }
                                
                                $basicHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(work_time)) as worktime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->get()->toarray();
                                
                                $overTimeHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(overtime)) as overtime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->where("status", "1")->get()->toarray();
                                if($overTimeHourSum==null)
                                {
                                    $overTimeHourSum = 0;
                                }
                                
                                $userArray[$index] = $userId;
                                $index += 1;
                                $userArray[$index] = $userName;
                                $index += 1;
                                $userArray[$index] = $basicHourSum;
                                $index += 1;
                                $userArray[$index] = $overTimeHourSum;
                                $index += 1;

                                $tempBasicHour = $userArray[$index - 2][0]['worktime'];
                                $originalbasichours = explode('.', $tempBasicHour);
                                $tempOverHour = $userArray[$index - 1][0]['overtime'];
                                $originalOverhours = explode('.', $tempOverHour);
                                $userArray[$index - 2] = $originalbasichours[0];
                                $userArray[$index - 1] = $originalOverhours[0];
                        }
                        return view('Admin/newsearch', get_defined_vars());
                }
                if ($request->cycle && $request->start_date && $request->end_date && $dep=="-99") {
                        $checkcycle = $request->cycle;
                        $min = $request->cycle - 1;
                        // Add days to date and display it
                        $enddate = $request->end_date;
                        $startdate = $request->start_date;

                        $userData = User::where("user_role", "!=", 'admin')->where('salary_base',"Hourly")->get();
                        $userArray = [];
                        $index = 0;


                        foreach ($userData as $data) {
                                $userId = $data->id;
                                $userName = $data->first_name." ".$data->last_name;
                                $sick_dates = [];
                                $vacation_dates = [];
                                $sick_data = SickLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$end_date)->get()->toArray();
                                
                                $vacation_data = VacationLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$end_date)->get()->toArray();
                                
                                

                                foreach($sick_data as $sick_d)
                                {
                                    array_push($sick_dates,$sick_d["leave_date"]);
                                }
                                foreach($vacation_data as $vacation_d)
                                {
                                    array_push($vacation_dates,$vacation_d["leave_date"]);
                                }
                                
                                $basicHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(work_time)) as worktime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->get()->toarray();
                                
                                $overTimeHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(overtime)) as overtime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->where("status", "1")->get()->toarray();
                                if($overTimeHourSum==null)
                                {
                                    $overTimeHourSum = 0;
                                }
                                $userArray[$index] = $userId;
                                $index += 1;
                                $userArray[$index] = $userName;
                                $index += 1;
                                $userArray[$index] = $basicHourSum;
                                $index += 1;
                                $userArray[$index] = $overTimeHourSum;
                                $index += 1;
                                $tempBasicHour = $userArray[$index - 2][0]['worktime'];
                                $originalbasichours = explode('.', $tempBasicHour);
                                $tempOverHour = $userArray[$index - 1][0]['overtime'];
                                $originalOverhours = explode('.', $tempOverHour);
                                $userArray[$index - 2] = $originalbasichours[0];
                                $userArray[$index - 1] = $originalOverhours[0];
                        }

                        return view('Admin/newsearch', get_defined_vars());
                }

                return view('Admin/newsearch', compact('threshold', 'department', 'users'));
            }
            else {
                $min = $request->cycle - 1;
                $end_date = $request->end_date;
                $startdate = $request->start_date;
                $threshold = Threshold::select('cycle', 'days')->distinct()->get();
                $department = Department::select('department', 'id')->get();
                $users = User::where('user_role', "!=", 'admin')->select('first_name', 'id')->where('add_attendance', 1)->get();
                if ($request->cycle && $request->start_date && $request->DEPARTMENT && $request->end_date && $request->DEPARTMENT!="-99") {
                        // Add days to date and display it
                        $min = $request->cycle - 1;
                        $end_date = $request->end_date;

                        $startdate = $request->start_date;
                        $enddate = $end_date;

                        $userData = User::where("user_role", "!=", 'admin')->where("department",$request->DEPARTMENT)->where('salary_base',"Hourly")->get();

                        $userArray = [];
                        $index = 0;
                        foreach ($userData as $data) {
                                $userId = $data->id;
                                $userName = $data->first_name." ".$data->last_name;
                                $sick_dates = [];
                                $vacation_dates = [];
                                $sick_data = SickLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$enddate)->get()->toArray();
                                
                                $vacation_data = VacationLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$enddate)->get()->toArray();
                                
                                

                                foreach($sick_data as $sick_d)
                                {
                                    array_push($sick_dates,$sick_d["leave_date"]);
                                }
                                foreach($vacation_data as $vacation_d)
                                {
                                    array_push($vacation_dates,$vacation_d["leave_date"]);
                                }
                                
                                $basicHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(work_time)) as worktime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->get()->toarray();
                                
                                $overTimeHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(overtime)) as overtime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->where("status", "1")->get()->toarray();
                                if($overTimeHourSum==null)
                                {
                                    $overTimeHourSum = 0;
                                }
                                $userArray[$index] = $userId;
                                $index += 1;
                                $userArray[$index] = $userName;
                                $index += 1;
                                $userArray[$index] = $basicHourSum;
                                $index += 1;
                                $userArray[$index] = $overTimeHourSum;
                                $index += 1;

                                $tempBasicHour = $userArray[$index - 2][0]['worktime'];
                                $originalbasichours = explode('.', $tempBasicHour);
                                $tempOverHour = $userArray[$index - 1][0]['overtime'];
                                $originalOverhours = explode('.', $tempOverHour);
                                $userArray[$index - 2] = $originalbasichours[0];
                                $userArray[$index - 1] = $originalOverhours[0];
                        }

                        return view('Admin/newsearch', get_defined_vars());
                }
                else if ($request->cycle && $request->start_date && $request->end_date && $request->DEPARTMENT=="-99") {
                        $checkcycle = $request->cycle;
                        $min = $request->cycle - 1;
                        // Add days to date and display it
                        $enddate = $request->end_date;
                        $startdate = $request->start_date;

                        $userData = User::where("user_role", "!=", 'admin')->where('salary_base',"Hourly")->get();

                        $userArray = [];
                        $index = 0;


                        foreach ($userData as $data) {
                                $userId = $data->id;
                                $userName = $data->first_name." ".$data->last_name;

                                $sick_dates = [];
                                $vacation_dates = [];
                                $sick_data = SickLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$enddate)->get()->toArray();
                                
                                $vacation_data = VacationLeave::select('leave_date')->where('user_id',$userId)->where('leave_date','>=',$startdate)->where('leave_date','<=',$enddate)->get()->toArray();
                                
                                

                                foreach($sick_data as $sick_d)
                                {
                                    array_push($sick_dates,$sick_d["leave_date"]);
                                }
                                foreach($vacation_data as $vacation_d)
                                {
                                    array_push($vacation_dates,$vacation_d["leave_date"]);
                                }
                                
                                $basicHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(work_time)) as worktime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->get()->toarray();
                                
                                $overTimeHourSum = Attendence::select(DB::raw('SUM(TIME_TO_SEC(overtime)) as overtime'))
                                        ->where("user_id", $userId)->whereNotIn('date',$sick_dates)->whereNotIn('date',$vacation_dates)->where('date', '>=', $startdate)->where('date', '<=', $enddate)->where("status", "1")->get()->toarray();
                                if($overTimeHourSum==null)
                                {
                                    $overTimeHourSum = 0;
                                }
                                $userArray[$index] = $userId;
                                $index += 1;
                                $userArray[$index] = $userName;
                                $index += 1;
                                $userArray[$index] = $basicHourSum;
                                $index += 1;
                                $userArray[$index] = $overTimeHourSum;
                                $index += 1;
                                $tempBasicHour = $userArray[$index - 2][0]['worktime'];
                                $originalbasichours = explode('.', $tempBasicHour);
                                $tempOverHour = $userArray[$index - 1][0]['overtime'];
                                $originalOverhours = explode('.', $tempOverHour);
                                $userArray[$index - 2] = $originalbasichours[0];
                                $userArray[$index - 1] = $originalOverhours[0];
                        }

                        return view('Admin/newsearch', get_defined_vars());
                }

                return view('Admin/newsearch', compact('threshold', 'department', 'users'));
            }
                
        }

        public function PayrollStartFunc()
        {
                $payroll_s = PayrollStart::get();

                $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'payrol'])->count();
                if ($permision == 0) {
                        return redirect('/admin/dashboard')->with('error', 'This Feature is restricted For You !');
                } else {
                        $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'payrol'])->first()->toarray();
                        // dd($Employepermision); die;
                }
                return view('Admin.payroll_start', get_defined_vars());
        }
        public function AddStartDate(Request $request)
        {
                $payroll = new PayrollStart();
                $payroll->start_date = $request->start_d;
                $year = Carbon::now()->format('Y');
                $payroll->Year = $request->year;
                $payroll->Flag = '0';
                $payroll->save();
                return redirect()->back();
        }
        public function EditStartDate($id)
        {
                $payroll = PayrollStart::find($id);
                return view('Admin.edit_payroll_start', get_defined_vars());
        }
        public function UpdateStartDate(Request $request, $id)
        {
                $payroll = PayrollStart::find($id);
                $payroll->start_date = $request->s_d;
                $payroll->Flag = '0';
                $payroll->save();
                $payroll_s = PayrollStart::get();
                        return redirect('admin/payroll_start')->with('message', 'Payrolll start date updated !');
        }
        public function ViewBonus()
        {
                $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get()->toArray();
                $bonusData = "";
                $contains = Str::contains(Auth::user()->user_role, 'Supervisor');
                $dep_arr = array();
                $bonus_arr = array();
                if($contains)
                {
                    $depr = explode(",",$dep_per[0]["department_id"]);
                    foreach($depr as $dep)
                    {
                        $dep_id = DB::table('departments')->select('id')->where('department',$dep)->get()->toArray();
                       $users = DB::table('users')
                        ->select('id')->where('department',$dep_id[0]->id)->get()->toArray();
                       array_push($dep_arr,$users);
                    }
                    foreach($dep_arr as $arr)
                    {
                        foreach($arr as $a)
                        {
                            $user_bonus = Bonuse::where('user_id',$a->id)->get()->toArray(); 
                            if(count($user_bonus)!=0)
                            array_push($bonus_arr,$user_bonus);
                        }
                    }
                }
                else
                {
                    $bonusData = Bonuse::get();
                }
                $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus'])->count();
                if ($permision == 0) {
                        return back()->with('error', 'This Feature is restricted For You !');
                } else {
                        $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus'])->first()->toarray();
                        // dd($Employepermision); die;
                }
                return view('Admin.bonus',get_defined_vars())->with(compact('Employepermision', 'bonusData'));
        }
        public function UpdateBonus(Request $request)
        {
                $bonusData2 = Bonuse::where('user_id', $request->user_id)->where('start_date', $request->start_date)->where('end_date', $request->end_date)->first();
                $bonusData2->bonus = $request->bonus;
                $bonusData2->save();
                return redirect('admin/viewbonus')->with('message', 'Bonus successfully Updated!');
        }
        public function EditBonus(Request $request, $id, $start_d, $end_d)
        {
                $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus', 'edit_access' => '1'])->count();
                $full = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus', 'full_access' => '1'])->count();

                if ($full == 1 or $permision == 1) {

                        $bonusData = Bonuse::where('user_id', $id)->where('start_date', $start_d)->where('end_date', $end_d)->first();
                        return view('Admin/editBonus', compact("bonusData"));
                } else {
                        return back()->with('error', 'This Feature is restricted For You !');
                }
        }
       
       public function atten_get(Request $request)
        {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $user_id = $request->user_id;
                $user_total_hours = $request->totalhours;
                $user_total_mint = $request->totalm;
                $user_basic_hour = $request->hoursBasihourctime;
                $user_Basic_mint = '0.'.$request->minBasic;
                $user_overtime_hours = $request->hoursOverTime;
                $user_over_mint = $request->totOverTimeMin;
                $user_incometax = $request->incometax;
                $atten_get = User::where('id', $user_id)->first();
                $hourly_rate = $atten_get->hourly_rate;
                $ORP = $atten_get->ot_rate;
                $total_sal = $request->total_sala;
                $regular_hours = 80;
                $Nis = $atten_get->nis;
                $first_name = $atten_get->first_name." ".$atten_get->last_name;
                $dep_id = $atten_get->department;
                $ORP = $atten_get->ot_rate;
                $trn = $atten_get->trn;
                $cycle = $request->cycle;
                
                
                
                $attn_rate = DB::table('users')->select('attn_inc_rate')->where('id',$user_id)->get()->toArray();
                $holiday = Holiday::where('holiday_date','>=',$start_date)->where('holiday_date','<=',$end_date)->get();
                $holiday_flag = false;
                $h_t_h = 0;
                $h_t_m = 0;
                
                $sick_data = SickLeave::where('user_id',$user_id)->where('leave_date','>=',$start_date)->where('leave_date','<=',$end_date)->where('status','1')->count();
                
                $vacation_date = VacationLeave::where('user_id',$user_id)->where('leave_date','>=',$start_date)->where('leave_date','<=',$end_date)->where('status','1')->count();
                
                $maternity_date = Maternity::where('user_id',$user_id)->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)->get()->toArray();
                if(count($maternity_date)==0)
                {
                    $maternity_date = Maternity::where('user_id',$user_id)->where('start_date','<=',$start_date)->where('end_date','<=',$end_date)->get()->toArray();
                }
                $tempCount = 0;
                $total_time = 0;
                $user_time = '';
                if(count($maternity_date)>0)
                {
                    $temp_s = $start_date;
                    $temp_e = $end_date;
                    
                    $s_temp = $maternity_date[0]['start_date'];
                    $e_temp = $maternity_date[0]['end_date'];
                    

                
                    while($temp_s<=$temp_e)
                    {
                        if($temp_s>=$s_temp && $temp_s<=$e_temp)
                        {
                            $tempCount++;
                            $user_time = DB::table('attendences')->select('work_and_overtime')->where('user_id',$user_id)->where('date',$temp_s)->get()->toArray();
                            if(count($user_time)>0)
                            {
                                $total_time = $total_time+intval($user_time[0]->work_and_overtime);
                                $date = strtotime($temp_s);
                                $date = strtotime("+1 day", $date);
                                $date = date('Y-m-d', $date);
                                $temp_s = $date;
                            }
                            else{
                                $date = strtotime($temp_s);
                                $date = strtotime("+1 day", $date);
                                $date = date('Y-m-d', $date);
                                $temp_s = $date;
                            }
                                
                        }
                        else{
                            $date = strtotime($temp_s);
                            $date = strtotime("+1 day", $date);
                            $date = date('Y-m-d', $date);
                            $temp_s = $date;
                        }
                    }
                }
                $maternityHour = $total_time/3600;
                $maternityMin = $total_time-($maternityHour*3600);
                
                
                
                $vacation_hour = 0;
                if($vacation_date>0)
                {
                    $total_vacation_hour = 28800*$vacation_date;
                    $vacation_hour = $total_vacation_hour/3600;
                }
                $sick_hour = 0;
                if($sick_data>0)
                {
                    $total_sick_hour = 28800*$sick_data;
                    $sick_hour = $total_sick_hour/3600;
                }
                $count = 0;
                $tempArr = [];
                foreach($holiday as $h)
                {
                    $tempDate = $h["holiday_date"];
                    $holiday_time = DB::table('holiday_pays')->select('total_time')->where('user_id',$user_id)->where('date',$tempDate)->get()->toArray();
                        if(count($holiday_time)>0)
                        {
                            $count++;
                            $holiday_hour = intval($holiday_time[0]->total_time)/3600;
                            $temp_hour = intval($holiday_time[0]->total_time)-(intval($holiday_hour)*3600);
                                    $holiday_min = intval($temp_hour)/60;
                        
                            $h_t_h = $h_t_h+$holiday_hour;
                            $h_t_m =$h_t_m+$holiday_min;
                        }
                }
                $h_t_m = round(floatval($h_t_m/60),2);
                $user_rate = DB::table('users')->select('hourly_rate')->where('id',$user_id)->get()->toArray();
                
                $rate = $user_rate[0]->hourly_rate;
                
                $maternityPay = ($maternityHour*$rate)+(round(($maternityMin/60),2)*$rate);
                
                // $user_total_hours = $user_total_hours-$maternityHour;
                // $user_total_mint = $user_total_mint - $maternityMin;
                
                $maternityMin = floatval($maternityMin/60);
                
                $sick_pay = $sick_hour*$rate;
                $vacation_pay = $vacation_hour*$rate;
                
                $min_rate = $rate/60;
                
                $hours = intval($h_t_h);
                $min = $h_t_m;
                $h_t_h = intval($h_t_h)*$rate;
                $h_t_m = floatval($h_t_m*$rate);
                $holiday_total = floatval($h_t_h+$h_t_m);
                

                if (floor($user_total_hours) > 80) {
                   
                        $hoursOverTime = (floor($user_total_hours)) - 80;
                        $minBasic = 0;
                        $hoursBasictime = 80;
                        $totOverTimeMin = $user_total_mint;
                        $user_basic_hour = $hoursBasictime;
                        $user_Basic_mint = $minBasic;
                        $user_overtime_hours = $hoursOverTime;
                        $user_over_mint = $totOverTimeMin;
                } 
                else if($user_total_hours==80 && $user_total_mint > 0) {
                    $hoursOverTime = (floor($user_total_hours)) - 80;
                        $minBasic = 0;
                        $hoursBasictime = 80;
                        $totOverTimeMin = $user_total_mint;
                        $user_basic_hour = $hoursBasictime;
                        $user_Basic_mint = $minBasic;
                        $user_overtime_hours = $hoursOverTime;
                        $user_over_mint = $totOverTimeMin;
                }else {
                        $hoursOverTime = 0;
                        $totOverTimeMin = 0;
                        $minBasic = $user_total_mint;
                        
                        $user_basic_hour = $user_total_hours;
                        $user_overtime_hours = $hoursOverTime;
                        $user_over_mint = $totOverTimeMin;
                        $user_Basic_mint = $minBasic;
                }
                
                $total_work_hours_and_minits = $user_total_hours . ':' . $user_total_mint;
                
                $total_work_Basic_hours_and_minits = $user_basic_hour . ':' . $user_Basic_mint;
                $total_work_Over_hours_and_minits = $user_overtime_hours . ':' . $user_over_mint;
                
                
                $att_calc = ($user_basic_hour*floatval($attn_rate[0]->attn_inc_rate))+(floatval('0.'.$user_Basic_mint)*(floatval($attn_rate[0]->attn_inc_rate)));
                
                
                
                //regular pay 8 hours per day
                $hoursREGPAY = $user_basic_hour * $hourly_rate;
                $basicmin = $hourly_rate * floatval('0.'.$user_Basic_mint);
                $total_basic_pay_rate = $hoursREGPAY + $basicmin;
                //total overtime pay

                $Overtimepay = $user_overtime_hours * $ORP;
                $overtimeminutespay = $ORP * floatval('0.'.$user_over_mint);
                $total_basic_pay = $Overtimepay + $overtimeminutespay;
                
                $Nis = $atten_get->nis;

                $rate = $hourly_rate + $ORP;


                $sum = round($total_basic_pay_rate, 2) + round($total_basic_pay, 2);

                $department_get = Department::where('id', $dep_id)->select('department')->first();
                $department_name = $department_get->department;
                $deduction = Deduction::get()->toarray();
                
                //nis
                $nis_value_percentage = $deduction[0]['nis_fix_value'];
                $nis_limit_value = $deduction[0]['nis'];
                //nht
                $nht_value_percentage = $deduction[1]['nis_fix_value'];
                //edtax
                $edtax_value_percentage = $deduction[2]['nis_fix_value'];
                
                //heart
                $heart_value_percentage = $deduction[5]['nis_fix_value'];

                $bonus_name = '';
                // dd($user_id);
                $bonusPay = Bonuse::select('bonus','bonus_name')->where('start_date', $start_date)->where('end_date', $end_date)->where('user_id', $user_id)->get()->toArray();
                $bonus = 0;
                if ($bonusPay == null) {
                        $bonus = 0;
                } else {
                        foreach($bonusPay as $b_data)
                        {
                            $bonus = $bonus+floatval($b_data['bonus']);
                            $temp = $b_data['bonus_name'];
                            $bonus_name = $bonus_name.','.$b_data['bonus_name'];
                        }
                }
                $bonus_name = Str::replaceFirst(",","",$bonus_name);
                $sum = floatval($sum) + floatval($bonus) + floatval($att_calc)+floatval($holiday_total)+floatval($sick_pay)+floatval($vacation_pay)+floatval($maternityPay);
                $Nis = ($sum / 100) * floatval($nis_value_percentage);
                if ($Nis > $nis_limit_value) {
                        $Nis = floatval($nis_limit_value);
                }
                $Nht = round((round($sum, 2) / 100) * floatval($nht_value_percentage), 2);
                $EdTax = round(((round($sum, 2) - $Nis) / 100) * floatval($edtax_value_percentage), 2);
                

                $heart = round((round($sum, 2) / 100) * floatval($heart_value_percentage), 2);
                $tot_sal = 0;
                $tot_nis = 0;
                $income = 0;
                $inc_tax = 0;
                $acc = '';
                $acc_flag = false;
                $acc_val = 0;
                

                if(intval($cycle)==14)
                {
                    $acc = Accumulate::where('start_date',$start_date)->where('end_date',$end_date)->get('accumalative_payrol_value')->toArray();
                    if(count($acc)==0)
                    {
                        return response()->json(
                        [
                            'status'=>2
                        ]
                        );
                    }
                    $acc_flag = false;
                }
                else {
                    $acc = DB::table('accumulates_monthly')->where('start_date',$start_date)->where('end_date',$end_date)->get('accumulate_value')->toArray();
                    $acc_flag = true;
                }
                if(!$acc_flag)
                {
                    if(count($acc)==0)
                    {
                        $acc_val = 0;
                    }
                    else 
                    {
                        $acc_val = floatval($acc[0]['accumalative_payrol_value']);
                    }
                }
                else {
                    if(count($acc)==0)
                    {
                        $acc_val = 0;
                        return response()->json(
                        [
                            'status'=>3
                        ]
                        );
                    }
                    else {
                        $acc_val = intval($acc[0]->accumulate_value);
                    }
                }
                $process_count = Proceed::where("user_id", $user_id)->count();
                
                
                $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(income) as income_tax'))->where("user_id", $user_id)->where('start_date','<=',$start_date)->where('end_date','<=',$end_date)->get()->toarray();
                
                $check_status = Proceed::where("user_id", $user_id)->where('start_date', $start_date)
                        ->where('end_date', $end_date)->count();
                $incomeTaxThreshold = Deduction::select('nis_fix_value')->where('name', 'income tax')->first();
                $incomeTaxPercentage = intval($incomeTaxThreshold->nis_fix_value);
                
                
                
                if ($process_count == 0) {
                        $tot_sal = $sum;
                        $tot_nis = floatval($Nis);
                        
                } else {
                        $tot_sal = $process[0]['total_pay'] + $sum;
                        $tot_nis = $process[0]['nis_total'] + $Nis;
                }
                if ($tot_sal > $acc_val) {
                        $income = floatval(($tot_sal - $tot_nis - $acc_val));
                        $inc_tax = floatval(($income / 100) * $incomeTaxPercentage);
                        if($inc_tax < 0)
                        {
                            $inc_tax = 0;
                        }
                        $inc_tax = $inc_tax - $process[0]['income_tax'];
                        
                } else {
                        $inc_tax = 0;
                        if($process_count>0)
                        {
                            $inc_tax = $inc_tax - $process[0]['income_tax'];
                        }
                }
                $taxStatus = User::select('statutory_deductions')->where('id', $user_id)->first();
                if (strcmp($taxStatus->statutory_deductions, "not applicable") == 0) {
                        $inc_tax = 0;
                        $Nis = 0;
                        $Nht = 0;
                        $EdTax = 0;
                }
                
                $one_time_deduction = DB::table('one_time_deduction')->where('employee',$user_id)->where('salary_base','0')->where('start_period',$start_date)->where('status','0')->where('cycle',$cycle)->get()->toArray();
                $one_time_name = '';
                $one_time_id = '';
                $one_time_value = 0;
                
                foreach($one_time_deduction as $onetime)
                {
                    if(count($one_time_deduction)>1)
                    {
                    $one_time_name = $one_time_name.','.$onetime->deduction_name;
                    $one_time_id = $one_time_id.','.$onetime->id;
                    $one_time_value = floatval($one_time_value)+floatval($onetime->amount);
                    }
                    else {
                         $one_time_name = $onetime->deduction_name;
                         $one_time_id = $onetime.id;
                        $one_time_value = floatval($onetime->amount);
                    }
                }
                
                $continuous_name = '';
                $continuous_id = '';
                $continuous_value = 0.0;
                $continuous_deduction = DB::table('continuous_deduction')->where('user_id',$user_id)->where('next_period',$start_date)->where('action','0')->where('cycle',$cycle)->get()->toarray();
                foreach($continuous_deduction as $continuous)
                {
                    if(count($continuous_deduction)>1)
                    {
                    $continuous_name = $continuous_name.','.$continuous->deduction_name;
                    $continuous_id = $continuous_id.','.$continuous->id;
                    $continuous_value = floatval($continuous_value)+floatval($continuous->amount);
                    }
                    else {
                         $continuous_name = $continuous->deduction_name;
                         $continuous_id = $continuous->id;
                        $continuous_value = floatval($continuous->amount);
                    }
                }
                
                
                
                $loan_amount = 0;
                $ins_val = 0;
                $loan_flag = false;
                $pause = -1;
                $periodic_name = '';
                $periodic_id = '';
                $periodic_value = 0.0;
                $laon_data = DB::table('loan')->where('user_id',$user_id)->where('cycle',$cycle)->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)->where('remaning_period','>','0')->where('salary_base','0')->where('status','0')->where('stop','0')->get()->toArray();
                $status = count($laon_data);
                
                foreach($laon_data as $laon)
                {
                    if(count($laon_data)>1)
                    {
                    $periodic_name = $periodic_name.','.$laon->deduction_name;
                    $periodic_id = $periodic_id.','.$laon->id;
                    $periodic_value = floatval($periodic_value)+floatval($laon->amount);
                    }
                    else {
                         $periodic_name = $laon->deduction_name;
                         $periodic_id = $laon->id;
                        $periodic_value = floatval($laon->amount);
                    }
                }
                
                
                return response()->json([
                        'id_user' => $request->user_id, 'department' => $department_name, 'regular_hours' => $regular_hours, 'first_name' => $first_name, 'total_over_time_pay' => round($total_basic_pay, 2),
                        'total_work_hours_and_minits' => $total_work_hours_and_minits,
                        'total_basic_pay' => round($total_basic_pay_rate, 2), 'sum' => round($sum , 2), 'total_work_Basic_hours_and_minits' => $total_work_Basic_hours_and_minits,
                        'over_time_rate' => $ORP, 'hourly_rate' => $hourly_rate, 'nis' => round($Nis, 2), 'total_work_Over_hours_and_minits' => $total_work_Over_hours_and_minits,
                        'trn' => $trn, 'bonusPay' => $bonus, 'user_incometax' => round($inc_tax, 2), 'rate' => $rate, 'nis_value_percentage' => $nis_value_percentage, 'nis_limit_value', $nis_limit_value, 'nht_value_percentage' => $nht_value_percentage, 'edtax_value_percentage' => $edtax_value_percentage,
                        'nht_val' => $Nht, 'edTax_val' => $EdTax, 'atten_inc' => $att_calc, 'atten_rate' => floatval($attn_rate[0]->attn_inc_rate), 'basic_hour' => $user_basic_hour, 'basic_min' => $user_Basic_mint, 'cycle' => $cycle,'h_t_h' => $hours,'h_t_m'=>floatval($min),'h_t'=>round($holiday_total,2),'sick_pay'=> $sick_pay,'sick_hour'=> $sick_hour,'vacation_pay' => $vacation_pay, 'vacation_hour' => $vacation_hour,
                        'm_hour'=> $maternityHour, 'm_min' => floatval($maternityMin),
                        'm_pay' => $maternityPay,'bonus_name' => $bonus_name,'one_time_name'=>$one_time_name,'one_time_value'=>$one_time_value,'continuous_name'=>$continuous_name,'continuous_value'=>$continuous_value,'periodic_name'=>$periodic_name,
                        'periodic_value'=>$periodic_value,'heart'=>$heart,'one_time_id'=>$one_time_id,'continuous_id'=>$continuous_id,'periodic_id'=>$periodic_id
                ]);
        }
       
        public function Addbonus(Request $request)
        {
            $bonus_dep = '';
            $bonus_user = '';
            $s_date = '';
            $e_date = '';
            $temp_inp = "0";
                $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus', 'full_access' => '1'])->count();
                if ($permision == 0) {
                        return redirect()->back()->with('error', 'This Feature is restricted For You !');
                } else {
                    
                    if($request->start_date && $request->end_date && $request->dep && $request->user) 
                    {
                        $s_date = $request->start_date;
                        $e_date = $request->end_date;
                        $bonus_dep = $request->dep;
                        $bonus_user = $request->user;
                        $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus'])->first()->toarray();
                        
                        $period_from = $request->start_date;
                        $period_to = $request->end_date;
                        $users = User::where('department', $request->dep)->where('id',$request->user)->where('user_role','!=','admin')->get();
        
                        return view('Admin.bonusadd', get_defined_vars());
                    }
                    else if($request->start_date && $request->end_date && $request->dep)
                    {
                        $s_date = $request->start_date;
                        $e_date = $request->end_date;
                        $bonus_dep = $request->dep;
                        $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus'])->first()->toarray();
                        
                        $period_from = $request->start_date;
                        $period_to = $request->end_date;
                        // dd($period_from,$period_to,$request->dep);
                        $users = User::where('department', $request->dep)->where('user_role','!=','admin')->get();
                        return view('Admin.bonusadd', get_defined_vars());
                    }
                    else if($request->start_date && $request->end_date)
                    {
                        $s_date = $request->start_date;
                        $e_date = $request->end_date;
                        $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'bonus'])->first()->toarray();
                        
                        $period_from = $request->start_date;
                        $period_to = $request->end_date;
                        // dd($period_from,$period_to,$request->dep);
                        $users = User::where('user_role','!=','admin')->get();
                        return view('Admin.bonusadd', get_defined_vars());
                    }
                    else {
                        $period_from = $request->start_date;
                        $period_to = $request->end_date;
                        $users = [];
                        return view('Admin.bonusadd', get_defined_vars());
                    }
                }
        }

        public function storeBonus(Request $request)
        {
                $user_dep = DB::table('users')->select('department')->where('id',$request->user_id)->get()->toArray();
                $bonusCount = Bonuse::where('start_date', $request->period_from)->where('end_date', $request->period_to)->where('dep',$user_dep[0]->department)->where('bonus_name',$request->bonus_name)->count();
                for ($i = 0; $i < count($request->user_id); $i++) {
                                $bonus_start = $request->period_from[$i];
                                $bonus_end = $request->period_to[$i];



                                Bonuse::create([
                                        'user_id'  => $request->user_id[$i],
                                        'name'     => $request->first_name[$i] . ' ' . $request->last_name[$i],
                                        'bonus_name' => $request->bonus_name[$i],'dep' => $user_dep[0]->department,
                                        'sttsus' => 0,
                                        'start_date' => $request->period_from[$i],
                                        'end_date' => $request->period_to[$i],
                                        'bonus' => $request->bonus[$i],
                                ]);
                        }
                        return redirect()->back()->with('message', 'Your Bonus Add Successfully!');

        }
        public function payrol_proceed_all(Request $request)
        {
            $cycle = $request->cycle;
            $department = $request->department;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $atten_data = DB::table('users')
            ->where('users.department',$department)
            ->join('attendences','users.id','=','attendences.user_id')
            ->select('users.first_name','users.last_name','users.department','users.hourly_rate','users.ot_rate','users.attn_inc_rate','attendences.user_id',DB::raw("SUM(total_hours) as t_h"))
            ->where('attendences.date','>=',$start_date)
            ->where('attendences.date','<=',$end_date)
            ->groupBy('attendences.user_id','users.first_name','users.last_name','users.hourly_rate','users.ot_rate','users.attn_inc_rate','users.department')->get();
            foreach($atten_data as $attn)
            {
                $total_hour = round(($attn->t_h/3600),2);
                $basic_hour = '';
                $atten_hour = '';
                $sick_hour = '';
                $vacation_hour = '';
                $maternity_hour = '';
                $holiday_hour = '';
                $overtime_hour = '';
                $bonus_name = '';
                $laon_data = '';
                $continuous_deduction = '';
                $continuous_name = '';
                $continuous_id = '';
                $continuous_value = 0.0;
                $loan_amount = 0;
                $ins_val = 0;
                $loan_flag = false;
                $pause = -1;
                $periodic_name = '';
                $periodic_id = '';
                $periodic_value = 0.0;
                $one_time_name = '';
                $one_time_id = '';
                $one_time_value = 0;
                $one_time_deduction = '';

                $total_basic_pay = 0.0;
                $total_atten_pay = 0.0;
                $sick_pay = 0.0;
                $vacation_pay = 0.0;
                $maternity_pay = 0.0;
                $holiday_pay = 0.0;
                $total_overtime_pay = 0.0;
                $bonus_pay = 0.0;
                $nis = 0.0;
                $nht = 0.0;
                $edtax = 0.0;
                $income_tax = 0.0;
                $bonus = 0.0;
                $total_pay = 0.0;
                $net_pay = 0.0;
                $total_deduction = 0.0;

                if($total_hour>80)
                {
                    $basic_hour = 80.0;
                    $total_basic_pay = $basic_hour*$attn->hourly_rate;
                    $overtime_hour = round(($total_hour - 80),2);
                    $overtime_pay = $overtime_hour*$attn->ot_rate;
                    $total_atten_pay = round($basic_hour,2)*$attn->attn_inc_rate;
                    $atten_hour = $basic_hour;        
                    
                }
                else {
                    $total_basic_pay = round($total_hour,2)*$attn->hourly_rate;
                    $basic_hour = $total_hour;
                    $overtime_hour = "0:0";
                    $overtime_pay = 0;
                    $total_atten_pay = round($total_hour,2)*$attn->attn_inc_rate;
                    $atten_hour = $total_hour;
                    
                }  
                $sick_data = SickLeave::where('user_id',$attn->user_id)->where('leave_date','>=',$start_date)->where('leave_date','<=',$end_date)->where('status','1')->count();   
                if($sick_data > 0)
                {
                    $s_h = (8*$sick_data);
                    $basic_hour = floatval($basic_hour) - $s_h;
                    $sick_pay = $s_h*$attn->hourly_rate;
                    $sick_hour = $s_h.'.0';
                }
                $vacation_data = VacationLeave::where('user_id',$attn->user_id)->where('leave_date','>=',$start_date)->where('leave_date','<=',$end_date)->where('status','1')->count();
                if($vacation_data>0)
                {
                    $v_h = (8*$vacation_data);
                    $basic_hour = floatval($basic_hour) - $v_h; 
                    $vacation_pay = $v_h*$attn->hourly_rate;
                    $vacation_hour = $v_h.'.0';
                }
                $maternity_date = Maternity::where('user_id',$attn->user_id)->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)->get()->toArray();
                if(count($maternity_date)>0)
                {
                    if($cycle==14)
                    {
                        $m_h = (28800*($cycle-4))/3600;
                        $maternity_pay = $m_h*$attn->hourly_rate;
                        $maternity_hour = $m_h.'.0';
                    }
                    else {
                        $m_h = (28800*($cycle-8))/3600;
                        $maternity_pay = $m_h*$attn->hourly_rate;
                        if($m_h<10)
                        {
                            $maternity_hour = '0'.$m_h.'.0';
                        }
                        else
                        {
                            $maternity_hour = $m_h.'.0';
                        }
                    }
                }

                $holiday_data = DB::table('holidays')->where('holidays.holiday_date','>=',$start_date)
                ->where('holidays.holiday_date','<=',$end_date)
                ->join('holiday_pays','holidays.holiday_date', '=', 'holiday_pays.date')
                ->select('holiday_pays.user_id',DB::raw("SUM(total_time) as t_t"))
                ->where('holiday_pays.user_id',$attn->user_id)
                ->where('holiday_pays.date','>=',$start_date)
                ->where('holiday_pays.date','<=',$end_date)
                ->groupBy('holiday_pays.user_id')
                ->get();

                if(count($holiday_data)>0)
                {
                    $holiday_hour = round(($holiday_data[0]->t_t/3600),2);
                    $holiday_pay = $holiday_hour*$attn->hourly_rate;
                }
                
                    
                $bonusPay = Bonuse::select('bonus','bonus_name')->where('start_date', $start_date)->where('end_date', $end_date)->where('user_id', $attn->user_id)->get()->toArray();
                $bonus = 0;
                if ($bonusPay == null) {
                    $bonus = 0;
                }
                else {
                        foreach($bonusPay as $b_data)
                        {
                            $bonus = $bonus+floatval($b_data['bonus']);
                            $temp = $b_data['bonus_name'];
                            $bonus_name = $bonus_name.','.$b_data['bonus_name'];
                        }
                }
                $bonus_name = Str::replaceFirst(",","",$bonus_name);


                $total_pay = floatval($total_basic_pay)+floatval($total_atten_pay)+floatval($sick_pay)+floatval($vacation_pay)
                    +floatval($maternity_pay)+floatval($holiday_pay)+floatval($overtime_pay)+floatval($bonus);

                
               
                $sum = $total_pay;
                $deduction = Deduction::get()->toarray();
                
                //nis
                $nis_value_percentage = $deduction[0]['nis_fix_value'];
                $nis_limit_value = $deduction[0]['nis'];
                //nht
                $nht_value_percentage = $deduction[1]['nis_fix_value'];
                //edtax
                $edtax_value_percentage = $deduction[2]['nis_fix_value'];
                
                //heart
                $heart_value_percentage = $deduction[5]['nis_fix_value'];



                $Nis = ($sum / 100) * floatval($nis_value_percentage);
                if ($Nis > $nis_limit_value) {
                        $Nis = floatval($nis_limit_value);
                }
                $Nht = round((round($sum, 2) / 100) * floatval($nht_value_percentage), 2);
                $EdTax = round(((round($sum, 2) - $Nis) / 100) * floatval($edtax_value_percentage), 2);
                $heart = round((round($sum, 2) / 100) * floatval($heart_value_percentage), 2);
                $tot_sal = 0;
                $tot_nis = 0;
                $income = 0;
                $inc_tax = 0;
                $acc = '';
                $acc_flag = false;
                $acc_val = 0;
                

                if(intval($cycle)==14)
                {
                    $acc = Accumulate::where('start_date',$start_date)->where('end_date',$end_date)->get('accumalative_payrol_value')->toArray();
                    $acc_flag = false;
                }
                else {
                    $acc = DB::table('accumulates_monthly')->where('start_date',$start_date)->where('end_date',$end_date)->get('accumulate_value')->toArray();
                    $acc_flag = true;
                }
                if(!$acc_flag)
                {
                    if(count($acc)==0)
                    {
                        $acc_val = 0;
                    }
                    else 
                    {
                        $acc_val = floatval($acc[0]['accumalative_payrol_value']);
                    }
                }
                else {
                    if(count($acc)==0)
                    {
                        $acc_val = 0;
                        return response()->json(
                        [
                            'status'=>3
                        ]
                        );
                    }
                    else {
                        $acc_val = intval($acc[0]->accumulate_value);
                    }
                }
                $process_count = Proceed::where("user_id", $attn->user_id)->count();
                
                
                $process = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(nis) as nis_total,SUM(income) as income_tax'))
                ->where("user_id", $attn->user_id)
                ->where('start_date','<=',$start_date)
                ->where('end_date','<=',$end_date)->get()->toarray();
                
                $check_status = Proceed::where("user_id", $attn->user_id)->where('start_date', $start_date)
                        ->where('end_date', $end_date)->count();
                $incomeTaxThreshold = Deduction::select('nis_fix_value')->where('name', 'income tax')->first();
                $incomeTaxPercentage = intval($incomeTaxThreshold->nis_fix_value);
                
                
                
                if ($process_count == 0) {
                        $tot_sal = $sum;
                        $tot_nis = floatval($Nis);
                        
                } else {
                        $tot_sal = $process[0]['total_pay'] + $sum;
                        $tot_nis = $process[0]['nis_total'] + $Nis;
                }
                if ($tot_sal > $acc_val) {
                        $income = floatval(($tot_sal - $tot_nis - $acc_val));
                        $inc_tax = floatval(($income / 100) * $incomeTaxPercentage);
                        if($inc_tax < 0)
                        {
                            $inc_tax = 0;
                        }
                        $inc_tax = $inc_tax - $process[0]['income_tax'];
                        
                } else {
                        $inc_tax = 0;
                        if($process_count>0)
                        {
                            $inc_tax = $inc_tax - $process[0]['income_tax'];
                        }
                }
                $taxStatus = User::select('statutory_deductions')->where('id', $attn->user_id)->first();
                if (strcmp($taxStatus->statutory_deductions, "not applicable") == 0) {
                        $inc_tax = 0;
                        $Nis = 0;
                        $Nht = 0;
                        $EdTax = 0;
                }

                $one_time_deduction = DB::table('one_time_deduction')
                ->where('employee',$attn->user_id)->where('salary_base','0')
                ->where('start_period',$start_date)->where('status','0')
                ->where('cycle',$cycle)->get()->toArray();
                
                
                foreach($one_time_deduction as $onetime)
                {
                    if(count($one_time_deduction)>1)
                    {
                    $one_time_name = $one_time_name.','.$onetime->deduction_name;
                    $one_time_id = $one_time_id.','.$onetime->id;
                    $one_time_value = floatval($one_time_value)+floatval($onetime->amount);
                    }
                    else {
                         $one_time_name = $onetime->deduction_name;
                         $one_time_id = $onetime->id;
                        $one_time_value = floatval($onetime->amount);
                    }
                }
                
                
                $continuous_deduction = DB::table('continuous_deduction')
                ->where('user_id',$attn->user_id)->where('next_period',$start_date)
                ->where('action','0')->where('cycle',$cycle)->get()->toarray();
                foreach($continuous_deduction as $continuous)
                {
                    if(count($continuous_deduction)>1)
                    {
                    $continuous_name = $continuous_name.','.$continuous->deduction_name;
                    $continuous_id = $continuous_id.','.$continuous->id;
                    $continuous_value = floatval($continuous_value)+floatval($continuous->amount);
                    }
                    else {
                         $continuous_name = $continuous->deduction_name;
                         $continuous_id = $continuous->id;
                        $continuous_value = floatval($continuous->amount);
                    }
                }
                $laon_data = DB::table('loan')->where('user_id',$attn->user_id)
                ->where('cycle',$cycle)->where('start_date','<=',$start_date)
                ->where('end_date','>=',$end_date)->where('remaning_period','>','0')
                ->where('salary_base','0')->where('status','0')
                ->where('stop','0')->get()->toArray();
                $status = count($laon_data);
                
                foreach($laon_data as $laon)
                {
                    if(count($laon_data)>1)
                    {
                    $periodic_name = $periodic_name.','.$laon->deduction_name;
                    $periodic_id = $periodic_id.','.$laon->id;
                    $periodic_value = floatval($periodic_value)+floatval($laon->amount);
                    }
                    else {
                         $periodic_name = $laon->deduction_name;
                         $periodic_id = $laon->id;
                        $periodic_value = floatval($laon->amount);
                    }
                }
                $total_deduction = (round($Nis,2)+round($Nht,2)+round($EdTax,2)+round($inc_tax,2)+$one_time_value+$continuous_value+$periodic_value);
                $net_pay = $sum - $total_deduction;

                
                if(count($laon_data)>0)
                {
                    $temp_rem = intval($laon_data[0]->remaning_period);
                    $temp_rem = $temp_rem-1;
                    DB::table('loan')->where('user_id',$attn->user_id)->where('start_date','<=',$start_date)
                    ->where('end_date','>=',$end_date)->where('salary_base','0')
                    ->where('status','0')->where('stop','0')
                    ->update([
                        'remaning_period'=>$temp_rem
                        ]);
                }
                
                
                if(count($one_time_deduction)>0)
                {
                    DB::table('one_time_deduction')->where('status','0')
                    ->where('start_period',$start_date)->where('employee',$attn->user_id)
                    ->update(['status'=>'1']);
                }
                
                
                if(count($continuous_deduction)>0)
                {
                    $temp_d = $start_date;
                    $t_d = strtotime($temp_d);
                    $temp_d = strtotime("+14 day",$t_d);
                    
                    $temp1 = strtotime($start_date);
                    $date = strtotime("+14 day", $temp1);
                    $temp_d = date('Y-m-d',$date);
                    DB::table('continuous_deduction')->where('user_id',$attn->user_id)
                    ->where('next_period',$start_date)->where('cycle',$cycle)
                    ->where('action','0')->update(['next_period'=>$temp_d,'temp_start'=>$start_date]);
                }
                
                $count = Proceed::where('start_date', $start_date)->where('end_date', $end_date)
                ->where('user_id', $attn->user_id)->count();
                $dep_name = DB::table('departments')->select('department')->where('id',$attn->department)->get();
                if ($count== 0) {
                        $success = 'Successfully  payroll ADD';

                        $c_year = Carbon::now()->year;
                        $proceed = new Proceed();
                        $proceed->user_id = $attn->user_id;
                        $proceed->start_date = $start_date;
                        $proceed->end_date = $end_date;
                        $proceed->nis = round($Nis,2);
                        $proceed->nht = round($Nht,2);
                        $proceed->edtax = round($EdTax,2);
                        $proceed->total_pay = $sum;
                        $proceed->net_pay = $net_pay;
                        $proceed->total_deduction = $total_deduction;
                        $proceed->status = 1;
                        $proceed->income = round($inc_tax,2);
                        $proceed->year = $c_year;
                        $proceed->bonus = $bonus;
                        $proceed->dept = $dep_name[0]->department;
                        $proceed->emp_name = $attn->first_name." ".$attn->last_name;
                        $proceed->type = "Hourly";
                        $proceed->one_time = $one_time_value;
                        $proceed->continuous = $continuous_value;
                        $proceed->periodic = $periodic_value;
                        $proceed->work_hours = round($total_hour,2);
                        $proceed->basic_hour = round($basic_hour,2);
                        $proceed->basic_pay = $total_basic_pay;
                        $proceed->atten_hour = round($atten_hour,2);
                        $proceed->atten_pay = $total_atten_pay;
                        $proceed->sick_hour = $sick_hour;
                        $proceed->sick_pay = $sick_pay;
                        $proceed->vacation_hour = $vacation_hour;
                        $proceed->vacation_pay = $vacation_pay;
                        $proceed->maternity_hour = $maternity_hour;
                        $proceed->maternity_pay = $maternity_pay;
                        $proceed->holiday_hour = $holiday_hour;
                        $proceed->holiday_pay = $holiday_pay;
                        $proceed->overtime_hour = $overtime_hour;
                        $proceed->overtime_pay = $overtime_pay;
                        $proceed->bonus_name = $bonus_name;
                        $proceed->onetime_name = $bonus_name;
                        $proceed->continuous_name = $continuous_name;
                        $proceed->onetime_id = $one_time_id;
                        $proceed->continuous_id = $continuous_id;
                        $proceed->periodic_id = $periodic_id;
                        $proceed->cont_nis = round($Nis,2);
                        $proceed->cont_nht = round($Nht,2);
                        $proceed->cont_edtax = round($EdTax,2);
                        $proceed->cont_heart = round($heart,2);
                        $proceed->periodic_name = $periodic_name;
                        if(intval($cycle)==14)
                        {
                            $proceed->cycle = "Fortnightly";
                        }
                        else {
                            $proceed->cycle = "Monthly";
                        }
                        
                        $proceed->save();
                }
                
            }
            return response()->json(
                [
                    'response' => 1
                ]
                );
        }
        public function payrol_proceed(Request $request)
        {
                
                $user_id = $request->user_id;
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $nis = $request->nis;
                $nht = $request->nht;
                $edtax = $request->edtax;
                $netpay = $request->netpay;
                $uBonus = $request->bonus;
                $gross_pay = $request->gross__pay;
                $tot_deduc = $request->tot_deduc;
                $userBonus = Str::remove('$', $uBonus);
                $incometax = $request->income_save;
                $deptartment_ = $request->dept;
                $emp_name = $request->emp_name;
                $one_time = $request->one_time_pass;
                $continuous = $request->continuous;
                $periodic = $request->periodic;
                $cycle = $request->cycle;
                
                $work_hours = $request->work_hours;
                $basic_hours = $request->basic_hours;
                $basic_pay = $request->basic_pay;
                $atten_hour = $request->atten_hour;
                $atten_pay = $request->atten_pay;
                $sick_hour = $request->sick_hour;
                $sick_pay = $request->sick_pay;
                $vacation_hour = $request->vacation_hour;
                $vacation_pay = $request->vacation_pay;
                $maternity_hour = $request->maternity_hour;
                $maternity_pay = $request->maternity_pay;
                $holiday_hour = $request->holiday_hour;
                $holiday_pay = $request->holiday_pay;
                $overtime_hour = $request->overtime_hour;
                $overtime_pay = $request->overtime_pay;
                $bonus_name = $request->bonus_name;
                $cont_nis = $request->cont_nis;
                $cont_nht = $request->cont_nht;
                $cont_edtax = $request->cont_edtax;
                $cont_heart = $request->cont_heart;
                
                
                $p_d_c = DB::table('loan')->where('user_id',$user_id)
                ->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)
                ->where('remaning_period','>','0')->where('status','0')->where('stop','0')
                ->where('salary_base','0')->where('cycle',$cycle)->get()->toArray();
                if(count($p_d_c)>0)
                {
                    $temp_rem = intval($p_d_c[0]->remaning_period);
                    $temp_rem = $temp_rem-1;
                    DB::table('loan')->where('user_id',$user_id)->where('start_date','<=',$start_date)->where('end_date','>=',$end_date)->where('salary_base','0')->where('status','0')->where('stop','0')->update([
                        'remaning_period'=>$temp_rem
                        ]);
                }
                
                $o_t_c = DB::table('one_time_deduction')->where('status','0')
                ->where('start_period',$start_date)->where('salary_base','0')
                ->where('employee',$user_id)->where('cycle',$cycle)->count();
                if($o_t_c>0)
                {
                    DB::table('one_time_deduction')->where('status','0')->where('start_period',$start_date)->where('employee',$user_id)->update(['status'=>'1']);
                }
                
                $c_d_c = DB::table('continuous_deduction')->where('user_id',$user_id)
                ->where('next_period',$start_date)->where('cycle',$cycle)
                ->where('action','0')->count();
                if($c_d_c>0)
                {
                    $temp_d = $start_date;
                    $t_d = strtotime($temp_d);
                    $temp_d = strtotime("+14 day",$t_d);
                    
                    $temp1 = strtotime($start_date);
                    $date = strtotime("+14 day", $temp1);
                    $temp_d = date('Y-m-d',$date);
                    DB::table('continuous_deduction')->where('user_id',$user_id)->where('next_period',$start_date)->where('cycle',$cycle)->where('action','0')->update(['next_period'=>$temp_d,'temp_start'=>$start_date]);
                }
                
                $count = Proceed::where('start_date', $start_date)->where('end_date', $end_date)->where('user_id', $user_id)->count();
                if ($count > 0) {
                        return 1;
                } else {
                        $success = 'Successfully  payroll ADD';

                        $c_year = Carbon::now()->year;
                        $proceed = new Proceed();
                        $proceed->user_id = $user_id;
                        $proceed->start_date = $start_date;
                        $proceed->end_date = $end_date;
                        $proceed->user_id = $user_id;
                        $proceed->nis = $nis;
                        $proceed->nht = $nht;
                        $proceed->edtax = $edtax;
                        $proceed->total_pay = $gross_pay;
                        $proceed->net_pay = $netpay;
                        $proceed->total_deduction = $tot_deduc;
                        $proceed->status = 1;
                        $proceed->income = $incometax;
                        $proceed->year = $c_year;
                        $proceed->bonus = $userBonus;
                        $proceed->dept = $deptartment_;
                        $proceed->emp_name = $emp_name;
                        $proceed->type = "Hourly";
                        $proceed->one_time = $one_time;
                        $proceed->continuous = $continuous;
                        $proceed->periodic = $periodic;
                        $proceed->work_hours = $work_hours;
                        $proceed->basic_hour = $basic_hours;
                        $basic_pay = Str::replace("$","",$basic_pay);
                        $proceed->basic_pay = $basic_pay;
                        $proceed->atten_hour = $atten_hour;
                        $atten_pay = Str::replace("$","",$atten_pay);
                        $proceed->atten_pay = $atten_pay;
                        $proceed->sick_hour = $sick_hour;
                        $sick_pay = Str::replace('$','',$sick_pay);
                        $proceed->sick_pay = $sick_pay;
                        $proceed->vacation_hour = $vacation_hour;
                        $vacation_pay = Str::replace("$","",$vacation_pay);
                        $proceed->vacation_pay = $vacation_pay;
                        $proceed->maternity_hour = $maternity_hour;
                        $maternity_pay = Str::replace("$","",$maternity_pay);
                        $proceed->maternity_pay = $maternity_pay;
                        $proceed->holiday_hour = $holiday_hour;
                        $holiday_pay = Str::replace("$","",$holiday_pay);
                        $proceed->holiday_pay = $holiday_pay;
                        $proceed->overtime_hour = $overtime_hour;
                        $overtime_pay = Str::replace("$","",$overtime_pay);
                        $proceed->overtime_pay = $overtime_pay;
                        $proceed->bonus_name = $bonus_name;
                        $proceed->onetime_name = $request->onetime_name;
                        $proceed->continuous_name = $request->continuous_name;
                        $proceed->onetime_id = $request->one_time_id;
                        $proceed->continuous_id = $request->continuous_id;
                        $proceed->periodic_id = $request->loan_id;
                        $proceed->cont_nis = $cont_nis;
                        $proceed->cont_nht = $cont_nht;
                        $proceed->cont_edtax = $cont_edtax;
                        $proceed->cont_heart = $cont_heart;
                        $proceed->periodic_name = $request->periodic_name;
                        if(intval($request->payroll_cycle)==14)
                        {
                            $proceed->cycle = "Fortnightly";
                        }
                        else {
                            $proceed->cycle = "Monthly";
                        }
                        
                        $proceed->save();
                        return 0;
                }
        }
        public function GeneratePDF(Request $request)
        {
            $start_date = $request->print_s_d;
            $end_date = $request->print_e_d;
            $user_id = $request->print_u_id;
            $cycle = $request->print_cycle;

            $data = [
                'title' => 'Welcome to ItSolutionStuff.com',
                'date' => date('m/d/Y')
            ];
              
            $pdf = PDF::loadView('myPDF', $data);
        
            return $pdf->download('itsolutionstuff.pdf');
            

            dd($email,$start_date,$end_date,$user_id,$cycle);
        }
        public function processed_atten_get(Request $request)
        {
            $cycle = '';
            if(intval($request->cycle)==14)
            {
                $cycle = 'Fortnightly';
            }
            else {
                $cycle = 'Monthly';
            }
            $process = Proceed::where('user_id',$request->user_id)->where('start_date',$request->start_date)->where('end_date',$request->end_date)->where('cycle',$cycle)->get()->toArray();
            $user = DB::table('users')->where('id',$request->user_id)->get();
            return response()->json([
                'dep_name'=>$process[0]["dept"],
                'overtime_rate'=>$user[0]->ot_rate,'hourly_rate'=>$user[0]->hourly_rate,'atten_rate'=>$user[0]->attn_inc_rate,'emp_name'=>$user[0]->first_name.' '.$user[0]->last_name,'trn'=>$user[0]->trn,'nis'=>$user[0]->nis,'work_hours'=>$process[0]["work_hours"],'reg_pay'=>$process[0]["basic_pay"],'overtime_pay'=>$process[0]['overtime_pay'],'bonus'=>$process[0]["bonus"],
                'basic_hour'=>$process[0]["basic_hour"],'atten_pay'=>$process[0]["atten_pay"],'sick_hour'=>$process[0]["sick_hour"],'sick_pay'=>$process[0]["sick_pay"],'vacation_hour'=>$process[0]["vacation_hour"],'vacation_pay'=>$process[0]["vacation_pay"],'maternity_hour'=>$process[0]["maternity_hour"],'maternity_pay'=>$process[0]["maternity_pay"],'holiday_hour'=>$process[0]["holiday_hour"],'holiday_pay'=>$process[0]["holiday_pay"],'overtime_hour'=>$process[0]["overtime_hour"],'bonus_name'=>$process[0]["bonus_name"],'total_pay'=>$process[0]["total_pay"],'ded_nis'=>$process[0]["nis"],'ded_nht'=>$process[0]["nht"],'ded_edtax'=>$process[0]["edtax"],'income_tax'=>$process[0]["income"],'one_time'=>$process[0]["one_time"],'continuous'=>$process[0]["continuous"],'periodic'=>$process[0]["periodic"],'onetime_name'=>$process[0]["onetime_name"],'continuous_name'=>$process[0]["continuous_name"],'periodic_name'=>$process[0]["periodic_name"],'total_deduction'=>$process[0]["total_deduction"],'netpay'=>$process[0]["net_pay"],'cont_nis'=>$process[0]["cont_nis"],'cont_nht'=>$process[0]["cont_nht"],'cont_edtax'=>$process[0]["cont_edtax"],'cont_heart'=>$process[0]["cont_heart"]
                ]);
            
        }
}