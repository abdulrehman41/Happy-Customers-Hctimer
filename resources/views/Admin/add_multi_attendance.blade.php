@extends('layouts.admin')
@section('content')
    @php
        $dep_arr = array();
        $flag = 0;
        $contains = gettype($d);
        if(strcmp($contains,"array")==0)
        {
            foreach($d as $depr)
            {
                $dep = DB::table('departments')->where('department',$depr)->get()->toArray();
            array_push($dep_arr,$dep);
            }
            $flag = 1;
        }
        else {
            $dep = DB::table('departments')->get();
            $flag = 0;
        }
        $s_time = date('H:i:s A',strtotime('09:00:00 AM'));
        $e_time = date('H:i:s A',strtotime('05:00:00 PM'));
    @endphp
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Multiple Attendance</h5>
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <form action="{{route('admin.add_multi.admin_attendance')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="position-relative  has-icon-left">
                                <input required  style="padding:0;"    onfocus="(this.type='date')"  placeholder="Start Date" type="text" name="s_date" class="form-control  "  >
                            </div>
                        </div>
                </div>

                <div class="position-relative  col-md-3 has-icon-left">
                    <input required  style="padding:0;"    onfocus="(this.type='date')"  placeholder="End Date" type="text" name="e_date" class="form-control  "  >
                </div>
                <div class="position-relative has-icon-left col-md-3">
                    <input required placeholder="Start time" type="time" name="start_time" value={{$s_time}} step="1" class="form-control "  >
                </div>



                <div class="position-relative  col-md-3 has-icon-left">
                    <input required  placeholder="End time" type="time" name="end_time" value={{$e_time}} step="1" class="form-control  @error('department_name') is-invalid @enderror"  >
                </div>
                <div class="position-relative   col-md-3 has-icon-left">
                    @if($flag==1)
                        <select class="form-control select2" name="department" placeholder="Department" id="department-dropdown" >
                            <option value="" >select Department</option>
                            @foreach ($dep_arr as $list)
                                <option value="{{ $list[0]->id }}" >
                                    {{ $list[0]->department }}</option>
                            @endforeach
                        </select>
                    @else
                        <select class="form-control select2" name="department" id="department-dropdown" placeholder="Department" >
                            <option value="" >select Department</option>
                            @foreach ($dep as $list)
                                <option value="{{ $list->id }}" >
                                    {{ $list->department }}</option>
                            @endforeach
                        </select>
                    @endif

                </div>
                <div class="position-relative  col-md-3  has-icon-left">
                    <select class="select2 form-control" name="user" id="user_dropdown"  required>

                        <option value="" >select user</option>
                    </select>
                </div>
                <br>
                <button type="submit"  class="btn btn-primary " style="height: 40px;display:block;">Add Attendance</button>

            </div>
        </div>

        </form>

    </div>

    </div>
    </div>

@endsection