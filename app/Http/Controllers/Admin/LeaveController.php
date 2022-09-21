<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Maternity;
use App\Models\SickLeave;
use App\Models\Attendence;
use App\Models\HolidayPay;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\VacationLeave;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Holiday;
use Illuminate\Support\Carbon;

class LeaveController extends Controller
{
    public function sick_leave()
    {
        $view_sick_leave['all_emp'] = User::where('user_role', 'Employee')->select('id', 'first_name','last_name')->get();
        $view_sick_leave['view_sick_leave'] = DB::table('sick_leaves')
            ->leftjoin('users', 'users.id', '=', 'sick_leaves.user_id')
            ->select('users.first_name','users.last_name', 'sick_leaves.*')->orderBy('id', 'DESC')->get();

        return view('Admin.sick_leave', $view_sick_leave);
    }


    public function insert_sick_leave(Request $request)
    {
        // get leave data start
        $title = $request->title;
        $leave_date = $request->leave_date;
        $description = $request->description;
        // get leave data end


        $user_id = $request->user_id;
        $checkRec = SickLeave::where('user_id', $user_id)->where('leave_date', $leave_date)->count();
        $atten_count = Attendence::where('user_id', $user_id)->where('date', $leave_date)->count();
        if($atten_count>0)
        {
            return redirect()->back()->with('error', 'We already have Attendance for this Date!!');
        }
        
        if ($checkRec > 0 && $atten_count > 0) {
            return redirect()->back()->with('error', 'We already have Sick Leave Request applied for this date!');
        }
        $c_year = Carbon::now()->year;
        $insert_sick_leave = new SickLeave();
        $insert_sick_leave->user_id = $user_id;
        $insert_sick_leave->title = $title;
        $insert_sick_leave->leave_date = $leave_date;
        $insert_sick_leave->description = $description;
        $insert_sick_leave->c_year = $c_year;
        $insert_sick_leave->status = 0;
        $insert_sick_leave->save();

        return redirect()->back()->with('message', 'Your Sick leave apply Successfully!');

        return view('Admin.sick_leave');
    }

    public function sick_status_deactive($id)
    {
        $sick_status_deactive = SickLeave::find($id);
        $sick_status_deactive->status = 0;
        $sick_status_deactive->save();

        ///sick leave update emp start
        $update_emp_sick = User::find($sick_status_deactive->user_id);
        if ($update_emp_sick->annual_sick_leave < 10 && $update_emp_sick->annual_sick_leave >= 0) {
            //dd($update_emp_sick->annual_sick_leave);
            $update_emp_sick->annual_sick_leave = $update_emp_sick->annual_sick_leave + 1;
            $update_emp_sick->save();
        }

        // dd($emp_get->annual_sick_leave);
        ///sick leave update emp end
        return redirect()->back()->with('error', 'Sick Leave successfully Deactive! remaning leave -' . $update_emp_sick->annual_sick_leave . '');
    }
    public function sick_status_active($id)
    {
        $sick_status_active = SickLeave::find($id);
        $sick_status_active->status = 1;
        $sick_status_active->save();

        $leaveLeft = User::where('id', $sick_status_active->user_id)->first();
        $leftLeave = intval($leaveLeft->annual_sick_leave);
        $leftLeave--;
        $leftLeave_ = strval($leftLeave);
        $leaveLeft->annual_sick_leave = $leftLeave_;
        $leaveLeft->sick_leave_date = $sick_status_active->leave_date;
        $leaveLeft->save();
        $atten_check = Attendence::where('user_id', $sick_status_active->user_id)->where('date', $sick_status_active->leave_date)->first();
        if ($atten_check == null) {
            
            
            $start_time = date('H:i:s');
            $atten = new Attendence();
            $atten->user_id = $sick_status_active->user_id;
            $atten->start_time = '08:00:00';
            $atten->date = $sick_status_active->leave_date;
            $atten->work_time = '08:00:00';
            $atten->overtime = '00:00:00';
            $atten->end_time = '16:00:00';
            $atten->total_hours = 28800;
            $atten->work_and_overtime = 28800;
            $atten->status = 0;
            $atten->save();
            return redirect()->back()->with('success', 'Sick Leave successfully Approved! remaning leave ' . $leaveLeft->annual_sick_leave . '');
        }
        else {
            return redirect()->back()->with('warning', 'Attendance Already Exist for this Date!!');
        }
        ///attendence inserted end
        
    }
    public function  delete_sick($id)
    {
        $delete_sick = SickLeave::find($id);
        $atten_data = Attendence::where('user_id',$delete_sick["user_id"])->where('date',$delete_sick["leave_date"])->count();
        if($atten_data>0)
        {
            Attendence::where('user_id',$delete_sick["user_id"])->where('date',$delete_sick["leave_date"])->delete();
            $delete_sick->delete();
            return redirect()->back()->with('error', 'Sick Leave successfully Deleted!');
        }
        else {
            $delete_sick->delete();
        return redirect()->back()->with('error', 'No Attendance Found!!');
        }
    }


    ////////////////////////////sick leave function end///////////////////////////
    public function vacation_leave()
    {
        $view_vacation_leave['all_emp'] = User::where('user_role', 'Employee')->select('id', 'first_name','last_name')->get();
        $view_vacation_leave['view_vacation_leave'] = DB::table('vacation_leaves')
            ->leftjoin('users', 'users.id', '=', 'vacation_leaves.user_id')
            ->select('users.first_name','users.last_name', 'vacation_leaves.*')->orderBy('id', 'DESC')->get();

        return view('Admin.vacation_leave', $view_vacation_leave);
    }
    public function insert_vacation_leave(Request $request)
    {
        // get leave data start
        $title = $request->title;
        $leave_date = $request->leave_date;
        $description = $request->description;
        // get leave data end
        
        $user_id = $request->user_id;
        $checkRec = VacationLeave::where('user_id', $user_id)->where('leave_date', $leave_date)->count();

            $atten_count = Attendence::where('user_id', $user_id)->where('date', $leave_date)->count();
                    if($atten_count>0)
                    {
                        return redirect()->back()->with('error', 'We already have Attendance for this Date!!');
                    }


        if ($checkRec > 0) {
            return redirect()->back()->with('error', 'We already have Sick Leave Request applied for this date!');
        }
        $c_year = Carbon::now()->year;
        $insert_sick_leave = new VacationLeave();
        $insert_sick_leave->user_id = $user_id;
        $insert_sick_leave->title = $title;
        $insert_sick_leave->leave_date = $leave_date;
        $insert_sick_leave->description = $description;
        $insert_sick_leave->c_year = $c_year;
        $insert_sick_leave->status = 0;
        $insert_sick_leave->save();

        return redirect()->back()->with('message', 'Your Vacation leave apply Successfully!');

        return view('Admin.vacation_leave');
    }

    public function vacation_status_deactive($id)
    {
        $vacation_status_deactive = VacationLeave::find($id);
        $vacation_status_deactive->status = 0;
        $vacation_status_deactive->save();

        ///sick leave update emp start
        $update_emp_vacation = User::find($vacation_status_deactive->user_id);
        if ($update_emp_vacation->annual_vacation_leave < 10 && $update_emp_vacation->annual_vacation_leave >= 0) {
            //dd($update_emp_sick->annual_sick_leave);
            $update_emp_vacation->annual_vacation_leave = $update_emp_vacation->annual_vacation_leave + $vacation_status_deactive->allow_leave;
            $update_emp_vacation->save();
        }
        ///attendence inserted start
        $atten_check = Attendence::where('user_id', $vacation_status_deactive->user_id)->where('date', $vacation_status_deactive->leave_date_end)->first();
        if ($atten_check == null) {
            $start_time = date('H:i:s');
            $atten = new Attendence();
            $atten->user_id = $vacation_status_deactive->user_id;
            $atten->start_time = '00:00:00';
            $atten->date = $vacation_status_deactive->leave_date_end;
            $atten->work_time = '08:00:00';
            $atten->overtime = '00:00:00';
            $atten->end_time = '16:00:00';
            $atten->total_hours = 0;
            $atten->work_and_overtime = 0;
            $atten->status = 0;
            $atten->save();
        }
        ///attendence inserted end
        // dd($emp_get->annual_sick_leave);
        ///sick leave update emp end
        return redirect()->back()->with('error', 'Sick Leave successfully Deactive! remaning leave :' . $update_emp_vacation->annual_vacation_leave . ' Days');
    }
    public function vacation_status_active($id)
    {
        $vacation_status_active = VacationLeave::find($id);
        $vacation_status_active->status = 1;
        $vacation_status_active->save();

        $leaveLeft = User::where('id', $vacation_status_active->user_id)->first();
        $leftLeave = intval($leaveLeft->annual_vacation_leave);
        $leftLeave--;
        $leftLeave_ = strval($leftLeave);
        $leaveLeft->annual_vacation_leave = $leftLeave_;
        $leaveLeft->v_leave_date = $vacation_status_active->leave_date;
        $leaveLeft->save();
        $atten_check = Attendence::where('user_id', $vacation_status_active->user_id)->where('date', $vacation_status_active->leave_date)->first();
        if ($atten_check == null) {
            
            $start_time = date('H:i:s');
            $atten = new Attendence();
            $atten->user_id = $vacation_status_active->user_id;
            $atten->start_time = '08:00:00';
            $atten->date = $vacation_status_active->leave_date;
            $atten->work_time = '08:00:00';
            $atten->overtime = '00:00:00';
            $atten->end_time = '16:00:00';
            $atten->total_hours = 28800;
            $atten->work_and_overtime = 28800;
            $atten->status = 0;
            $atten->save();
            return redirect()->back()->with('success', 'Vacation Leave successfully Approved! remaning leave :' . $leaveLeft->annual_vacation_leave);
        }
        else {
            return redirect()->back()->with('success', 'Attendance Already Exist for this Date'); 
        }
        
    }
    public function  delete_vacation($id)
    {
        $delete_vacation = VacationLeave::find($id);
        
        $atten_data = Attendence::where('user_id',$delete_vacation["user_id"])->where('date',$delete_vacation["leave_date"])->count();
        if($atten_data>0)
        {
            Attendence::where('user_id',$delete_vacation["user_id"])->where('date',$delete_vacation["leave_date"])->delete();
            $delete_vacation->delete();
            return redirect()->back()->with('error', 'Vacation Leave successfully Deleted!');
        }
        else {
            $delete_vacation->delete();
            return redirect()->back()->with('error', 'No Attendance Found!!');
        }
    }
    ////////////////////////////vacation leave function end///////////////////////////

    public function holidays()
    {
        $holiday_date = Holiday::get();
        return view('Admin.holidays', get_defined_vars());
    }
    public function add_holiday()
    {
        return view('Admin.add_holiday');
    }
    public function add_holiday_values(Request $request)
    {
        $title = $request->title;
        $date = $request->date_;

        $holiday = new Holiday();
        $holiday->holiday_title = $title;
        $holiday->holiday_date = $date;
        $holiday->save();

        return redirect('admin/holidays');
    }
    public function delete_holiday($id)
    {
        $data = Holiday::find($id);
        $data->delete();
        return redirect()->back();
    }
    public function update_holiday($id)
    {
        $holiday_id = Holiday::find($id);
        return view('Admin.update_holiday', get_defined_vars());
    }
    public function update_holiday_values(Request $request, $id)
    {

        $h_title = $request->title;
        $h_date = $request->date;
        $data = Holiday::find($id);
        $data->holiday_title = $h_title;
        $data->holiday_date = $h_date;
        $data->save();
        return redirect('admin/holidays');
    }
    public function MaternityLeave()
    {
        $maternity = Maternity::get();
        $all_emp = User::where('user_role', 'Employee')->get();
        return view('Admin.maternity', get_defined_vars());
    }

    public function InsertMaternity(Request $request)
    {
        $name = $request->user_name;
        $title = $request->title;
        $leave_date = $request->leave_date;
        $no_weak = $request->no_weak;
        $desc = $request->description;
        $temp = explode(" ",$name);
        
        $maternity = new Maternity();
        $maternity->user_id = $temp[0]; 
        $maternity->name = $temp[1];
        $maternity->title = $title;
        $maternity->start_date = $leave_date;
        $maternity->no_weak = $no_weak;
        $maternity->desc = $desc;
        $maternity->status = 0;
        $maternity->save();

        return redirect('admin/maternity')->with('message', 'Maternity Leave Added Successfully!');
    }
    public function ApproveMaternity($id)
    {
        
        $maternity = Maternity::where('user_id',$id)->get()->toArray();
        

        $start_date = $maternity[0]['start_date'];
        $no_weak = intval($maternity[0]['no_weak']);
        $s_date = strtotime($start_date);

        if($no_weak>8)
        {
            $no_weak = 8;
        }
        $no_days = $no_weak * 6;
        
        for ($i = 0; $i < $no_days; $i++) {
            $s_date = strtotime($start_date);
            $s_date = date('w', $s_date);
            $weak_day = intval($s_date);
            if ($weak_day <= 5) {
                $atten = new Attendence();
                $atten->total_hours = 28800;
                $atten->work_and_overtime = 28800;
                $atten->user_id = $id;
                $atten->start_time = '08:00:00';
                $atten->end_time = '16:00:00';
                $atten->date = $start_date;
                $atten->work_time = '08:00:00';
                $atten->overtime = '00:00:00';
                $atten->status = '1';
                $atten->save();
                $date = strtotime($start_date);
                $date = strtotime("+1 day", $date);
                $date = date('Y/m/d', $date);
                $start_date = Str::replace('/', '-', $date);
            } else if ($weak_day == 0) {
                $date = strtotime($start_date);
                $date = strtotime("+1 day", $date);
                $date = date('Y/m/d', $date);
                $start_date = Str::replace('/', '-', $date);
            } else if ($weak_day == 6) {
                $date = strtotime($start_date);
                $date = strtotime("+2 day", $date);
                $date = date('Y/m/d', $date);
                $start_date = Str::replace('/', '-', $date);
            }
        }
        
        $date = strtotime($start_date);
        $date = strtotime("-1 day", $date);
        $date = date('Y/m/d', $date);
        $start_date = Str::replace('/', '-', $date);
        DB::table('maternities')
              ->where('user_id', $id)
              ->update(['status' => 1,'end_date' => $start_date]);
        
        return redirect('admin/maternity')->with('message', 'Maternity Leave Approve Successfully!');
    }
    public function DeleteMaternity($id)
    {
        $maternity = Maternity::find($id);
        $maternity->delete();
        return redirect('admin/maternity')->with('message', 'Maternity Deleted Successfully!');
    }
}
