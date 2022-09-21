<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Mail\TestMail;
use App\Models\Holiday;
use App\Models\Proceed;
use App\Models\Deduction;
use App\Models\HolidayPay;
use App\Models\Threshold;
use App\Models\Accumulate;
use App\Models\Attendence;
use App\Models\Department;
use App\Models\Permission;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Deppermissinons;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;




class AdminController extends Controller
{
    public function MacAddressManagment()
    {
        $data = DB::table('manage_macs')
            ->join('users', 'users.id', '=', 'manage_macs.user_id')
            ->select('manage_macs.*', 'users.first_name', 'users.last_name')
            ->get();
        $users = User::select('id','first_name','last_name')->get()->toArray();
        return view('Admin.mac_manage',get_defined_vars());
    }
    public function AddMac(Request $request)
    {
        $count = DB::table('manage_macs')->where('mac',$request->mac)->count();
        if($count==0)
        {
            DB::table('manage_macs')->insert([
                'user_id' => $request->mac_user,
                'mac' => $request->mac,
                'status' => '1'
            ]);
            $data = DB::table('manage_macs')
                ->join('users', 'users.id', '=', 'manage_macs.user_id')
                ->select('manage_macs.*', 'users.first_name', 'users.last_name')
                ->get();
            $users = User::select('id','first_name','last_name')->get()->toArray();
            return redirect()->back()->with('message','Mac Address Added Successfully');
        }
        else {
            return redirect()->back()->with('message','Mac Address Already Exist');
        }

    }
    public function MacAddressEdit(Request $request)
    {
        DB::table('manage_macs')
            ->where('user_id', $request->user_id)
            ->update(['mac' => $request->mac]);
        return redirect()->back()->with('message','Mac Edit Successfully');
    }
    public function MacDelete($id)
    {
        DB::table('manage_macs')->where('user_id', $id)->delete();
        return 0;
    }
    public function IpManagment()
    {
        $data = DB::table('manage_i_p')->get()->toArray();
        return view('Admin.ip_manage',get_defined_vars());
    }
    public function AddIP(Request $request)
    {
        DB::table('manage_i_p')->insert([
            'name' => $request->network,
            'ip' => $request->ip
        ]);
        $data = DB::table('manage_i_p')->get()->toArray();
        return redirect()->back()->with('message','IP Address Added Successfully');
    }
    public function IpEdit(Request $request,$id)
    {
        DB::table('manage_i_p')
            ->where('id', $id)
            ->update(['name' => $request->network,'ip' => $request->ip]);
        $data = DB::table('manage_i_p')->get()->toArray();
        return redirect()->back()->with('message','IP Edit Successfully');
    }
    public function IpDelete($id)
    {
        DB::table('manage_i_p')->where('id', $id)->delete();
        return 0;
    }
    public function admin_multi_attendance(Request $request)
    {
        $s_date = $request->s_date;
        $e_date = $request->e_date;
        $s_time = $request->start_time;
        $e_time = $request->end_time;
        $dep = $request->department;
        $user = $request->user;
        $c_date = date('Y-m-d');

        while($s_date<=$e_date)
        {

            $temp1 = strtotime($s_date);
            $checkD = date('w',$temp1);
            if($checkD=="6")
            {
                $date = strtotime("+1 day", $temp1);
                $s_date = date('Y-m-d',$date);
            }
            $temp1 = strtotime($s_date);
            $checkD = date('w',$temp1);
            if($checkD=="0")
            {
                $date = strtotime("+1 day", $temp1);
                $s_date = date('Y-m-d',$date);
            }
            $holiday=Holiday::where('holiday_date',$s_date)->count();
            if (intval($holiday)==1) {
                $start_time = date('h:i:s A', strtotime($s_time));
                $end_time = date('h:i:s A', strtotime($e_time));
                $startTime = Carbon::parse($start_time);
                $endTime = Carbon::parse($end_time);

                $totalDuration =  $startTime->diff($endTime)->format('%h:%i:%s');
                $tempData = explode(':',$totalDuration);
                $hour = '';
                $min = '';
                $sec = '';
                if(intval($tempData[0])<=9)
                {
                    $hour = '0'.$tempData[0];
                }
                else {
                    $hour = $tempData[0];
                }
                if(intval($tempData[1])<=9)
                {
                    $min = '0'.$tempData[1];
                }
                else {
                    $min = $tempData[1];
                }
                if(intval($tempData[2])<=9)
                {
                    $sec = '0'.$tempData[2];
                }
                else {
                    $sec = $tempData[2];
                }
                $totalDuration = $hour.':'.$min.':'.$sec;
                $d = explode(':', $totalDuration);
                $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];
                $total_time_seconds = Carbon::parse($s_time)->diffInSeconds($endTime);

                $duble_salary=$total_time_seconds;

                $total_seconds = $duble_salary - 28800;
                $add_overtime_after_approve = $duble_salary - $total_seconds;
                $after = gmdate("h:i:s", $duble_salary);
                $overtime = gmdate("h:i:s", $total_seconds);
                $In_time_update = new Attendence();

                $check_atten_one_time = Attendence::where('user_id',$user)->where('date',$s_date)->first();
                if ($check_atten_one_time == null) {
                    $cnt = HolidayPay::where('date',$s_date)->where('user_id',$user)->count();
                    if($cnt==0)
                    {
                        $test_hol = new HolidayPay();
                        $test_hol->user_id = $user;
                        $test_hol->date = $s_date;
                        $test_hol->total_time = $total_time_seconds;
                        $test_hol->status = "0";
                        $test_hol->save();
                    }


                    $In_time_update->user_id = $user;
                    $In_time_update->start_time = $start_time;
                    $In_time_update->end_time = $end_time;
                    $In_time_update->date = $s_date;
                    $In_time_update->work_time = $after;
                    $In_time_update->overtime = '00:00:00';
                    $In_time_update->total_hours = $total_time_seconds;
                    $In_time_update->work_and_overtime = $add_overtime_after_approve;
                    $In_time_update->status = 1;
                    $In_time_update->save();
                }
            }
            else{

                $start_time = date('h:i:s A', strtotime($s_time));
                $end_time = date('h:i:s A', strtotime($e_time));


                $startTime = Carbon::parse($s_time);
                $endTime = Carbon::parse($e_time);
                $totalDuration =  $startTime->diff($endTime)->format('%h:%i:%s');
                $tempData = explode(':',$totalDuration);
                $hour = '';
                $min = '';
                $sec = '';
                if(intval($tempData[0])<=9)
                {
                    $hour = '0'.$tempData[0];
                }
                else {
                    $hour = $tempData[0];
                }
                if(intval($tempData[1])<=9)
                {
                    $min = '0'.$tempData[1];
                }
                else {
                    $min = $tempData[1];
                }
                if(intval($tempData[2])<=9)
                {
                    $sec = '0'.$tempData[2];
                }
                else {
                    $sec = $tempData[2];
                }
                $totalDuration = $hour.':'.$min.':'.$sec;
                $d = explode(':', $totalDuration);
                $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];
                $total_time_seconds = Carbon::parse($s_time)->diffInSeconds($endTime);

                if($total_time_seconds>28800)
                {
                    $total_seconds = $total_time_seconds - 28800;
                    $add_overtime_after_approve = $total_time_seconds - $total_seconds;
                    $after = gmdate("h:i:s", $add_overtime_after_approve);
                    $overtime = gmdate("h:i:s", $total_seconds);
                }
                else {
                    $after = gmdate("h:i:s", $total_time_seconds);
                    $overtime = "00:00:00";
                    $add_overtime_after_approve = $total_time_seconds;
                }
                $In_time_update = new Attendence();


                $check_atten_one_time = Attendence::where('user_id', $user)->where('date', $s_date)->first();
                if ($check_atten_one_time == null) {
                    if ($total_time_seconds >= 28800) {
                        $In_time_update->user_id = $user;
                        $In_time_update->start_time = $start_time;
                        $In_time_update->end_time = $end_time;
                        $In_time_update->date = $s_date;
                        $In_time_update->work_time = $after;
                        $In_time_update->overtime = $overtime;
                        $In_time_update->total_hours = $total_time_seconds;
                        $In_time_update->work_and_overtime = $add_overtime_after_approve;
                        $In_time_update->status = 0;
                        $In_time_update->save();
                    } else {
                        $In_time_update->user_id = $user;
                        $In_time_update->start_time = $start_time;
                        $In_time_update->end_time = $end_time;
                        $In_time_update->date = $s_date;
                        $In_time_update->work_time = $totalDuration;
                        $In_time_update->overtime = '00:00:00';
                        $In_time_update->total_hours = $total_time_seconds;
                        $In_time_update->work_and_overtime = $simplework;
                        $In_time_update->status = 0;
                        $In_time_update->save();
                    }
                }
            }

            $date = strtotime($s_date);
            $date = strtotime("+1 day", $date);
            $s_date = date('Y-m-d',$date);
        }

        return redirect()->back()->with('message', 'Your attendance successfully!');

    }
    public function add_multi_attendance(Request $request)
    {
        $auth_role = Auth::user()->user_role;
        $contains = Str::contains($auth_role, 'Supervisor');
        $d = "";
        if($contains)
        {
            $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
            $d = explode(',', $dep_per[0]->department_id);
        }
        return view('Admin.add_multi_attendance',get_defined_vars());
    }
    public function deleteBonus($id,$start_date,$end_date)
    {
        $res = DB::table('bonuses')->where('user_id',$id)->where('start_date',$start_date)->where('end_date',$end_date)->delete();
        if($res)
        {
            return response()->json([
                    'status'=>200
                ]
            );
        }
        else {
            return response()->json([
                    'status'=>400
                ]
            );
        }

    }
    public function dashboard()
    {

        return view('Admin.dashboard');
    }
    public function ViewLoan()
    {
        $laon = DB::table('loan')->get()->toArray();
        return view('Admin.ViewLoan',get_defined_vars());
    }
    public function AddLoanView()
    {
        return view('Admin.AddLoan');
    }
    public function AddLoanData(Request $request)
    {
        $s_date = $request->start_date;
        $e_date = $request->end_date;
        $temp = strtotime($e_date) - strtotime($s_date);
        $remaining_period = abs(round($temp / 86400));
        $remaining_period = $remaining_period+1;
        $remaining_period = $remaining_period/14;

        $total = floatval($request->total);
        $emp = $request->employee;
        $nameData = DB::table('users')->select('first_name','last_name')->where('id',$emp)->get()->toArray();
        $cycle = intval($request->cycle);
        $amount = floatval($request->total);
        if($cycle==14)
        {
            $temp = $s_date;
            $temp2 = $e_date;
            $count1 = 0;
            $count2 = 0;
            DB::table('loan')->insert([
                'user_id' => $emp,
                'deduction_name'=>$request->deduction_name,
                'name' => $nameData[0]->first_name." ".$nameData[0]->last_name,
                'start_date' => $s_date,
                'end_date' => $e_date,
                'total' => floatval($total),
                'remaning' => '0',
                'remaning_ins' => '0',
                'installment' => '0',
                'remaning_period'=>$remaining_period,
                'cycle' => $cycle,
                'amount' => floatval($amount),
                'salary_base'=>$request->salary_base,
                'status' => 0
            ]);
            return redirect()->back()->with('message','Loan Added Successfully!!');
        }
        else {
            DB::table('loan')->insert([
                'user_id' => $emp,
                'deduction_name'=>$request->deduction_name,
                'name' => $nameData[0]->first_name." ".$nameData[0]->last_name,
                'start_date' => $s_date,
                'end_date' => $e_date,
                'total' => $total,
                'remaning' => '0',
                'remaning_ins' => '0',
                'installment' => '0',
                'remaning_period'=>$remaining_period,
                'cycle' => $cycle,
                'amount' => floatval($amount),
                'salary_base'=>$request->salary_base,
                'status' => 0
            ]);
            return redirect()->back()->with('message','Loan Added Successfully!!');
        }

    }
    public function PauseLoan($id)
    {
        DB::table('loan')->where('id', $id)->update(['status' => 1]);
        return redirect()->back()->with('message', 'Loan Pause Successfully!');

    }
    public function StartLoan($id)
    {
        DB::table('loan')->where('id', $id)->update(['status' => 0]);
        return redirect()->back()->with('message', 'Loan Start Again Successfully!');
    }
    public function StopLoan($id,$s_date)
    {
        DB::table('loan')->where('id', $id)->where('start_date',$s_date)->update(['stop' => 1]);
        return redirect()->back()->with('message', 'Loan Stop Successfully!');
    }
    public function DeleteLoan($id,$s_date)
    {
        DB::table('loan')->where('id',$id)->where('start_date',$s_date)->delete();
        return redirect()->back()->with('message', 'Loan Deleted Successfully!');
    }
    public function deleteProcessPayroll($id)
    {
        $pro = Proceed::where('user_id',$id)->delete();
        return redirect()->back()->with('error', 'Processed Payroll Deleted!');
    }
    public function Adminlogout()
    {
        Auth::logout();

        return redirect('/login');
    }
    public function deperpermission()
    {
        $all= Deppermissinons::get();
        return view('Admin.department_permission',get_defined_vars());
    }

    public function deperpermissionsave(Request $request)
    {
        $new=  Deppermissinons::where('user_id', $request->user_id)->delete();
        $add=  new Deppermissinons;
        $d = implode(',', $request->department_name);
        $u=$request->user_id;
        $add->user_id = $u;
        $add->department_id = $d;
        $add->save();

        return redirect()->back()->with('message', 'Your department permission Add Successfully!');



    }

    public function deperpermissionupdate(Request $request,$id)
    {
        $edit= Deppermissinons::where('user_id',$id)->first();
        return view('Admin.updatedepartment_permission')->with(compact('edit'));
    }

    public function departmentpostper(Request $request,$id)
    {
        Deppermissinons::where('user_id', $id)->delete();

        $d = implode(',', $request->department_name);
        $dep_per=new Deppermissinons ;
        $dep_per->user_id = $id;
        $dep_per->department_id = $d;
        $dep_per->save();
        return redirect()->back()->with('message', 'Your department permission update Successfully!');


    }




    public function addattendance()
    {
        $auth_role = Auth::user()->user_role;
        $contains = Str::contains($auth_role, 'Supervisor');
        $d = "";
        if($contains)
        {
            $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
            $d = explode(',', $dep_per[0]->department_id);
        }
        return view('Admin.addattendance',get_defined_vars());
    }

    public function getuserdropdown(Request $request)
    {
        $data['users'] = User::where("department", $request->dep_id)
            ->get(["id","department","first_name","last_name"]);
        return response()->json($data);
    }


    public function DeductionReport()
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'report'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        }
        $deduction = null;
        $totalDeduction = null;
        $user = User::get();
        return view('Admin.deduction_report', get_defined_vars());
    }

    public function delete_attendance($id,$date)
    {
        $attn = Attendence::find($id);
        $holiday=Holiday::where('holiday_date',$attn->date)->count();
        if($holiday==1)
        {
            $ho = HolidayPay::where("user_id",$attn->user_id)->where('date',$date)->count();
            if($ho==1)
            {
                $deleted = DB::table('holiday_pays')->where('user_id',$attn->user_id)->where('date',$date)->delete();
            }
        }
        $attn->delete();
        return redirect()->back()->with('error', 'Attendance successfully Deleted!');
    }
    public function update_attendance($id)
    {
        $all_attn = Attendence::all();
        foreach($all_attn as $a_attn)
        {
            if($a_attn->start_time==null)
            {
                Attendence::where('id',$a_attn->id)->delete();
            }
            else if($a_attn->end_time==null) {
                Attendence::where('id',$a_attn->id)->delete();
            }
        }

        $auth_id = Auth::user()->id;
        $attn = Attendence::find($id);
        return view('Admin.attendance_update',get_defined_vars());
    }
    public function update_attendance_return(Request $request,$id,$userId)
    {
        $start_time = date('h:i:s A', strtotime($request->start_time));
        $end_time = date('h:i:s A', strtotime($request->end_time));

        $startTime = Carbon::parse($start_time);
        $endTime = Carbon::parse($end_time);

        $date_cur = Carbon::now()->format('Y-m-d h:i:s A');
        $auth_name = User::find($request->auth_id);
        $auth_full_name = $auth_name->first_name." ".$auth_name->last_name;
        $totalDuration =  $startTime->diff($endTime)->format('%h:%i:%s');
        $tempData = explode(':',$totalDuration);
        $hour = '';
        $min = '';
        $sec = '';
        if(intval($tempData[0])<=9)
        {
            $hour = '0'.$tempData[0];
        }
        else {
            $hour = $tempData[0];
        }
        if(intval($tempData[1])<=9)
        {
            $min = '0'.$tempData[1];
        }
        else {
            $min = $tempData[1];
        }
        if(intval($tempData[2])<=9)
        {
            $sec = '0'.$tempData[2];
        }
        else {
            $sec = $tempData[2];
        }
        $totalDuration = $hour.':'.$min.':'.$sec;
        $d = explode(':', $totalDuration);
        $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];
        $total_time_seconds = Carbon::parse($request->start_time)->diffInSeconds($endTime);
        $total_seconds = $total_time_seconds - 28800;
        $add_overtime_after_approve = $total_time_seconds - $total_seconds;
        $after = gmdate("h:i:s", $add_overtime_after_approve);
        $init = $total_seconds;
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;
        $overtime = $hours.':'.$minutes.':'.$seconds;
        $tempData = explode(':',$overtime);
        $hour = '';
        $min = '';
        $sec = '';
        if(intval($tempData[0])<=9)
        {
            $hour = '0'.$tempData[0];
        }
        else {
            $hour = $tempData[0];
        }
        if(intval($tempData[1])<=9)
        {
            $min = '0'.$tempData[1];
        }
        else {
            $min = $tempData[1];
        }
        if(intval($tempData[2])<=9)
        {
            $sec = '0'.$tempData[2];
        }
        else {
            $sec = $tempData[2];
        }
        $overtime = $hour.':'.$min.':'.$sec;
        $In_time_update = Attendence::find($request->user);
        if ($total_time_seconds >= 28800) {

            $In_time_update->id = $request->user;
            $In_time_update->user_id = $request->user_id;
            $In_time_update->start_time = $start_time;
            $In_time_update->end_time = $end_time;
            $In_time_update->date = $request->date;
            $In_time_update->work_time = $after;
            $In_time_update->overtime = $overtime;
            $In_time_update->total_hours = $total_time_seconds;
            $In_time_update->work_and_overtime = $add_overtime_after_approve;
            $In_time_update->status = 0;
            $In_time_update->update_by = $auth_full_name." : ".$date_cur;
            $In_time_update->save();
            $h_c = Holiday::where("holiday_date",$request->date)->count();
            if($h_c>0)
            {
                DB::table('holiday_pays')->where('user_id',$userId)->update(['total_time'=>$total_time_seconds]);
            }
        } else {
            $In_time_update->id = $request->user;
            $In_time_update->user_id = $request->user_id;
            $In_time_update->start_time = $start_time;
            $In_time_update->end_time = $end_time;
            $In_time_update->date = $request->date;
            $In_time_update->work_time = $totalDuration;
            $In_time_update->overtime = '00:00:00';
            $In_time_update->total_hours = $total_time_seconds;
            $In_time_update->work_and_overtime = $simplework;
            $In_time_update->status = 0;
            $In_time_update->update_by = $auth_full_name." : ".$date_cur;
            $In_time_update->save();
            $h_c = Holiday::where("holiday_date",$request->date)->count();
            if($h_c>0)
            {
                DB::table('holiday_pays')->where('user_id',$userId)->update(['total_time'=>$total_time_seconds]);
            }
        }
        return redirect('admin/attendance_history')->with('message', 'Your attendance Update successfully!');
    }


    public function DeductionResponse(Request $request)
    {
        $s_date = '';
        $e_date = '';
        if($request->start_date && $request->end_date && $request->dep && $request->user)
        {
            $s_date = $request->start_date;
            $e_date = $request->end_date;
            $dept = $request->dep;
            $user = User::get();
            $dep_name = DB::table('departments')->where('id',$dept)->get();
            $deduction = Proceed::where('user_id',$request->user)->where('dept', $dep_name[0]->department)->where('start_date',$s_date)->where('end_date',$e_date)->get();
            $totalDeduction = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction, SUM(bonus) as total_bonus, SUM(nis) as total_nis, SUM(nht) as total_nht, SUM(edtax) as total_edtax, SUM(income) as total_income'))->where('start_date',$s_date)->where('end_date',$e_date)->where('user_id',$request->user)->where('dept', $dep_name[0]->department)->get()->toArray();
            return view('Admin.deduction_report', get_defined_vars());
        }
        else if($request->start_date && $request->end_date && $request->dep)
        {
            $s_date = $request->start_date;
            $e_date = $request->end_date;
            $dept = $request->dep;
            $user = User::get();
            $dep_name = DB::table('departments')->where('id',$dept)->get();
            $deduction = Proceed::where('dept', $dep_name[0]->department)->where('start_date',$s_date)->where('end_date',$e_date)->get();
            $totalDeduction = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction, SUM(bonus) as total_bonus, SUM(nis) as total_nis, SUM(nht) as total_nht, SUM(edtax) as total_edtax, SUM(income) as total_income'))->where('dept', $dep_name[0]->department)->where('start_date',$s_date)->where('end_date',$e_date)->get()->toArray();
            return view('Admin.deduction_report', get_defined_vars());
        }
        else if($request->start_date && $request->end_date){
            $s_date = $request->start_date;
            $e_date = $request->end_date;
            $dept = $request->dep;
            $user = User::get();
            $deduction = Proceed::where('start_date',$s_date)->where('end_date',$e_date)->get();
            $totalDeduction = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction, SUM(bonus) as total_bonus, SUM(nis) as total_nis, SUM(nht) as total_nht, SUM(edtax) as total_edtax, SUM(income) as total_income'))->where('start_date',$s_date)->where('end_date',$e_date)->get()->toArray();
            return view('Admin.deduction_report', get_defined_vars());
        }
        else {
            return null;
        }



        $dept = $request->dep;
        $user = User::get();
    }
    public function AdmincreateUser(Request $request)
    {
        $data = $request->all();


        $user = User::create(
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'email' => $request->email,
                'user_role' => $request->user_role,
                'user_password' => $request->password,
                'password' => Hash::make($request->password),
            ]
        );

        unset($data['_token']);
        unset($data['photo']);
        unset($data['first_name']);
        unset($data['last_name']);
        unset($data['gender']);
        unset($data['user_role']);
        unset($data['email']);
        unset($data['password']);

        foreach ($data as $key => $value) {
            if (isset($value['view']) ? $value['view'] : '') {
                $view = $value['view'];
            } else {
                $view = 0;
            }

            if (isset($value['edit']) ? $value['edit'] : '') {
                $edit = $value['edit'];
            } else {
                $edit = 0;
            }

            if (isset($value['full']) ? $value['full'] : '') {
                $full = $value['full'];
            } else {
                $full = 0;
            }
            $permission = Permission::where('user_id', $user->id)->insert([
                'user_id' => $user->id, 'module' => $key,
                'view_access' => $view, 'edit_access' => $edit, 'full_access' => $full
            ]);
        }
        return redirect()->back()->with('success', 'Successfull Add user Permission');
    }

    public function UpdateRoles(Request $request, $id)
    {
        $update_role = Role::find($id);
        $update_role->name = $request->role_name_update;
        $update_role->save();
        return redirect()->back()->with('message', 'Successfull Update user Role');
    }
    public function starttime(Request $request)
    {

        $user_id = $request->user_id;
        $c_date = date('Y-m-d');
        DB::table('users')
        ->where('id', $user_id)
        ->update(['temp1' => '1','temp2' => $c_date]);
        $c_time = date('h:i:s A');
        $start_time = date('h:i:s A', strtotime($c_time));
        $user = User::select('id', 'add_attendance')->where('id', $user_id)->first();
        $user->add_attendance = 1;
        $user->save();

        $check_atten_one_time = Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
        if (!isset($check_atten_one_time)) {
            $atten = new Attendence();
            $atten->user_id = $user_id;
            $atten->start_time = $start_time;
            $atten->date = $c_date;
            $atten->work_time = '00:00:00';
            $atten->overtime = '00:00:00';
            $atten->status = 0;
            $atten->save();

            return redirect()->back()->with('message', 'Your attendance  successfully!');
        } else {
            return redirect()->back()->with('error', 'Your attendance Already Done!');
        }
    }

    public function user_attendance_history()
    {
        $user_id = Auth::user()->id;
        $atten_emp['emp_atten'] = Attendence::where('user_id', $user_id)->orderBy('date', 'DESC')->get();
        return view('Admin.userattendancedata', $atten_emp);
    }

    public function endtime(Request $request)
    {
        $user_id = $request->user_id;
        $atten_id = $request->atten_id;
        $In_time_update = Attendence::find($atten_id);
        $todayDate = Carbon::now()->format('d-m-Y');
        $c_date = date('Y-m-d');
        $c_time = date('h:i:s A');
        $end_timee = date('h:i:s A', strtotime($c_time));
        
        DB::table('users')
        ->where('id', $user_id)
        ->update(['temp1' => '2','temp3' => $c_time]);
        
        $user = User::where('id',$user_id)->first();
        $val1 = $user->temp1;
        $val2 = $user->temp2;
        
        if($c_date!==$val2)
        {
            $c_date = $val2;
        }

        $startTime = Carbon::parse($In_time_update->start_time);
        $endTime = Carbon::parse($c_time);

        $totalDuration =  $startTime->diff($endTime)->format('%h:%i:%s');
        $totalDuration =  $endTime->diff($startTime)->format('%h:%i:%s');
        $tempData = explode(':',$totalDuration);
        $hour = '';
        $min = '';
        $sec = '';
        if(intval($tempData[0])<=9)
        {
            $hour = '0'.$tempData[0];
        }
        else {
            $hour = $tempData[0];
        }
        if(intval($tempData[1])<=9)
        {
            $min = '0'.$tempData[1];
        }
        else {
            $min = $tempData[1];
        }
        if(intval($tempData[2])<=9)
        {
            $sec = '0'.$tempData[2];
        }
        else {
            $sec = $tempData[2];
        }
        $totalDuration = $hour.':'.$min.':'.$sec;
        $d = explode(':', $totalDuration);
        $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];


        $end_time = date('h:i:s A', strtotime($c_time));

        $total_time_seconds = Carbon::parse($In_time_update->start_time)->diffInSeconds($end_time);
        $duble_salary=$total_time_seconds*2;

        $total_seconds = $duble_salary - 28800;


        $add_overtime_after_approve = $total_time_seconds - $total_seconds;
        $after = gmdate("h:i:s A", $add_overtime_after_approve);
        //dd($after,$total_time_seconds,$add_overtime_after_approve);

        $overtime = gmdate("h:i:s A", $total_seconds);
        // dd($total_hours,$total_minutes,$total_seconds);
        $holiday=Holiday::where('holiday_date',$request->date)->count();
        if ($holiday==1) {
            $check_atten_one_time = Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
            if (isset($check_atten_one_time)) {
                $In_time_update->user_id = $user_id;
                $In_time_update->end_time = $end_time;
                $In_time_update->date = $c_date;
                $In_time_update->work_time = $after;
                $In_time_update->overtime = '00:00:00';
                $In_time_update->total_hours = $duble_salary;
                $In_time_update->work_and_overtime = $add_overtime_after_approve;
                $In_time_update->status = 0;
                $In_time_update->save();

                return redirect()->back()->with('success', 'Your attendance successfully!');
            } else {
                return redirect()->back()->with('error', 'Your attendance Already Done!');
            }
        }

        else
        {
            $check_atten_one_time = Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
            if (isset($check_atten_one_time)) {
                if ($total_time_seconds >= 28800) {
                    $In_time_update->user_id = $user_id;
                    $In_time_update->end_time = $end_time;
                    $In_time_update->date = $c_date;
                    $In_time_update->work_time = $after;
                    $In_time_update->overtime = $overtime;
                    $In_time_update->total_hours = $total_time_seconds;
                    $In_time_update->work_and_overtime = $add_overtime_after_approve;
                    $In_time_update->status = 0;
                    $In_time_update->save();
                } else {
                    $In_time_update->user_id = $user_id;
                    $In_time_update->end_time = $end_time;
                    $In_time_update->date = $c_date;
                    $In_time_update->work_time = $totalDuration;
                    $In_time_update->overtime = '00:00:00';
                    $In_time_update->total_hours = $total_time_seconds;
                    $In_time_update->work_and_overtime = $simplework;
                    $In_time_update->status = 0;
                    $In_time_update->save();
                }
                return redirect()->back()->with('success', 'Your attendance successfully!');
            } else {
                return redirect()->back()->with('error', 'Your attendance Already Done!');
            }
        }
        // dd( $total_time_hours,$end_time);
    }

    public function clock()
    {
        $user_id = Auth::user()->id;
        $c_time = date('h:i:s A');
        $c_date = date('Y-m-d');
        $user = User::where('id',$user_id)->first();
        $val1 = $user->temp1;
        $val2 = $user->temp2;
        $val3 = $user->temp3;

        $d = '';
        $startTime = Carbon::parse($c_time);
        $endTime = Carbon::parse($val3);

        if($val1=='2' && $val2!=$c_date)
        {
            $totalDuration =  $startTime->diff($endTime)->format('%h:%i:%s');
            $d = explode(':', $totalDuration);
            DB::table('users')
                ->where('id', $user_id)
                ->update(['temp1' => '0','temp2' => '0','temp3' => '0']);
            $user = User::where('id',$user_id)->first();
            $val1 = $user->temp1;
            $val2 = $user->temp2;
            $val3 = date('h:i:s A');
        }


        if($val1=='0')
        {
            $user_atten['start_time'] = Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
        }
        else if($val1=='1') {
            $user_atten['start_time'] = Attendence::where('user_id', $user_id)->where('date', $val2)->first();
        }
        else if($val1=='2') {
            $user_atten['start_time'] = Attendence::where('user_id', $user_id)->where('date', $val2)->first();

        }

        return view('Admin.clock', $user_atten);
    }
    public function Adminpermissionupdate(Request $request, $id)
    {
        // dd($emp);



        $data = $request->all();
        $employee = User::find($id);
        if (auth::user()->user_role == 'admin') {
            // if (1 == $id && auth::user()->user_role == 'admin') {
            //     return redirect()->back()->with('error', 'Your Are not allow the super admin Permision ');
            // }
            $user = User::select('id', 'password', 'user_password', 'first_name', 'last_name', 'email', 'photo', 'user_role', 'dob')->where('id', $id)->first();
            $adminroles = Permission::where('user_id', $id)->get()->toarray();
            if ($request->isMethod('post')) {
                if ($request->photo != '') {
                    $path = public_path() . '/uploads/employees/';
                    //code for remove old file
                    if ($employee->photo != ''  && $employee->photo != null) {
                        $file_old = $path . $employee->photo;
                        unlink($file_old);
                    }
                    //upload new file
                    $file = $request->photo;
                    $filename = $file->getClientOriginalName();
                    $file->move($path, $filename);
                    //for update in table
                    $employee->update(['photo' => $filename]);
                }
                $employee->update([
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'gender' => $request->gender,
                    'dob' => $request->dob,
                    'user_role' => $request->user_role,
                ]);
                if ($employee->user_password == $request->user_password) {
                    $employee->password = $employee->password;
                    $employee->user_password = $employee->user_password;
                    $employee->save();
                } else {
                    $employee->password = Hash::make($request->password);
                    $employee->user_password = $request->password;
                    $employee->save();
                }
                unset($data['_token']);
                unset($data['photo']);

                unset($data['first_name']);
                unset($data['last_name']);
                unset($data['gender']);
                unset($data['user_role']);
                unset($data['email']);
                unset($data['password']);


                Permission::where('user_id', $id)->delete();
                foreach ($data as $key => $value) {
                    if (isset($value['view']) ? $value['view'] : '') {
                        $view = $value['view'];
                    } else {
                        $view = 0;
                    }

                    if (isset($value['edit']) ? $value['edit'] : '') {
                        $edit = $value['edit'];
                    } else {
                        $edit = 0;
                    }

                    if (isset($value['full']) ? $value['full'] : '') {
                        $full = $value['full'];
                    } else {
                        $full = 0;
                    }
                    $permission = Permission::where('user_id', $id)->insert([
                        'user_id' => $id, 'module' => $key,
                        'view_access' => $view, 'edit_access' => $edit, 'full_access' => $full
                    ]);
                }

                return redirect()->back()->with('message', 'Successful Updated User Permission');
            }
            $simple = Role::where('name', '!=', 'super admin')->get();
            $superadmin = Role::get();

            return view('Admin.admin_permistion', compact('adminroles', 'user', 'simple', 'superadmin'));
        } else {
            return redirect()->back()->with('error', 'This Feature are Restricted!');
        }
    }


    public function AddAdmin()
    {
        // $users=User::where('user_role','super_admin')->first();

        if (auth::user()->user_role == 'admin') {
            $users = User::get();

            return view('Admin.addadmin', compact('users'));
        } else {

            return redirect()->back()->with('error', 'This Feature are Restricted!');
        }
    }

    public function AddRoles()
    {
        $roles = Role::where('name','!=','admin')->get();
        if (request()->isMethod('post')) {
            $Add_role = new Role();
            $Add_role->name = request()->role_name;
            $Add_role->save();
            return redirect()->back()->with('message', 'Role are Add Succesfully!');
        }
        return view('Admin.addroles', get_defined_vars());
    }
    public function department()
    {

        $auth_id = Auth::user()->id;
        $auth_role = Auth::user()->user_role;


        $data = array();
        $get_dep = Department::get()->toArray();
        $contains = Str::contains($auth_role, 'Supervisor');
        if($contains)
        {
            $dep_per = Deppermissinons::where('user_id',$auth_id)->get();
            $d = explode(',', $dep_per[0]->department_id);
            foreach($d as $dep)
            {
                $department['view_department'] = Department::where('department',$dep)->get()->toArray();
                array_push($data,$department['view_department']);
            }
        }
        else if(!$contains){
            foreach($get_dep as $dep)
            {
                $department['view_department'] = Department::where('department',$dep["department"])->get()->toArray();
                array_push($data,$department['view_department']);
            }
        }
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'department'])->count();
        if ($permision == 0) {
            return redirect('/admin/dashboard')->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'department'])->first()->toarray();
            // dd($Employepermision); die;
        }
        return view('Admin.department',get_defined_vars());
    }
    public function updateThreshold(Request $request)
    {
        $htreshold = Threshold::get()->toArray();
        $count = Accumulate::get()->count();
        $currentThreshold = $htreshold[0]['amount'];
        if ($count == 0) {
            $startDate = '2021-12-21';
            $min = 13;
            $endDate = date('Y-m-d', strtotime($startDate . ' + ' . $min . ' days'));
            $addVal = new Accumulate();
            $addVal->payroll_no = $count += 1;
            $addVal->start_date = $startDate;
            $addVal->end_date = $endDate;
            $addVal->accumalative_payrol_value = $currentThreshold;
            $addVal->save();
        } else {
            $data = Accumulate::get()->last()->toArray();
            $startDate = $data['end_date'];
            $datedata = $data['end_date'];
            $lastThreshold = $data['accumalative_payrol_value'];
            $current = Date('Y-m-d');
            // dd($current, $datedata);
            $total_time_seconds = Carbon::parse($current)->diffInDays($datedata);
            $min = 1;
            $startDate = date('Y-m-d', strtotime($startDate . ' + ' . $min . ' days'));
            $date = 14;
            $endDate = date('Y-m-d', strtotime($startDate . ' + ' . $date . ' days'));

            //dd($total_time_seconds);
            //dd($total_time_seconds);
            if ($total_time_seconds == 14) {
                $addVal = new Accumulate();
                $addVal->payroll_no = $count += 1;
                $addVal->start_date = $startDate;
                $addVal->end_date = $endDate;
                $addVal->accumalative_payrol_value = $currentThreshold + $lastThreshold;
                $addVal->save();
            }
        }

        return redirect()->back();
    }
    public function  add_department(Request $request)
    {
        $add_Department = new Department();
        $add_Department->department = $request->department_name;
        $add_Department->status = 0;
        $add_Department->save();

        return redirect()->back()->with('message', 'Department successfully Addedd!');
    }
    public function  edit_department(Request $request, $id)
    {
        $edit_department = Department::find($id);
        $edit_department->department = $request->department_name;
        $edit_department->save();

        return redirect()->back()->with('message', 'Department successfully Updated!');
    }

    public function userdepartment($id)
    {
        $users=User::where('department',$id)->get();
        return view('admin.userdepartment');
    }

    public function depart_status_deactive($id)
    {

        $depart_status_deactive = Department::find($id);
        $depart_status_deactive->status = 0;
        $depart_status_deactive->save();
        return redirect()->back()->with('message', 'Department successfully Deactive!');
    }
    public function depart_status_active($id)
    {

        $depart_status_active = Department::find($id);
        $depart_status_active->status = 1;
        $depart_status_active->save();
        return redirect()->back()->with('message', 'Department successfully Active!');
    }
    public function  delete_department(Request $request, $id)
    {
        $delete_department = Department::find($id);
        $delete_department->delete();

        return redirect()->back()->with('error', 'Department successfully Deleted!');
    }


    public function attendance_history()
    {
        $attn_dep = '';
        $temp_table = '0';
        $temp_attn = "0";
        $employees = User::select('first_name','last_name','id')->get()->toArray();
        $temp_s_d = '';
        $res_attn = '';
        $temp_e_d = '';
        $auth_role = Auth::user()->user_role;
        $flag = false;
        $user_arr = array();
        $d = '';
        $atten_emp = array();
        $temp = '';
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'attendance'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'attendance'])->first()->toarray();

            $contains = Str::contains($auth_role, 'Supervisor');
            $data = array();
            $attn_data = array();
            if($contains)
            {
                $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
                $d = explode(',', $dep_per[0]->department_id);
                $tempArr = array();
                foreach($d as $dep)
                {
                    $dep_name = Department::where('department',$dep)->first();
                    array_push($tempArr,$dep_name->id);
                }
                $now = Carbon::now();
                $startOfMonth = $now->startOfMonth('Y-m-d');
                $endOfMonth = $now->endOfMonth()->format('Y-m-d');
                $res_attn = DB::table('users')->where('department',$tempArr)
                    ->join('attendences','users.id','=','attendences.user_id')
                    ->where('attendences.date','>=',$startOfMonth)
                    ->where('attendences.date','<=',$endOfMonth)
                    ->select('users.first_name','users.last_name','attendences.*')->paginate(50);
                $flag = false;
                $temp_table = "0";

            }
            else {
                $now = Carbon::now();
                $startOfMonth = $now->startOfMonth()->format('Y-m-d');
                $endOfMonth = $now->endOfMonth()->format('Y-m-d');
                $res_attn = DB::table('users')
                    ->join('attendences','users.id','=','attendences.user_id')
                    ->where('attendences.date','>=',$startOfMonth)
                    ->where('attendences.date','<=',$endOfMonth)
                    ->select('users.first_name','users.last_name','attendences.*')
                    ->orderBy('attendences.date', 'desc')->paginate(50);
                $flag = false;
            }
            $dep_arr = array();
            $flag2 = 0;
            $contains = gettype($d);
            if(strcmp($contains,"array")==0)
            {
                foreach($d as $depr)
                {
                    $dep = DB::table('departments')->where('department',$depr)->get()->toArray();
                    array_push($dep_arr,$dep);
                }
                $flag2 = 1;
            }
            else {
                $dep = DB::table('departments')->get();
                $flag2 = 0;
            }
            return view('Admin.attendance_history')->with(compact('Employepermision','flag','attn_data','flag2','dep','dep_arr','atten_emp','user_arr','attn_dep','temp_attn','temp_s_d','temp_e_d','res_attn','temp_table','employees'));
        }

    }
    public function attent_status_disapprove($id)
    {
        $attent_status_disapprove = Attendence::find($id);
        $d = explode(':', $attent_status_disapprove->work_time);
        $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];

        $attent_status_disapprove->status = 0;
        $date_cur = Carbon::now()->format('Y-m-d h:i:s A');
        $user_name = Auth::user()->first_name." ".Auth::user()->last_name;
        $attent_status_disapprove->update_by = $user_name." ".$date_cur;
        $attent_status_disapprove->work_and_overtime = $simplework;
        $ans = $attent_status_disapprove->update();
        if($ans)
        {
            return response()->json([
                'val'=>1,
                'name'=>$user_name,
                'work_time'=>$attent_status_disapprove->work_time,
                'dataTime'=>$todayDate = Carbon::now()->format('Y-m-d h:i:s A')
            ]);
        }
        else {
            return response()->json([
                'val'=>2,
            ]);
        }
        return redirect()->back()->with('message', 'Attendance successfully Disapproved!');
    }
    public function processedPayroll()
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'payrol'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'payrol'])->first()->toarray();
            // dd($Employepermision); die;

        }

        $processPayroll = Proceed::get();
        $dep_arr = Department::where('status','1')->get()->toArray();
        $threshold = Threshold::get()->toArray();
        $totalprocessed = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction'))->get()->toArray();
        return view('Admin.processedPayroll', get_defined_vars());
    }
    public function searchProcessPayroll(Request $request)
    {
        $dep_arr = Department::where('status','1')->get()->toArray();
        $threshold = Threshold::get()->toArray();
        if($request->start_date && $request->end_date && $request->department &&$request->cycle)
        {
            $processPayroll = Proceed::where('start_date',$request->start_date)->where('end_date',$request->end_date)->where('dept',$request->department)->where('cycle',$request->cycle)->get();

            $totalprocessed = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction'))->where('start_date',$request->start_date)->where('end_date',$request->end_date)->where('dept',$request->department)->where('cycle',$request->cycle)->get()->toArray();
            return view('Admin.processedPayroll', get_defined_vars());
        }
        else if($request->start_date && $request->end_date && $request->cycle)
        {
            $processPayroll = Proceed::where('start_date',$request->start_date)->where('end_date',$request->end_date)->where('cycle',$request->cycle)->get();

            $totalprocessed = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction'))->where('start_date',$request->start_date)->where('end_date',$request->end_date)->where('cycle',$request->cycle)->get()->toArray();
            return view('Admin.processedPayroll', get_defined_vars());
        }
        else if($request->start_date && $request->end_date)
        {
            $processPayroll = Proceed::where('start_date',$request->start_date)->where('end_date',$request->end_date)->get();

            $totalprocessed = Proceed::select(DB::raw('SUM(total_pay) as total_pay ,SUM(net_pay) as net_pay, SUM(total_deduction) as total_deduction'))->where('start_date',$request->start_date)->where('end_date',$request->end_date)->get()->toArray();
            return view('Admin.processedPayroll', get_defined_vars());
        }
    }
    public function attent_status_approve($id)
    {

        $attent_status_disapprove = Attendence::find($id);
        $d = explode(':', $attent_status_disapprove->work_time);
        $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];
        $o = explode(':', $attent_status_disapprove->overtime);
        $simpleover = ($o[0] * 3600) + ($o[1] * 60) + $o[2];
        $attent_status_disapprove->status = 1;
        $date_cur = Carbon::now()->format('Y-m-d h:i:s A');
        $user_name = Auth::user()->first_name." ".Auth::user()->last_name;
        $attent_status_disapprove->update_by = $user_name." ".$date_cur;
        $attent_status_disapprove->work_and_overtime = $simplework + $simpleover;
        $ans = $attent_status_disapprove->update();
        if($ans)
        {
            return response()->json([
                'val'=>1,
                'name'=>$user_name,
                'overtime'=>$attent_status_disapprove->overtime,
                'work_time'=>$attent_status_disapprove->work_time,
                'dataTime'=>$todayDate = Carbon::now()->format('Y-m-d h:i:s A')
            ]);
        }
        else {
            return response()->json([
                    'val'=>2
                ]
            );
        }
        return redirect()->back()->with('success', 'Attendance successfully Approved!');
    }
    public function employees()
    {
        $employees = User::get();
        //admin restriction
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'employes'])->count();
        if ($permision == 0) {
            return redirect('/admin/dashboard')->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'employes'])->first()->toarray();
            // dd($Employepermision); die;

        }
        return view('Admin.employee.index', compact('employees', 'Employepermision'));
    }
    public function department_employe(Request $request)
    {
        if($request->department)
        {
            $employees = User::where('department',$request->department)->get();
        }
        else {
            $employees = User::get();
        }
        //admin restriction
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'employes'])->count();
        if ($permision == 0) {
            return redirect('/admin/dashboard')->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'employes'])->first()->toarray();
            // dd($Employepermision); die;

        }
        return view('Admin.employee.index', compact('employees', 'Employepermision'));
    }

    public function employeeDestroy($id){

        $emp=User::where('id',$id)->first();
        $emp->delete();
        return redirect()->back()->with('error', 'Employee Delete successfully!');


    }

    public function employeeCreate()
    {
        return view('Admin.employee.create');
    }

    public function employeeStore(Request $request)
    {
        // dd($request->all());

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return back()->with('error', 'This user email already exists.');
        } else {
            $user = User::create(
                [
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'gender' => $request->gender,
                    'dob' => $request->dob,
                    'password' => Hash::make($request->password),
                    'user_password' => $request->password,
                    'residence_address' => $request->residence_address,
                    'employment_status' => $request->employment_status,
                    'hire_date' => $request->hire_date,
                    'employee_id' => $request->employee_id,
                    'regular_hours' => $request->regular_hours,
                    'hourly_rate' => $request->hourly_rate,
                    'ot_rate' => $request->ot_rate,
                    'department' => $request->department,
                    'statutory_deductions' => $request->statutory_deductions,
                    'attn_inc_rate' => $request->attn_inc_rate,
                    'phone_number' => $request->phone_number,
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_number' => $request->emergency_contact_number,
                    'salary_base' => $request->salary_base,
                    'personal_email' => $request->personal_email,
                    'illness' => $request->illness,
                    'relation' => $request->relation,
                    'daily_pay' => $request->daily_pay,
                    'education' => $request->education,
                    'experience' => $request->experience,
                    'id_type' => $request->id_type,
                    'id_number' => $request->id_number,
                    'bank' => $request->bank,
                    'account_number' => $request->account_number,
                    'branch' => $request->branch,
                    'bank_photo' => 'kkk',
                    'trn' => $request->trn,
                    'nis' => $request->nis,
                    'user_role' => 'Employee',
                ]
            );
            if (request()->hasfile('photo')) {
                $image = request()->file('photo');
                $filename = time() . '.' . $image->getClientOriginalName();
                $movedFile = $image->move('uploads/employees', $filename);
                $user->photo = $filename;
                $user->save();
            } else {
                $user->save();
            }
            $details = [
                'title' => 'Email and Password',
                'body' => 'Hi...' . $request->first_name . 'Your Email address : ' . $request->email . '' . 'and Your password : ->  ' . $request->password
            ];

            Mail::to($request->email)->send(new TestMail($details));

            // $user = User::where('email', '_mainaccount@briway.uk')->first();

            // \Mail::to($user->email)->send(new TestMail($details));
            // $admin = [
            //     'title' => 'user  Email and Password',
            //     'body' =>'Hi...'.$request->first_name.'Your Email address : '.$request->email.''.'and Your password : ->  '. $request->password
            // ];


            return redirect()->route('admin.employees')->with('message', 'Employee data saved successfully.');
        }
    }


    public function employeeEdit($id)
    {
        $permision = Permission::where('user_id', auth::user()->id)->where('module', 'employes')->first()->toarray();
        if ($permision['full_access'] == 1 || $permision['edit_access'] == 1) {

            $emp = User::find($id);

            return view('Admin.employee.edit', compact('emp'));
        } else {
            // dd( $permision);
            return redirect('/admin/dashboard')->with('error', 'This Feature is restricted For You !');
            // dd($Employepermision); die;
        }
    }


    public function employeeView($id)
    {
        $emp = User::find($id);

        return view('Admin.employee.view', compact('emp'));
    }

    public function employeeUpdate(Request $request, $id)
    {
        $emp = User::find($id);

        $emp->update([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'residence_address' => $request->residence_address,
            'employment_status' => $request->employment_status,
            'hire_date' => $request->hire_date,
            'employee_id' => $request->employee_id,
            'regular_hours' => $request->regular_hours,
            'hourly_rate' => $request->hourly_rate,
            'ot_rate' => $request->ot_rate,
            'salary_base' => $request->salary_base,
            'department' => $request->department,
            'statutory_deductions' => $request->statutory_deductions,
            'attn_inc_rate' => $request->attn_inc_rate,
            'phone_number' => $request->phone_number,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'personal_email' => $request->personal_email,
            'illness' => $request->illness,
            'relation' => $request->relation,
            'daily_pay' => $request->daily_pay,
            'education' => $request->education,
            'experience' => $request->experience,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'bank' => $request->bank,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
            'bank_photo' => 'null',
            'trn' => $request->trn,
            'nis' => $request->nis,
        ]);
        if (request()->hasfile('photo')) {
            $image = request()->file('photo');
            $filename = time() . '.' . $image->getClientOriginalName();
            $movedFile = $image->move('uploads/employees', $filename);
            $emp->photo = $filename;
            $emp->save();
        } else {
            $emp->save();
        }
        return redirect()->route('admin.employees')->with('message', 'Employee updated succeddfuly.');
    }



    public function employeeShow($id)
    {
        $emp = User::find($id);
        return view('Admin.employee.show', compact('emp'));
    }
    public function update_profile(Request $request)
    {

        $user = User::find(Auth::user()->id);
        if (isset($request->photo)) {
            $image = $request->file('photo');
            $imageName = $image->getClientOriginalName();

            $user->update([
                'photo' => $imageName,
            ]);
            $path = $image->move(public_path('uploads/employees'), $imageName);
        }



        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,

        ]);

        if (isset($request->c_password)) {
            $request->validate([
                'new_password' => 'required|min:8',
                'confirm_password' => 'required_with:password|same:new_password|min:8'

            ]);
            if (Hash::check($request->c_password, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->new_password),
                ]);
                $msg = "Your profile has been updated";
                $request->session()->flash('message', $msg);
                return back();
            } else {
                $msg = "Your Password does't match";
                $request->session()->flash('error', $msg);
                return redirect('/profile');
            }
        } else {
            $msg = "Your profile has been updated";
            $request->session()->flash('message', $msg);
            return back();
        }
    }
    public function threshold(Request $request)
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->first()->toarray();
            // dd($Employepermision); die;
        }
        $threshold['threshold'] = Threshold::all();
        return view('Admin/threshold', $threshold)->with(compact('Employepermision'));
    }
    public function accumulate_threshold()
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->first()->toarray();
            // dd($Employepermision); die;
        }
        $threshold['threshold'] = Accumulate::all();
        return view('Admin/accumulate_threshold', $threshold)->with(compact('Employepermision'));
    }
    public function update_acc_threshold(Request $request)
    {
        $response = DB::table('accumulates')->orderBy('id', 'DESC')->first();
        $payrol_no = intval($response->payroll_no);
        $payrol_no = $payrol_no+1;
        $s_date = $response->end_date;
        $temp1 = strtotime($s_date);
        $date = strtotime("+1 day", $temp1);
        $s_date = date('Y-m-d',$date);
        $temp1 = strtotime($s_date);
        $date = strtotime("+13 day", $temp1);
        $e_date = date('Y-m-d',$date);
        $last_accumulate = floatval($response->accumalative_payrol_value);
        $result = DB::table('thresholds')->select('amount as amt')->where('days','14')->get();
        $update_accumulate = floatval($result[0]->amt)+floatval($last_accumulate);
        DB::table('accumulates')->insert(['payroll_no'=>$payrol_no,'start_date'=>$s_date,'end_date'=>$e_date,'accumalative_payrol_value'=>$update_accumulate]);
        return redirect()->back()->with('message','Update successfully!');
    }
    public function delete_accumulate_threshold($payroll_no)
    {
        DB::table('accumulates')->where('payroll_no',$payroll_no)->delete();
        return redirect()->back()->with('error','Delete Successfully!!');
    }
    public function monthly_accumulate_threshold()
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->first()->toarray();
            // dd($Employepermision); die;
        }
        $threshold['threshold'] = DB::table('accumulates_monthly')->get();
        return view('Admin/accumulate_monthly_threshold', $threshold)->with(compact('Employepermision'));
    }
    public function update_monthly_acc_threshold(Request $request)
    {
        $response = DB::table('accumulates_monthly')->orderBy('id', 'DESC')->first();
        $payrol_no = intval($response->payroll_no);
        $payrol_no = $payrol_no+1;
        $s_date = $response->end_date;
        $temp1 = strtotime($s_date);
        $date = strtotime("+1 day", $temp1);
        $s_date = date('Y-m-d',$date);
        $temp1 = strtotime($s_date);
        $date = strtotime("+31 day", $temp1);
        $e_date = date('Y-m-d',$date);
        $last_accumulate = floatval($response->accumulate_value);
        $result = DB::table('thresholds')->select('amount as amt')->where('days','30')->get();
        $update_accumulate = floatval($result[0]->amt)+floatval($last_accumulate);
        DB::table('accumulates_monthly')->insert(['payroll_no'=>$payrol_no,'start_date'=>$s_date,'end_date'=>$e_date,'accumulate_value'=>$update_accumulate]);
        return redirect()->back()->with('message','Update successfully!');
    }
    public function delete_monthly_accumulate_threshold($payroll_no)
    {
        DB::table('accumulates_monthly')->where('payroll_no',$payroll_no)->delete();
        return redirect()->back()->with('error','Delete Successfully!!');
    }
    public function one_time_deduction()
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->first()->toarray();
            // dd($Employepermision); die;
        }
        $threshold['threshold'] = DB::table('one_time_deduction')->get();
        return view('Admin/one_time_deduction', $threshold)->with(compact('Employepermision'));
    }
    public function add_one_time_deduction()
    {
        $dep_arr = DB::table('users')->select('id','first_name','last_name')->where('user_role','!=','admin')->get();
        return view("Admin.add_one_time_deduction",get_defined_vars());
    }
    public function delete_one_time_deduction($id,$s_period)
    {
        DB::table('one_time_deduction')->where('id',$id)->where('start_period',$s_period)->delete();
        return redirect()->back()->with('error','Delete Successfully!!');
    }
    public function edit_one_time_deduction($id,$s_period)
    {
        $dep_arr = DB::table('users')->select('id','first_name','last_name')->where('user_role','!=','admin')->get();
        $data = DB::table('one_time_deduction')->where('employee',$id)->where('start_period',$s_period)->get();
        return view('Admin.edit_one_time_deduction',get_defined_vars());
    }
    public function submit_edit_one_time_deduction(Request $request)
    {
        $end_date = strtotime($request->period);
        $end_date = strtotime("+13 day",$end_date);
        $end_date = date('Y-m-d',$end_date);
        DB::table('one_time_deduction')->where('employee',$request->employee)->update([
            'deduction_name'=>$request->deduction_name,
            'start_period'=>$request->period,
            'end_period'=>$end_date,
            'cycle'=>$request->cycle,
            'amount'=>$request->amount,
            'status'=>'0'
        ]);
        return redirect('admin/one_time_deduction')->with('message','Deduction Update Successfully!!');
    }
    public function submit_one_time_deduction(Request $request)
    {
        $end_date = strtotime($request->period);
        $end_date = strtotime("+13 day",$end_date);
        $end_date = date('Y-m-d',$end_date);

        DB::table('one_time_deduction')->insert([
            'deduction_name'=>$request->deduction_name,
            'employee'=>$request->employee,
            'start_period'=>$request->period,
            'end_period'=>$end_date,
            'cycle'=>$request->cycle,
            'amount'=>$request->amount,
            'salary_base'=>$request->salary_base,
            'status'=>'0'
        ]);
        return redirect()->back()->with('message','Deduction Added Successfully!!');
    }

    public function continuous_deduction()
    {
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'thresold'])->first()->toarray();
            // dd($Employepermision); die;
        }
        $threshold['threshold'] = DB::table('continuous_deduction')->get();
        return view('Admin/continuous_deduction', $threshold)->with(compact('Employepermision'));
    }
    public function add_continuous_deduction()
    {
        $dep_arr = DB::table('users')->select('id','first_name','last_name')->where('user_role','!=','admin')->get();
        return view('Admin/add_continuous_deduction',get_defined_vars());
    }
    public function delete_continuous($id,$s_date)
    {
        DB::table('continuous_deduction')->where('id',$id)->where('start_period',$s_date)->delete();
        return redirect()->back()->with('error','Deduction Deleted Successfully!!');
    }
    public function submit_continuous_deduction(Request $request)
    {
        $count = DB::table('continuous_deduction')->where('user_id',$request->employee)->where('start_period',$request->period)->where('cycle',$request->cycle)->count();
        $names = DB::table('users')->select('first_name','last_name')->where('id',$request->employee)->get();
        DB::table('continuous_deduction')->insert([
            'user_id'=>$request->employee,
            'user_name'=>$names[0]->first_name." ".$names[0]->last_name,
            'deduction_name'=>$request->deduction_name,
            'start_period'=>$request->period,
            'next_period'=>$request->period,
            'cycle'=>$request->cycle,
            'amount'=>$request->amount,
            'salary_base'=>$request->salary_base,
            'action'=>'0'
        ]);
        return redirect()->back()->with('message',"Deduction Added Successfully!!");

    }
    public function stop_continuous($id,$s_period)
    {
        DB::table('continuous_deduction')->where('id',$id)->where('start_period',$s_period)->update(['action'=>'1']);
        return redirect()->back()->with('message','Deduction Stop Successfully!!');
    }
    public function start_continuous($id,$s_period)
    {
        DB::table('continuous_deduction')->where('id',$id)->where('start_period',$s_period)->update(['action'=>'0']);
        return redirect()->back()->with('message','Deduction Start Successfully!!');
    }
    public function add_deduction(Request $request)
    {

        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'deduction', 'full_access' => 1])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        }

        return view('Admin/add_deduction');
    }
    public function add_threshold(Request $request)
    {

        $permision = Permission::where('user_id', auth::user()->id)->where('module', 'thresold')->first()->toarray();
        if ($permision['full_access'] == 1) {
            if ($request->ismethod('post')) {

                $request->validate([
                    'year' => 'required',
                    'cycle' => 'required',
                    'amount' => 'required',
                    'days' => 'required',
                    'paid_by' => 'required'

                ]);

                //Threshold data save start
                $threshold = new Threshold();
                $threshold->year = $request->year;
                $threshold->cycle = $request->cycle;
                $threshold->amount = $request->amount;
                $threshold->days = $request->days;
                $threshold->paid_by = $request->paid_by;
                $threshold->save();
                return redirect('admin/threshold')->with('message', 'Thresold Added Successfully!');
            }
        } else {
            // dd( $permision);
            return back()->with('error', 'This Feature is restricted For You !');
            // dd($Employepermision); die;
        }
        return view('Admin.add_threshold');
    }
    public function edit_threshold(Request $request, $id)
    {
        $edit_threshold['edit_threshold'] = Threshold::find($id);

        $permision = Permission::where('user_id', auth::user()->id)->where('module', 'thresold')->first()->toarray();
        if ($permision['full_access'] == 1 || $permision['edit_access'] == 1) {

            $emp = User::find($id);

            return view('Admin/edit_threshold', $edit_threshold);
        } else {
            // dd( $permision);
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
            // dd($Employepermision); die;
        }
    }
    public function edit_deduction(Request $request, $id)
    {
        $edit_deduction['edit_deduction'] = Deduction::find($id);
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'deduction', 'edit_access' => 1])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        }
        return view('Admin/edit_deduction', $edit_deduction);
    }
    public function update_deduction(Request $request, $id)
    {
        // $request->validate([
        //     'name' => 'required',
        //     'nis_fix_value' => 'required',
        //     'percentage_value' => 'required',
        //     'type_deduction' => 'required'
        // ]);
        $deduction = Deduction::find($id);
        $deduction->name = $request->name;
        $deduction->nis_fix_value = $request->percentage;
        $deduction->nis = $request->nis;
        $deduction->percentage_value = $request->type_value;
        $deduction->type_deduction = $request->type;
        $deduction->save();
        session()->flash('message', 'Deduction/Contribution successfully Updated!');
        return redirect('admin/deduction');
    }

    public function update_threshold(Request $request, $id)
    {
        $request->validate([
            'year' => 'required',
            'cycle' => 'required',
            'amount' => 'required',
            'days' => 'required',
            'paid_by' => 'required'

        ]);
        //Threshold data save start
        $threshold = Threshold::find($id);
        $threshold->year = $request->year;
        $threshold->cycle = $request->cycle;
        $threshold->amount = $request->amount;
        $threshold->days = $request->days;
        $threshold->paid_by = $request->paid_by;
        $threshold->save();
        session()->flash('message', 'Threshold successfully Updated!');
        return redirect('admin/threshold');
    }
    public function delete_threshold(Request $request, $id)
    {
        $threshold = Threshold::find($id);
        $threshold->delete();
        session()->flash('error', 'Threshold successfully Deleted!');
        return redirect('admin/threshold');
    }



    public function deduction()
    {
        $get_deduction = Deduction::all();
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'deduction'])->count();
        if ($permision == 0) {
            return redirect()->back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'deduction'])->first()->toarray();
            // dd($Employepermision); die;
        }
        return view('Admin.deduction', get_defined_vars());
    }



    public function create_deduction(Request $request)
    {
        // $request->validate([
        //     'name' => 'required',
        //     'percentage_value' => 'required',
        //     'nis_fix_value' => 'required',
        //     'type_deduction' => 'required'

        //     ]);
        //Threshold data save start
        $deduction = Deduction::create([
            'name' => $request->name,
            'percentage_value' => $request->type_value,
            'nis_fix_value' => $request->percentage,
            'nis' => $request->Nis,
            'type_deduction' => $request->type

        ]);

        return redirect()->back()->with('message', 'payrol Deduction successfully Add');
    }
    public function admin_attendance(Request $request)
    {


        $c_date = date('Y-m-d');
        $holiday=Holiday::where('holiday_date',$request->date)->count();
        if (intval($holiday)==1) {

            $start_time = date('h:i:s A', strtotime($request->start_time));
            $end_time = date('h:i:s A', strtotime($request->end_time));



            $startTime = Carbon::parse($start_time);
            $endTime = Carbon::parse($end_time);

            $totalDuration =  $endTime->diff($startTime)->format('%h:%i:%s');
            $tempData = explode(':',$totalDuration);
            $hour = '';
            $min = '';
            $sec = '';
            if(intval($tempData[0])<=9)
            {
                $hour = '0'.$tempData[0];
            }
            else {
                $hour = $tempData[0];
            }
            if(intval($tempData[1])<=9)
            {
                $min = '0'.$tempData[1];
            }
            else {
                $min = $tempData[1];
            }
            if(intval($tempData[2])<=9)
            {
                $sec = '0'.$tempData[2];
            }
            else {
                $sec = $tempData[2];
            }
            $totalDuration = $hour.':'.$min.':'.$sec;
            $d = explode(':', $totalDuration);
            $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];
            $total_time_seconds = $startTime->diffInSeconds($endTime);




            $duble_salary=$total_time_seconds;
            $total_seconds = $duble_salary - 28800;
            if(intval($total_seconds)>0)
            {
                $add_overtime_after_approve = $duble_salary - $total_seconds;
            }
            else {
                $add_overtime_after_approve = $duble_salary;
            }
            $after = gmdate("h:i:s", $duble_salary);
            $overtime = gmdate("h:i:s", $total_seconds);
            $In_time_update = new Attendence();

            $check_atten_one_time = Attendence::where('user_id', $request->user)->where('date', $request->date)->first();
            if ($check_atten_one_time == null) {
                $cnt = HolidayPay::where('date',$request->date)->where('user_id',$request->user)->count();
                if($cnt==0)
                {
                    $test_hol = new HolidayPay();
                    $test_hol->user_id = $request->user;
                    $test_hol->date = $request->date;
                    $test_hol->total_time = $total_time_seconds;
                    $test_hol->status = "0";
                    $test_hol->save();
                }



                $In_time_update->user_id = $request->user;
                $In_time_update->start_time = $start_time;
                $In_time_update->end_time = $end_time;
                $In_time_update->date = $request->date;
                $In_time_update->work_time = $after;
                $In_time_update->overtime = '00:00:00';
                $In_time_update->total_hours = $total_time_seconds;
                $In_time_update->work_and_overtime = $add_overtime_after_approve;
                $In_time_update->status = 0;
                $In_time_update->save();

                return redirect()->back()->with('message', 'Your attendance successfully!');
            } else {
                return redirect()->back()->with('error', 'Your attendance Already Done!');

            }
        }


        else{

            $start_time = date('h:i:s A', strtotime($request->start_time));
            $end_time = date('h:i:s A', strtotime($request->end_time));

            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
            $totalDuration =  $startTime->diff($endTime)->format('%h:%i:%s');
            $tempData = explode(':',$totalDuration);
            $hour = '';
            $min = '';
            $sec = '';
            if(intval($tempData[0])<=9)
            {
                $hour = '0'.$tempData[0];
            }
            else {
                $hour = $tempData[0];
            }
            if(intval($tempData[1])<=9)
            {
                $min = '0'.$tempData[1];
            }
            else {
                $min = $tempData[1];
            }
            if(intval($tempData[2])<=9)
            {
                $sec = '0'.$tempData[2];
            }
            else {
                $sec = $tempData[2];
            }
            $totalDuration = $hour.':'.$min.':'.$sec;
            // dd($totalDuration);
            $d = explode(':', $totalDuration);
            $simplework = ($d[0] * 3600) + ($d[1] * 60) + $d[2];
            $total_time_seconds = Carbon::parse($request->start_time)->diffInSeconds($endTime);


            if($total_time_seconds>28800)
            {
                $total_seconds = $total_time_seconds - 28800;
                $add_overtime_after_approve = $total_time_seconds - $total_seconds;
                $after = gmdate("h:i:s", $add_overtime_after_approve);
                $overtime = gmdate("h:i:s", $total_seconds);
            }
            else {
                $after = gmdate("h:i:s", $total_time_seconds);
                $overtime = "00:00:00";
                $add_overtime_after_approve = $total_time_seconds;
            }
            $In_time_update = new Attendence();

            $check_atten_one_time = Attendence::where('user_id', $request->user)->where('date', $request->date)->first();
            if ($check_atten_one_time == null) {
                if ($total_time_seconds >= 28800) {
                    $In_time_update->user_id = $request->user;
                    $In_time_update->start_time = $start_time;
                    $In_time_update->end_time = $end_time;
                    $In_time_update->date = $request->date;
                    $In_time_update->work_time = $after;
                    $In_time_update->overtime = $overtime;
                    $In_time_update->total_hours = $total_time_seconds;
                    $In_time_update->work_and_overtime = $add_overtime_after_approve;
                    $In_time_update->status = 0;
                    $In_time_update->save();
                } else {
                    $In_time_update->user_id = $request->user;
                    $In_time_update->start_time = $start_time;
                    $In_time_update->end_time = $end_time;
                    $In_time_update->date = $request->date;
                    $In_time_update->work_time = $totalDuration;
                    $In_time_update->overtime = '00:00:00';
                    $In_time_update->total_hours = $total_time_seconds;
                    $In_time_update->work_and_overtime = $simplework;
                    $In_time_update->status = 0;
                    $In_time_update->save();
                }
                return redirect()->back()->with('message', 'Your attendance successfully!');
            } else {
                return redirect()->back()->with('error', 'Your attendance Already Done!');
            }
        }

    }

    public function Roledelete($id)
    {
        $data=Role::find($id);
        $data->delete();
        return redirect()->back()->with('error', 'Role Delete Successfully!');

    }

    public function attendance_search(Request $request)
    {
        $temp_attn = "1";
        $temp_table = "0";
        $employees = User::select('first_name','last_name','id')->get()->toArray();
        $auth_role = Auth::user()->user_role;
        $atten_emp = array();
        $d = '';
        $flag = false;
        $attn_dep = '';
        $temp_s_d = $request->start_date;
        $temp_e_d = $request->end_date;
        $user_arr = array();
        $dep = DB::table('departments')->get();
        $flag2 = 0;
        $permision = Permission::where(['user_id' => auth::user()->id, 'module' => 'attendance'])->count();
        if ($permision == 0) {
            return back()->with('error', 'This Feature is restricted For You !');
        } else {
            $Employepermision = Permission::where(['user_id' => auth::user()->id, 'module' => 'attendance'])->first()->toarray();

            if ($request->start_date && $request->end_date && $request->department && $request->employee) {

                $attn_dep = $request->department;
                $res_attn = DB::table('users')->where('users.id',$request->employee)
                    ->join('attendences','users.id','=','attendences.user_id')
                    ->select('users.first_name','users.last_name','attendences.*')
                    ->where('attendences.date','>=',$request->start_date)
                    ->where('attendences.date','<=',$request->end_date)->paginate(50);
                $res_attn->appends($request->all());

                $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
                if(count($dep_per)>0) {
                    $d = explode(',', $dep_per[0]->department_id);
                }
                $dep_arr = array();
                $flag2 = 0;
                $contains = gettype($d);
                if(strcmp($contains,"array")==0)
                {
                    foreach($d as $depr)
                    {
                        $dep = DB::table('departments')->where('department',$depr)->get()->toArray();
                        array_push($dep_arr,$dep);
                    }
                    $flag2 = 1;
                }
                else {
                    $dep = DB::table('departments')->get();
                    $flag2 = 0;
                }

                return view('Admin.attendance_history', $atten_emp)->with(compact('Employepermision','dep_arr','flag2','flag','dep','temp_attn','attn_dep','temp_s_d','temp_e_d','res_attn','temp_table','employees'));
            }
            else if ($request->start_date && $request->end_date && ($request->department)=="-99") {

                $contains = Str::contains($auth_role, 'Supervisor');
                if($contains)
                {
                    $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
                    $d = explode(',', $dep_per[0]->department_id);
                    $tempArr = array();
                    foreach($d as $dep)
                    {
                        $dep_name = Department::where('department',$dep)->first();
                        array_push($tempArr,$dep_name->id);
                    }
                    $res_attn = DB::table('users')->where('department',$tempArr)
                        ->join('attendences','users.id','=','attendences.user_id')
                        ->select('users.first_name','users.last_name','attendences.*')
                        ->where('attendences.date','>=',$request->start_date)->where('attendences.date','<=',$request->end_date)->paginate(50);
                    $res_attn->appends($request->all());
                    $flag = false;
                }
                else {

                    $res_attn = DB::table('users')->join('attendences','users.id','=','attendences.user_id')
                        ->select('users.first_name','users.last_name','attendences.*')
                        ->where('attendences.date','>=',$request->start_date)
                        ->where('attendences.date','<=',$request->end_date)->paginate(50);
                    $res_attn->appends($request->all());
                    $flag = false;
                }
                $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
                if(count($dep_per)>0)
                {
                    $d = explode(',', $dep_per[0]->department_id);
                }
                $dep_arr = array();
                $flag2 = 0;
                $contains = gettype($d);
                if(strcmp($contains,"array")==0)
                {
                    foreach($d as $depr)
                    {
                        $dep = DB::table('departments')->where('department',$depr)->get()->toArray();
                        array_push($dep_arr,$dep);
                    }
                    $flag2 = 1;
                }
                else {
                    $dep = DB::table('departments')->get();
                    $flag2 = 0;
                }

                return view('Admin.attendance_history', $atten_emp)->with(compact('Employepermision','dep_arr','flag2','flag','dep','temp_attn','attn_dep','temp_s_d','temp_e_d','res_attn','temp_table','employees'));
            }
            else if ($request->start_date && $request->end_date && $request->department)
            {
                $contains = Str::contains($auth_role, 'Supervisor');
                $data = array();
                $attn_dep = $request->department;
                $attn_data = array();
                if($contains)
                {
                    $res_attn = DB::table('users')
                        ->join('attendences','users.id','=','attendences.user_id')
                        ->select('users.first_name','users.last_name','attendences.*')
                        ->where('users.department','=',$request->department)
                        ->where('attendences.date','>=',$request->start_date)
                        ->where('attendences.date','<=',$request->end_date)->paginate(50);
                    $flag = false;
                    $res_attn->appends($request->all());
                }
                else {
                    $res_attn = DB::table('users')
                        ->join('attendences','users.id','=','attendences.user_id')
                        ->select('users.first_name','users.last_name','attendences.*')
                        ->where('users.department','=',$request->department)
                        ->where('attendences.date','>=',$request->start_date)
                        ->where('attendences.date','<=',$request->end_date)->paginate(50);
                    $flag = false;
                    $res_attn->appends($request->all());
                }



                $dep_per = Deppermissinons::where('user_id',Auth::user()->id)->get();
                if(count($dep_per)>0) {
                    $d = explode(',', $dep_per[0]->department_id);
                    $dep_arr = array();
                    $contains = gettype($d);
                    if(strcmp($contains,"array")==0)
                    {
                        foreach($d as $depr)
                        {
                            $dep = DB::table('departments')->where('department',$depr)->get()->toArray();
                            array_push($dep_arr,$dep);
                        }
                        $flag2 = 1;
                    }
                }
                else {
                    $dep_arr = '';
                    $dep = DB::table('departments')->get();
                    $flag2 = 0;
                }
                return view('Admin.attendance_history')->with(compact('Employepermision','dep_arr','flag','dep','attn_data','atten_emp','flag2','user_arr','temp_attn','attn_dep','temp_s_d','temp_e_d','res_attn','temp_table','employees'));
            }
            else if ($request->start_date && $request->end_date) {
                $res_attn = DB::table('users')->join('attendences','users.id','=','attendences.user_id')->select('users.first_name','users.last_name','attendences.*')->paginate(50);
                $res_attn->appends($request->all());
                return view('Admin.attendance_history', $atten_emp)->with(compact('Employepermision','flag2','dep','temp_attn','attn_dep','temp_s_d','temp_e_d','res_attn','temp_table','employees'));
            }
        }


    }
}
