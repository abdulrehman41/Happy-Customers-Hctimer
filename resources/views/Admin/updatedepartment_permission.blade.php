@extends('layouts.admin')
@section('content')

<?php
use Carbon\Carbon;
use App\Models\Attendence;


?>


@php
$dep = DB::table('departments')->get();
$user = DB::table('users')->select('id','first_name','last_name','user_role')->where('user_role','Supervisor')->get();
@endphp
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">

          <a class="btn btn-primary" href="{{url('admin/department/permission',$edit->user->id)}}"> <i class="feather icon-reverse"></i> back</a></h5>
        
        <p class="card-text">
          <div class="row justify-content-center">
   <div class="col-md-2">

            <form action="{{url('admin/update-department-neew',$edit->user->id)}}" method="get">
              
            </div>


                <div class="position-relative   col-md-4 has-icon-left">
             <select class="select2 form-control "   name="user_id"   required>
        @foreach ($user as $list)
            <option value="{{ $list->id }}" @if($list->id==$edit->user->id) selected @endif >
                 {{ $list->first_name }}</option>
        @endforeach
    </select>
                </div>

                <div class="position-relative   col-md-4 has-icon-left">
             <select class="multipledepartment form-control "   name="department_name[]" multiple="multiple"   required>
      @foreach ($dep as $department)
             <option value="{{$department->department}}" 
               @foreach (explode(',',$edit->department_id) as $value)
                 @if ($value == $department->department)
                 {{'selected="selected"'}}
                 @endif 
               @endforeach >
              {{ $department->department }} </option>               
            @endforeach 
    </select>
                </div>

                <br>
                <button type="submit"  class="btn btn-primary " style="height: 40px;display:block;">update</button>

            </div>
        </div>
        
      
    </form>
 </div>
@endsection
