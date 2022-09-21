@extends('layouts.admin')
@section('content')
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>  --}}
{{--  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>  --}}
<!-- Year Picker CSS -->
{{--  <link rel="stylesheet" href="{{asset('css/yearpicker.css')}}" />  --}}
<div class="card">

    @php
        use App\Models\Department;

        $dept = Department::get();

    @endphp
    <div class="card-content ml-4 mr-4 mt-4 mb-2">
        <form class="form-horizontal form-material" method="post" action="{{ url('admin/deduction_response') }}"  >
            @csrf
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="password-icon">Start Date</label>
                    <div class="position-relative has-icon-left">
                        <input type="date" id="date-input" class="form-control" 
                        name="start_date" placeholder="Start Date">
                        <div class="form-control-position">
                        <i class="feather icon-calendar "></i>
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-6">
                    <label for="password-icon">End Date</label>
                    <div class="position-relative has-icon-left">
                        <input type="date" id="date-input" class="form-control" 
                        name="end_date" placeholder="End Date">
                        <div class="form-control-position">
                        <i class="feather icon-calendar "></i>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="form-group">
                <label for="department">Department</label>
                <div class="position-relative has-icon-left" >
                  <select class="form-control select2" name="dep" placeholder="Department" id="department-dropdown" >
                      <option value="" >Select Department</option>
                    @foreach($dept as $list)
                    <option value="{{$list->id}}" class="select_dep">{{$list->department}}</option>
                    @endforeach
                  </select>
                  
                </div>
            </div>
            <div class="form-group">
                <label for="department">User</label>
                <div class="position-relative has-icon-left">
                   <select class="select2 form-control" name="user" id="user_dropdown">
               
        <option value="" >select user</option>
    </select>
                  
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary mt-2">Submit</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
      <h4 class="card-title">Bonus Report</h4>
      </div>


    <div class="card-content">
      @if($message = Session::get('error'))
        <div class="alert alert-danger ">
          <strong>{{ $message }}</strong>
        </div>
      @endif
        <div class="card-body card-dashboard">
            <p class="card-text">List of Report</p>
            <div class="table-responsive">
                <table class="table zero-configuration">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Year</th>
                            <th>Start Period</th>
                            <th>End Period</th>
                            <th>NIS</th>
                            <th>NHT</th>
                            <th>ED Tax</th>
                            <th>Income Tax</th>
                            <th>Total Pay</th>
                            <th>Net Pay</th>
                            <th>Total Deduction</th>
                            <th>Bonus</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($deduction!=null)
                    @foreach ($deduction as $payroll)
                    <tr>
                        <td>{{$payroll->emp_name}}</td>
                        <td>{{$payroll->year}}</td>
                        <td>{{$payroll->start_date}}</td>
                        <td>{{$payroll->end_date}}</td>
                        <td>{{$payroll->nis}}</td>
                        <td>{{$payroll->nht}}</td>
                        <td>{{$payroll->edtax}}</td>
                        <td>{{$payroll->income}}</td>
                        <td>{{$payroll->total_pay}}</td>
                        <td>{{$payroll->net_pay}}</td>
                        <td>{{$payroll->total_deduction}}</td>
                        <td>{{$payroll->bonus}}</td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                @if($totalDeduction!=null)
                <table class="table">
                  <thead>
                      <tr>
                          <th>Total NIS</th>
                          <th>Total NHT</th>
                          <th>Total ED Tax</th>
                          <th>Total Income Tax</th>
                          <th>Total Pay</th>
                          <th>Total Net Pay</th>
                          <th>Total Deductions</th>
                          <th>Total Bonus</th>
                      </tr>
                  </thead>
                  <tr>
                      <td>{{ $totalDeduction[0]['total_nis'] }}</td>
                      <td>{{ $totalDeduction[0]['total_nht'] }}</td>
                      <td>{{ $totalDeduction[0]['total_edtax'] }}</td>
                      <td>{{ $totalDeduction[0]['total_income'] }}</td>
                      <td>{{ $totalDeduction[0]['total_pay'] }}</td>
                      <td>{{ round($totalDeduction[0]['net_pay'],2) }}</td>
                      <td>{{ round($totalDeduction[0]['total_deduction'],2) }}</td>
                      <td>{{ $totalDeduction[0]['total_bonus'] }}</td>
                  </tr>
              </table>
              @endif
              









  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="get" action="{{url('admin/add_start_date')}}">
            <div class="modal-body">
                <div class="form-group">
                    <div class="position-relative has-icon-left">
                        <input type="date" class="form-control"  name="start_d" placeholder="Start Date" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
  $('document').ready(function(){
      $('#mySelect option').click(function(){
        alert("click");
});
    
  });
  
  
  </script>

    @endsection
