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
                  <h4 class="card-title">Threshold List</h4>
                  <a href="{{url('admin/update_acc_threshold')}}" class="btn btn-primary float-right">
                    Update Threshold
                  </a>
              </div>
              <div class="card-content">
                  <div class="card-body card-dashboard">
                      <p class="card-text">Threshold List</p>
                      <div class="table-responsive">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>#id</th>
                                      <th>Payroll No</th>
                                      <th>Start Date</th>
                                      <th>End Date</th>
                                      <th>Threshold</th>
                         @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                                      <th>Action</th>
                                      @endif

                                  </tr>
                              </thead>
                              <tbody>
                                 
                                      @php
                                          $i=1;
                                      @endphp
                                      
                               @foreach($threshold as $list)
                               <tr>
                                <td>{{$i++}}</td>
                                <td>{{$list->payroll_no}}</td>
                                <td>{{$list->start_date}}</td>
                                <td>{{$list->end_date}}</td>
                                <td>{{$list->accumalative_payrol_value}}</td>
                                <td>
                     @if($i==(count($threshold)+1) && count($threshold)!=1)

                                    <a href="{{url('admin/delete_accumulate_threshold')}}/{{$list->payroll_no}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>
                                </td>
                    @endif

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
