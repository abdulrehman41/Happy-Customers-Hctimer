@extends('layouts.admin')
@section('content')

<?php
use Carbon\Carbon;
use App\Models\Attendence;


?>
 <section id="basic-datatable">
  <div class="row">
      <div class="col-12">
        @if ($message = Session::get('success'))
        <div class="alert alert-success ">    
            <strong>{{ $message }}</strong>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger ">    
            <strong>{{ $message }}</strong>
        </div>
        @endif 
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">One Time Deduction List</h4>
                  <a href="{{url('admin/add_one_time_deduction')}}" class="btn btn-primary float-right">
                    Add
                  </a>
              </div>
              <div class="card-content">
                  <div class="card-body card-dashboard">
                      <p class="card-text">One Time Deduction</p>
                      <div class="table-responsive">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>#id</th>
                                      <th>Deduction Name</th>
                                      <th>Employee Name</th>
                                      <th>Start Period</th>
                                      <th>Cycle</th>
                                      <th>Amount</th>
                                      <th>Salary Base</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                 
                                      @php
                                          $i=1;
                                      @endphp
                                      
                               @foreach($threshold as $list)
                               <tr>
                                <td>{{$i++}}</td>
                                <td>{{$list->deduction_name}}</td>
                                @php 
                                    
                                    $names = DB::table('users')->select('first_name','last_name')->where('id',$list->employee)->get();
                                    
                                @endphp
                                <td>{{$names[0]->first_name}}{{" "}}{{$names[0]->last_name}}</td>
                                <td>{{$list->start_period}}</td>
                                @if($list->cycle=='14')
                                <td>Fortnightly</td>
                                @else
                                <td>Monthly</td>
                                @endif
                                <td>{{$list->amount}}</td>
                                @if($list->salary_base==0)
                                <td>Hourly</td>
                                @else
                                <td>Daily</td>
                                @endif
                                <td>
                                <a href="{{url('admin/delete_one_time_deduction')}}/{{$list->id}}/{{$list->start_period}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>
                                <a href="{{url('admin/edit_one_time_deduction')}}/{{$list->id}}/{{$list->start_period}}" class="text-primary"><i class="feather icon-edit" title="Edit"></i></a>
                                </td>

                            </tr>
                               @endforeach
                            
                              </tbody>

                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
 
  
</section>
</div>
@endsection
