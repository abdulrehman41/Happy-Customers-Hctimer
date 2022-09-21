<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Notice;
use App\Models\Holiday;
use App\Models\HolidayPay;
use App\Models\Proceed;
use App\Models\Attendence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Null_;

class EmployeeController extends Controller
{
    public function dashboard()
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
            $val3 = date('h:i:s');
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

        return view('Employee.dashboard', $user_atten);
    }


    public function Noticesshow()
    {
        return view('Employee.notices');
    }
    public function Noticesdetail($id)
    {
        $data = Notice::where('id', $id)->first();

        return view('Employee.noticedetail', get_defined_vars());
    }


    public function attendance_history()
    {
        $user_id = Auth::user()->id;
        $atten_emp['emp_atten'] = Attendence::where('user_id', $user_id)->orderBy('date', 'DESC')->get();
        return view('Employee.attendance_history', $atten_emp);
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
       
        
    
        $duble_salary=$total_time_seconds;
        $total_seconds = $duble_salary - 28800;
      
        $add_overtime_after_approve = $total_time_seconds - $total_seconds;
        $after = gmdate("h:i:s A", $add_overtime_after_approve);
        //dd($after,$total_time_seconds,$add_overtime_after_approve);

        $overtime = gmdate("h:i:s A", $total_seconds);
        // dd($total_hours,$total_minutes,$total_seconds);
        
        $holiday=Holiday::where('holiday_date',$c_date)->count();  
        if (intval($holiday)==1) {
            
            $check_atten_one_time = Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
            if (isset($check_atten_one_time)) {
                
                $test_hol = new HolidayPay();
                    $test_hol->user_id = $user_id;
                    $test_hol->date = $c_date;
                    $test_hol->total_time = $total_time_seconds;
                    $test_hol->status = "0";
                    $test_hol->save();
                
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
            } 
            else 
            {
                return redirect()->back()->with('error', 'Your attendance Already Done!');
            } 
        }
    }
    public function user_processed_payroll()
    {
        $user_id = Auth::user()->id;
        $user_processed_payroll = Proceed::get()->where("user_id", $user_id);
        return view('Employee.user_processed_payroll', get_defined_vars());
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

            return redirect()->back()->with('success', 'Your attendance successfully!');
        } else {
            return redirect()->back()->with('error', 'Your attendance Already Done!');
        }
    }
}
