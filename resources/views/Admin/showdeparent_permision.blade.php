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
                      <form method="get" action="{{route('admin.department.save')}}" style="background-color: white;">
                    @csrf
                        <div class="form-group">
                        <div class="controls">
                            <select required style="margin:10px 10px; width:30%;" class="form-control select" name="department" placeholder="Department" >
                                <option value="" >select Supervisor</option>
                                @foreach ($user as $list)
                                <option value="{{ $list->id }}" >
                                {{ $list->first_name }}</option>
                                @endforeach
                            </select>
                        </div>
                     </div>
                
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
                               @foreach($all as $list)
       @php $var=explode(',',$list->department_id) ;

                                         @endphp
                                                                                                                 {{--  @foreach($var as $new)  --}
                               <tr>
                                    <td>{{$list->id}}</td>
                                         
                                    <td>{{$list->user->first_name ?? ''}}</td>

                                    <td>{{$list->department_id}}</td>
                              <td><a href="{{ url('admin/update-department-permission',$list->user->id) }}" >update</a></td>

                                    <td><input type="checkbox" name="demp[]" @if($list->user_id==$list->user->id) checked @endif value="{{$list->id}}" ></td>
                                </tr>
                                                                    {{--  @endforeach  --}}

                               @endforeach
                            
                              </tbody>

                          </table>
                      </div>
                           
                        </div>
                        <button type="submit" style="margin-top:10px; margin-left:10px;" class="btn  btn-primary "><i class="fa fa-search"> </i> Search</button>
                    </div>
                </form>
              </div>
          </div>
      </div>
  </div>
</section>
</div>
@endsection
