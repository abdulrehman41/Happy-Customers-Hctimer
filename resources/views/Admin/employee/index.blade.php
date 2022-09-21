@extends('layouts.admin')
@section('title','Employee')
@section('heading','Employees')

@section('content')

<?php
use Carbon\Carbon;
use App\Models\Attendence;


?>
@php
$dep = DB::table('departments')->get();
$user = DB::table('users')->select('id','first_name','last_name','user_role')->where('user_role','Employee')->get();
@endphp

 <section id="basic-datatable">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="form-group">
                      <h4 class="card-title" style="margin:15px">Filter By Department</h4>
                 </div>
            <div class="card-header">
                
                <form method="get" action="{{route('admin.employe.department')}}" style="background-color: white;">
                     <div class="form-group">
                        <div class="controls">
                            <select class="form-control select" name="department" placeholder="Department" >
                                <option value="" >select Department</option>
                                @foreach ($dep as $list)
                                <option value="{{ $list->id }}" >
                                {{ $list->department }}</option>
                                @endforeach
                            </select>
                            
                        </div>
                        <button type="submit" style="margin-top:10px;" class="btn  btn-primary "><i class="fa fa-search"> </i> Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>

  <div class="row">

      <div class="col-12">

 <div class="card">
                                <div class="card-content">
                                    
                                        
              <div class="card-header">
                <h4 class="card-title">Show All Employes List</h4>
                                                        @if($Employepermision['full_access']==1 )
                                                                                                        <div class="text-right">

        <a href="{{ route('admin.employees.create') }}" class="btn btn-success">+ Add New</a>
               
               </div>
@endif
                 
              </div>
                           

              <div class="card-content">
                  
                  <div class="card-body card-dashboard" style="mt--5">
                      <div class="table-responsive">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                                            <th>First Name</th>
                                                            <th>Last Name</th>
                                                            <th>Phone Number</th>
                                                            <th>Photo</th>
                                                            <th>Hourly Rate</th>
                                                            <th>Actions</th>
                                                        </tr>
                               
                              </thead>
                              <tbody>
                                                                                      @foreach($employees as $emp)

                                <tr>
                                                            <td>{{ $emp->first_name }}</td>
                                                            <td>{{ $emp->last_name }}</td>
                                                            <td>{{ $emp->phone_number }}</td>
                                                            <td><img src="{{ asset('uploads/employees/'.$emp->photo) }}" width="100"></td>
                                                            <td>{{ $emp->hourly_rate }}</td>
                                                            <td>

                                                         <a href="{{ route('admin.employees.view', $emp->id) }}"><i class="feather   icon-eye " title="View Detail" ></i></a>
                                                @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                                                                <a href="{{ route('admin.employees.edit', $emp->id) }}"><i class="feather icon-edit ml-0.5" title="Edit Detail"></i></a>
                                                                @endif
                                         @if($Employepermision['full_access']==1 )

                                                                <a href="{{ route('admin.employees.delete', $emp->id) }}"><i class="feather ml-0.5 icon-trash text-danger" title="Delete Detail"></i></a>
                                                         @endif
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
      <div class="modal fade" id="addattendance" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Add Attendance</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{route('admin.add.admin_attendance')}}" method="post">
                @csrf
              <div class="form-group">
                <label for="first-name-icon">Select Date</label>
                <div class="position-relative has-icon-left">
                  <input required type="date" name="date" class="form-control  @error('department_name') is-invalid @enderror"  >
                  @error('department_name')
                  <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>


                    <label for="first-name-icon">start Time</label>
                <div class="position-relative has-icon-left">
                  <input required type="time" name="start_time" class="form-control  @error('department_name') is-invalid @enderror"  >
                  @error('department_name')
                  <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>



                    <label for="first-name-icon">End Time</label>
                <div class="position-relative has-icon-left">
                  <input required type="time" name="end_time" class="form-control  @error('department_name') is-invalid @enderror"  >
                  @error('department_name')
                  <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>
                         <label for="first-name-icon">Users</label>
                <div class="position-relative has-icon-left">
             <select class="select2 form-control" name="user"  required>
      #
                </div>


            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit"  class="btn btn-primary">Add Attendance</button>
        </div>
    </form>

      </div>

  </div>

               
                @endsection

