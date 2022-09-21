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

 <section id="basic-datatable">
  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Supervisor Department</h4>
              </div>
              <div class="card-content">
                      <form method="get" action="{{route('admin.employe.department')}}" style="background-color: white;">
                     <div class="form-group">
                        <div class="controls">
                            <select style="margin:10px 10px; width:30%;" class="form-control select" name="department" placeholder="Department" >
                                <option value="" >select Supervisor</option>
                                @foreach ($user as $list)
                                <option value="{{ $list->id }}" >
                                {{ $list->first_name }}</option>
                                @endforeach
                            </select>
                            
                        </div>
                        <button type="submit" style="margin-top:10px; margin-left:10px;" class="btn  btn-primary "><i class="fa fa-search"> </i> Search</button>
                    </div>
                </form>
                
                <div class="table-responsive" style="padding: 0 10px;">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>#id</th>
                                      <th>Department</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                 
                                      @php
                                          $i=1;
                                      @endphp
                               @foreach($dep as $list)
                               <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$list->department}}</td>
                                    <td><input type="radio" name="radio{{$i-1}}" /></td>
                                </tr>
                               @endforeach
                            
                              </tbody>

                          </table>
                      </div>
              </div>
          </div>
      </div>
  </div>
</section>
</div>
@endsection
