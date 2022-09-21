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
                  <h4 class="card-title">Continuous Deduction List</h4>
                  <a href="{{url('admin/add_continuous_deduction')}}" class="btn btn-primary float-right">
                    Add
                  </a>
              </div>
              <div class="card-content">
                  <div class="card-body card-dashboard">
                      <p class="card-text">Continuous Deduction</p>
                      <div class="table-responsive">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>#id</th>
                                      <th>Employee Name</th>
                                      <th>Deduction Name</th>
                                      <th>Start Period</th>
                                      <th>Cycle</th>
                                      <th>Amount</th>
                                      <th>Salary Base</th>
                                      <th>Action</th>
                                      <th>Delete</th>
                                  </tr>
                              </thead>
                              <tbody>
                                 
                                      @php
                                          $i=1;
                                      @endphp
                                      
                               @foreach($threshold as $list)
                               <tr>
                                <td>{{$i++}}</td>
                                <td>{{$list->user_name}}</td>
                                <td>{{$list->deduction_name}}</td>
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
                                @if($list->action=='0')
                                <a href="{{url('admin/stop_continuous')}}/{{$list->id}}/{{$list->start_period}}" class="btn btn-danger  mr-2"  >Stop</a>
                                </td>
                                @else
                                <a href="{{url('admin/start_continuous')}}/{{$list->id}}/{{$list->start_period}}" class="btn btn-primary  mr-2"  >Start</a>
                                </td>
                                @endif
                                <td>
                                    <a href="{{url('admin/delete_continuous')}}/{{$list->id}}/{{$list->start_period}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>
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
  
  
  <script>
      $(document).ready(function(){
          $('.continuous_stop').click(function(e){
              alert("hello");
              e.preventDefault();
              alert($(this).attr('data-id'));
          })
      })
  </script>
  
</section>
</div>
@endsection
