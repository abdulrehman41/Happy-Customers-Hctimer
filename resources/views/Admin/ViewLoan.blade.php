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
                  <h4 class="card-title">Periodic Deduction List</h4>
                  <a href="{{url('admin/add_loan')}}" class="btn btn-primary float-right">
                    Add Loan
                  </a>
              </div>
              <div class="card-content">
                  <div class="card-body card-dashboard">
                      <p class="card-text">Period Deduction</p>
                      <div class="table-responsive">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>User Id</th>
                                      <th>Deduction Name</th>
                                      <th>Name</th>
                                      <th>Start Period</th>
                                      <th>End Period</th>
                                      <th>Salary Base</th>
                                      <th>Total Amount</th>
                                      <th>Remaning Period</th>
                                      <th>Cycle</th>
                                      <th>Pause</th>
                                      <th>Stop</th>
                                      <th>Delete</th>

                                  </tr>
                              </thead>
                              <tbody>
                                 
                            @foreach($laon as $l)
                               <tr>
                                <td>{{$l->user_id}}</td>
                                <td>{{$l->deduction_name}}</td>
                                <td>{{$l->name}}</td>
                                <td>{{$l->start_date}}</td>
                                <td>{{$l->end_date}}</td>
                                @if($l->salary_base==0)
                                <td>Hourly</td>
                                @else
                                <td>Daily</td>
                                @endif
                                <td>{{$l->total}}</td>
                                <td>{{$l->remaning_period}}</td>
                                @if($l->cycle=='14')
                                <td>Fortnightly</td>
                                @else
                                <td>Monthly</td>
                                @endif
                                <td>
                                    @if($l->status==0 && $l->stop==0)
                                    <a href="{{url('admin/pause_loan')}}/{{$l->id}}" class="btn btn-danger  mr-2">Pause</a>
                                    @elseif($l->stop==1)
                                    {{""}}
                                    @else
                                    <a href="{{url('admin/start_loan')}}/{{$l->id}}" class="btn btn-success  mr-2">Start</a>
                                    @endif
                                    
                                </td>
                                <td>
                                    @if($l->stop==0)
                                    <a href="{{url('admin/stop_loan')}}/{{$l->id}}/{{$l->start_date}}" class="btn btn-danger">Stop</a>
                                    @else
                                        <a class="btn btn-success" style="color:white">Completed</a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{url('admin/delete_loan')}}/{{$l->id}}/{{$l->start_date}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>
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
