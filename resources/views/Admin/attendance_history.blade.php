@extends('layouts.admin')
@section('content')

    <?php
    use Carbon\Carbon;
    use App\Models\Attendence;
    use Illuminate\Support\Str;

    ?>
    @php
        $user = DB::table('users')->select('id','first_name','last_name','user_role')->get();
    @endphp


    <section id="basic-datatable">

        <form  action="{{route('admin.attendance_search')}}" method="get" style="background-color: white;">
            <input type="hidden" value={{$attn_dep}} class="att_hidden" />
            <input type="hidden" value={{$temp_attn}} class="temp_inp" />
            @if($Employepermision['full_access']==1 )

                <div class="row justify-content-center">

                    <div class="col-12 col-md-3 mt-2 " >
                        <div class="form-group">
                            <div class="controls">

                                <input type="text" id="date-input" class="form-control a_h_s_d"  placeholder="Start date"
                                       onfocus="(this.type='date')"
                                       name="start_date" placeholder="start date"  value="{{ $temp_s_d }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mt-2" >
                        <div class="form-group">
                            <div class="controls">
                                <input class="form-control a_h_e_d"    placeholder="End date"
                                       onfocus="(this.type='date')"   type="text" id="range" name="end_date" placeholder="select date range" value={{$temp_e_d}} required  >
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-3 mt-2 " >
                        <div class="form-group">
                            <div class="controls">
                                @if($flag2==1)
                                    <select class="form-control a_h_d select" name="department" placeholder="Department" id="attendance-department-dropdown" >
                                        <option value="-99" >Select Department</option>

                                        @foreach ($dep_arr as $list)
                                            <option value="{{ $list[0]->id }}" >
                                                {{ $list[0]->department }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-control a_h_d select select2" name="department" placeholder="Department" id="attendance-department-dropdown">
                                        <option value="-99" >select Department</option>
                                        @foreach ($dep as $list)
                                            <option value="{{ $list->id }}" >
                                                {{ $list->department }}</option>
                                        @endforeach
                                    </select>
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-3 mt-2 " >
                        <div class="form-group">
                            <div class="controls">
                                <select class="select2 form-control select" name="employee" placeholder="Employee" id="attendance_user_dropdown">
                                    <option value="" >Select Employee</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-2 mt-2" >
                        <div class="form-group">

                            <div class="form-group  ">
                                <button type="submit" class="btn  btn-primary "><i class="fa fa-search"> </i> Search</button>
                            </div>


                        </div>
                    </div>
                </div>



        </form>
        @endif
        <div class="form-group d-flex">
            <a href="{{ url('admin/add-attendace') }}" class="btn btn-info" style="margin-right:20px"  >Single Attendance</a>
            <a href="{{ url('admin/add-multi-attendace') }}" class="btn btn-info"  >Multi Attendance</a>
        </div>
        </div>
        <div class="row">

            <div class="col-12">

                <div class="card" style="box-shadow: none;">


                    <div class="card-content">
                        <div class="card-body card-dashboard" style="mt--5">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#id</th>
                                    <th>Employee Name</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Work Time</th>
                                    <th>Over Time</th>
                                    <th>Total Time</th>
                                    @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )


                                        <th>Update By</th>
                                        <th>Action</th>
                                    @endif

                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $i=1;
                                    $j = 0;
                                    $user_name=Auth::user()->first_name." ".Auth::user()->last_name;
                                @endphp
                                @if($flag)
                                    @foreach($attn_data as $data)

                                        @foreach($data as $d)
                                            @php
                                                $Hours = intVal($d['total_hours'])/3600;
                                                $tempH = intVal($Hours)*3600;
                                                $tempMin = intVal($d['total_hours']) - $tempH;
                                                $Min = intVal($tempMin)/60;

                                            @endphp
                                            <tr>
                                                <td>{{$i++}}</td>
                                                @php $name_ = DB::table('users')->select('first_name','last_name')->where('id',$d["id"])->first();
                                                @endphp

                                                @if($name_!=null)
                                                    <td>{{$name_->first_name}}{{" "}}{{$name_->last_name}}</td>
                                                @else
                                                    <td></td>
                                                @endif
                                                <td>{{$d["date"]}} - {{$d["start_time"]}}</td>
                                                @if($d["end_time"]==0)

                                                    <td>00:00:00</td>

                                                @else
                                                    <td>{{$d["date"]}} - {{$d["end_time"]}}</td>

                                                @endif
                                                <td>{{ $d["work_time"]}}</td>
                                                <td>{{$d["overtime"]}}</td>
                                                @if($d["status"] == 0)
                                                    <td>{{$d["work_time"]}}</td>
                                                @else
                                                    <td>{{ intVal($Hours).":".intVal($Min)}}</td>
                                                @endif
                                                @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                                                    <td>{{$d["update_by"]}}</td>
                                                    @if($d["status"] == 0)
                                                        <td>
                                                            <button class="btn btn-success" id={{$d['id']}} style="color:white;" onClick="ApproveAttn(this.id)">Approve</button>

                                                            <a href="{{url('admin/update_attendance')}}/{{$d['id']}}" class="text-primary mr-2"><i class="feather icon-edit" title="Edit"></i></a>

                                                            <a href="{{url('admin/delete_attendance')}}/{{$d['id']}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <button class="btn btn-success" id={{$d['id']}} style="color:white;" onClick="DisApproveAttn(this.id)">Disapprove</button>

                                                            <a href="{{url('admin/update_attendance')}}/{{$d['id']}}" class="text-primary mr-2"><i class="feather icon-edit" title="Edit"></i></a>

                                                            <a href="{{url('admin/delete_attendance')}}/{{$d['id']}}/{{$d['date']}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>


                                                        </td>
                                                    @endif
                                                @endif


                                            </tr>
                                        @endforeach
                                    @endforeach



                                @else
                                    @php
                                        $j = 0;
                                        $N = '';
                                    @endphp
                                    @foreach($res_attn as $l)
                                        @php
                                            $Hours = intVal($l->total_hours)/3600;
                                            $tempH = intVal($Hours)*3600;
                                            $tempMin = intVal($l->total_hours) - $tempH;
                                            $Min = intVal($tempMin)/60;
                                        @endphp
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$l->first_name}}{{" "}}{{$l->last_name}}</td>


                                            <td>{{$l->date}} - {{$l->start_time}}</td>
                                            @if($l->end_time==0)

                                                <td>00:00:00</td>

                                            @else
                                                <td>{{$l->date}} - {{$l->end_time}}</td>

                                            @endif
                                            <td>{{ $l->work_time}}</td>
                                            <td>{{$l->overtime}}</td>
                                            @if($l->status == 0)
                                                <td id={{"total".$l->id }}>{{$l->work_time}}</td>
                                            @else
                                                <td id={{"total".$l->id}}>{{ intVal($Hours).":".intVal($Min)}}</td>
                                            @endif
                                            @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                                                <td id={{"update".$l->id}} >{{$l->update_by}}</td>
                                                @if($l->status == 0)
                                                    <td><button class="btn btn-success" id={{$l->id}} style="color:white;" onClick="ApproveAttn(this.id)">Approve</button>

                                                        <a href="{{url('admin/update_attendance')}}/{{$l->id}}" class="text-primary mr-2"><i class="feather icon-edit" title="Edit"></i></a>

                                                        <a href="{{url('admin/delete_attendance')}}/{{$l->id}}/{{$l->date}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>



                                                    </td>
                                                @else
                                                    <td>
                                                        <button class="btn btn-success" id={{$l->id}} style="color:white;" onClick="DisApproveAttn(this.id)">Disapprove</button>

                                                        <a href="{{url('admin/update_attendance')}}/{{$l->id}}" class="text-primary mr-2"><i class="feather icon-edit" title="Edit"></i></a>

                                                        <a href="{{url('admin/delete_attendance')}}/{{$l->id}}/{{$l->date}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>


                                                    </td>
                                                @endif
                                            @endif

                                        </tr>

                                    @endforeach
                                @endif
                                </tbody>

                            </table>
                            @if($temp_table==0)
                                {{ $res_attn->onEachSide(5)->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="addattendance" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Attendance</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('admin.add.admin_attendance')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="first-name-icon">Select Date</label>
                                    <div class="position-relative has-icon-left">
                                        <input required type="date" name="date" class="form-control  @error('department_name') is-invalid @enderror"  >
                                        @error('department_name')
                                        <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                  </span>
                                        @enderror
                                    </div>


                                    <label for="first-name-icon">start Time</label>
                                    <div class="position-relative has-icon-left">
                                        <input required type="time" name="start_time" class="form-control  @error('department_name') is-invalid @enderror"  >
                                        @error('department_name')
                                        <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                  </span>
                                        @enderror
                                    </div>



                                    <label for="first-name-icon">End Time</label>
                                    <div class="position-relative has-icon-left">
                                        <input required type="time" name="end_time" class="form-control  @error('department_name') is-invalid @enderror"  >
                                        @error('department_name')
                                        <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                  </span>
                                        @enderror
                                    </div>
                                    <label for="first-name-icon">Users</label>
                                    <div class="position-relative has-icon-left">
                                        <select class="select2 form-control" name="user"  required>
                                            <option value="" >select user</option>
                                            @foreach ($user as $list)
                                                <option value="{{ $list->id }}" >
                                                    {{ $list->first_name }} {{ $list->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit"  class="btn btn-primary">Add Attendance</button>
                        </div>
                        </form>

                    </div>

                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

                <script>

                    if(localStorage.getItem("a_h_s_d") === null && localStorage.getItem("a_h_e_d") === null && localStorage.getItem("a_h_d")=== null)
                    {
                        localStorage.setItem('a_h_s_d', "");
                        localStorage.setItem('a_h_e_d', "");
                        localStorage.setItem('a_h_d', "");

                    }
                    else {
                        if(localStorage.getItem("a_h_s_d") === "" && localStorage.getItem("a_h_e_d") === "" && localStorage.getItem("a_h_d")=== "")
                        {
                            var t_c = $(".att_hidden").val();
                            console.log(t_c);
                            if(t_c=="/")
                            {
                                t_c = "";
                            }
                            else if(t_c==undefined)
                            {
                                t_c = "";
                            }
                            localStorage.setItem("a_h_s_d",$(".a_h_s_d").val());
                            localStorage.setItem("a_h_e_d",$(".a_h_e_d").val());
                            localStorage.setItem("a_h_d",t_c);
                            $(".a_h_s_d").val(localStorage.getItem("a_h_s_d"));
                            $(".a_h_e_d").val(localStorage.getItem("a_h_e_d"));
                            var d_p_c = localStorage.getItem("a_h_d");
                            $(`.a_h_d option[value=${d_p_c}]`).attr("selected", "selected");
                        }
                        else {
                            var temp_c = $(".temp_inp").val();
                            if(temp_c==="0")
                            {
                                $(".a_h_s_d").val(localStorage.getItem("a_h_s_d"));
                                $(".a_h_e_d").val(localStorage.getItem("a_h_e_d"));
                                var d_p_c = localStorage.getItem("a_h_d");
                                $(`.a_h_d option[value=${d_p_c}]`).attr("selected", "selected");
                                $(".temp_inp").val("0");
                            }
                            else {
                                var t_c = $(".att_hidden").val();
                                localStorage.setItem("a_h_s_d",$(".a_h_s_d").val());
                                localStorage.setItem("a_h_e_d",$(".a_h_e_d").val());
                                localStorage.setItem("a_h_d",t_c);
                                $(".a_h_s_d").val(localStorage.getItem("a_h_s_d"));
                                $(".a_h_e_d").val(localStorage.getItem("a_h_e_d"));
                                var d_p_c = localStorage.getItem("a_h_d");
                                $(`.a_h_d option[value=${d_p_c}]`).attr("selected", "selected");
                                $(".temp_inp").val("0");
                            }
                        }


                    }


                    function ApproveAttn(e){
                        var url = '{{ route("admin.attent_status_approve", ":id") }}'
                        url = url.replace(":id", e);
                        $.ajax({
                            type:"get",
                            url:url,
                            data:{
                                "user_id":e
                            },
                            success: function (resutl) {
                                if(resutl.val===1)
                                {
                                    $('#'+e).html("Disapprove");
                                    $('#update'+e).html(resutl.name+" "+resutl.dataTime);
                                    $('#'+e).attr("onClick","DisApproveAttn(this.id)");
                                    var temp_overtime = resutl.overtime;
                                    var temp_worktime = resutl.work_time;
                                    var split_temp_overtime = temp_overtime.split(":");
                                    var split_temp_worktime = temp_worktime.split(":");
                                    var hours_overtime = parseInt(split_temp_worktime[0])+ parseInt(split_temp_overtime[0]);
                                    var min_total_time = parseInt(split_temp_worktime[1])+parseInt(split_temp_overtime[1]);
                                    var sec_total_time = parseInt(split_temp_worktime[2])+parseInt(split_temp_overtime[2]);
                                    var temp_hours;
                                    if(hours_overtime<=9)
                                    {
                                        temp_hours = "0"+hours_overtime;
                                    }
                                    else{
                                        temp_hours = hours_overtime;
                                    }
                                    var temp_mins;
                                    if(min_total_time<=9)
                                    {
                                        temp_mins = "0"+min_total_time;
                                    }
                                    else {
                                        temp_mins = min_total_time;
                                    }
                                    var temp_secs;
                                    if(sec_total_time<=9)
                                    {
                                        temp_secs = "0"+sec_total_time;
                                    }
                                    else {
                                        temp_secs = sec_total_time;
                                    }
                                    temp_hours = temp_hours+":"+temp_mins+":"+temp_secs;
                                    $('#total'+e).html(temp_hours);
                                    toastr.success("Approved Successfully!!");
                                }
                                else {
                                    toastr.error("Error in Updating!!");
                                }
                            }
                        });
                    }
                    function DisApproveAttn(e){
                        var url = '{{ route("admin.attent_status_disapprove", ":id") }}'
                        url = url.replace(":id", e);
                        $.ajax({
                            type:"get",
                            url:url,
                            data:{
                                "user_id":e
                            },
                            success: function (resutl) {
                                console.log(resutl.val);
                                if(resutl.val===1)
                                {
                                    $('#'+e).html("Approve");
                                    $('#update'+e).html(resutl.name+" "+resutl.dataTime);
                                    $('#'+e).attr("onClick","ApproveAttn(this.id)");
                                    $('#total'+e).html(resutl.work_time);
                                    toastr.success("Disapproved Successfully!!");
                                }
                                else {
                                    toastr.error("Error in Updating!!");
                                }
                            }
                        });
                    }

                </script>
                <script>

                    $("#filter_attendance").click(function(){
                        $.ajax({
                            url:"{{url('admin/filter_attendance')}}",
                            type:"get",
                            data:{
                                "user_id":id,"start_date":s_d,"end_date":e_d,"nis":nis,
                                "nht":nht, 'edtax':edtax,'netpay':netpay,'income_save':income_save
                            },
                            success: function (resutl) {
                                if(resutl==0) {
                                    toastr.success("Payroll Added");
                                }
                                else {
                                    toastr.error("Payroll Already Exist");
                                }
                            }
                        });
                    });
                    });
                </script>
    </section>
@endsection