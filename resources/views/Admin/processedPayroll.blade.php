@extends('layouts.admin')
@section('content')
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>  --}}
{{--  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>  --}}
<!-- Year Picker CSS -->
{{--  <link rel="stylesheet" href="{{asset('css/yearpicker.css')}}" />  --}}

<section id="basic-datatable">
    <form  action="{{route('admin.process_payroll_search')}}" method="get" style="background-color: white;">
    <div class="row justify-content-center">
    
        <div class="col-12 col-md-3 mt-2 " >
                   <div class="form-group">
           <div class="controls">
        
            <input type="date" id="date-input" class="form-control"  placeholder="Start date" name="start_date" placeholder="start date"  value="" required>
           </div>
        </div>
    </div>



    <div class="col-12 col-md-3 mt-2" >
        <div class="form-group">
            <div class="controls">
            <input class="form-control"    placeholder="End date"
            type="date" id="range" name="end_date" placeholder="select date range" value="" required  >
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3 mt-2 " >
        <div class="form-group">
            <div class="controls">
                <select class="form-control" name="cycle" placeholder="Cycle">
                    <option value="" >select cycle</option>
                    @foreach ($threshold as $list)
                        <option value="{{ $list['cycle'] }}" >
                            {{ $list['cycle'] }}</option>
                    @endforeach
                </select>
    
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3 mt-2 " >
        <div class="form-group">
            <div class="controls">
                <select class="form-control select2" name="department" placeholder="Department" id="department-dropdown">
                    <option value="" >select Department</option>
                    @foreach ($dep_arr as $list)
                        <option value="{{ $list['department'] }}" >
                            {{ $list['department'] }}</option>
                    @endforeach
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
</section>
<div class="card">
    <div class="card-header">
      <h4 class="card-title">Show All Process Pay Roll List</h4>
    </div>


    <div class="card-content">
        <div class="card-body card-dashboard">
            <p class="card-text">ProcessPay List</p>
            <div class="table-responsive">
                <table class="table zero-configuration">
                    <thead>
                        <tr>
                            <th>Employee Id</th>
                            <th>Name</th>
                            <th>Cycle</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Total Pay</th>
                            <th>Net Pay</th>
                            <th>Total Deductions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($processPayroll as $process)
                    <tr>
                        <td>{{$process->user_id}}</td>
                        <td>{{$process->emp_name}}</td>
                        <td>{{$process->cycle}}</td>
                        <td>{{$process->type}}</td>
                        <td>{{$process->start_date}}</td>
                        <td>{{$process->end_date}}</td>
                        <td>{{$process->total_pay}}</td>
                        <th>{{$process->net_pay}}</th>
                        <th>{{$process->total_deduction}}</th>
                        <td><a href="{{url('admin/delete_process_payroll')}}/{{$process->user_id}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Total Pay</th>
                            <th>Net Pay</th>
                            <th>Total Deductions</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>{{ round($totalprocessed[0]['total_pay'],2) }}</td>
                        <td>{{ round($totalprocessed[0]['net_pay'],2) }}</td>
                        <td>{{ round($totalprocessed[0]['total_deduction'],2) }}</td>
                    </tr>
                </table>
    @endsection