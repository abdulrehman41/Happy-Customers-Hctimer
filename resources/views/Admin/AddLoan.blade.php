@extends('layouts.admin')
@section('content')

@php

    $emp = DB::table('users')->select('id','first_name','last_name')->where('user_role','!=','Admin')->get()->toArray();
    $cycle = DB::table('thresholds')->select('days','cycle')->get()->toArray();
@endphp
        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Periodic Deduction
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <form class="my-5" method="post" action="{{url('admin/add_loan_data')}}" >
                                            @csrf
                                            
                                            <div class="form-group col-sm-12">
                                                <label for="first-name-icon">Deduction Name</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="text" class="form-control" name="deduction_name" placeholder="Deduction Name"  required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                    <select class="form-control             select2" name="employee"                  placeholder="Employee" >
                                    <option value="" >Select Employee</option>
                                    @foreach ($emp as $list)
                                        <option value="{{ $list->id }}" >
                                            {{ $list->first_name }}{{" "}}{{$list->last_name}}</option>
                                    @endforeach
                                    </select>
                                            </div>
                                    <div class="form-group">
                                    <select class="form-control             select2" name="cycle"                  placeholder="Cycle" >
                                    <option value="" >Select Cycle</option>
                                    @foreach ($cycle as $list)
                                        <option value="{{ $list->days }}" >
                                            {{ $list->cycle }}</option>
                                    @endforeach
                                    </select>
                                            </div>
                                    <div class="form-group">
                                    <select class="form-control" required name="salary_base"                  placeholder="Select" >
                                        <option value="" >Select</option>
                                        <option value="0" >Hourly</option>
                                        <option value="1" >Daily</option>
                                    </select>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                <label for="first-name-icon">Start Period</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="date" class="form-control" name="start_date" placeholder="Start Date" min="2021" max="2050" required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-sm-6">
                                                <label for="first-name-icon">End Period Date</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="date" class="form-control" name="end_date" placeholder="End Date" min="2021" max="2050" required>
                                                </div>
                                            </div>
                                            </div>
                                            
                                        <div class="row">
                                            
                                            <div class="form-group col-sm-12">
                                                <label for="first-name-icon">Total Amount</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="text" class="form-control" name="total" placeholder="Total" step="0.1" required>
                                                </div>
                                            </div>
                                            
                                        
                                        
                                        
                                            
                                            <div class="btn-group pull-right">
                                                <button type="submit" class="btn btn-info pull-right">Submit</button>
                                            </div>


                                        </form>
                                        <br>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
@endsection