@extends('layouts.admin')
@section('content')
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>  --}}
{{--  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>  --}}
<!-- Year Picker CSS -->
{{--  <link rel="stylesheet" href="{{asset('css/yearpicker.css')}}" />  --}}

<!-- Year Picker Js -->
{{--  <script src="{{asset('js/yearpicker.js')}}"></script>  --}}
    <section id="basic-datatable">
        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Continuous Deduction
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <form class="my-5" method="post" action="{{route('admin.add.continuous')}}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="first-name-icon">Select Type</label>

                                                <div class="position-relative has-icon-left">
                                    <select class="form-control select2" name="employee" placeholder="Department" id="department-dropdown" >
                                        <option value="" >Select Employee</option>
                                        @foreach ($dep_arr as $list)
                                            <option value="{{ $list->id }}" >
                                                {{ $list->first_name }}{{" "}}{{$list->last_name}}</option>
                                        @endforeach
                                    </select>
                                                    <div class="form-control-position">
                                                    </div>
                                                </div>
                                            </div>
                                    <div class="form-group">
                                                <label for="first-name-icon">Name</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="text" class="form-control" name="deduction_name" placeholder="name">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar "></i>
                                                    </div>
                                                </div>

                                    </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="password-icon">Period Start Date</label>
                                                        <div class="position-relative has-icon-left">
                                                            <input type="date" id="password-icon" class="form-control"
                                                                name="period" placeholder="Start Date">
                                                            <div class="form-control-position">
                                                                <i class="feather icon-calendar "></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="first-name-icon">Cycle</label>

                                                <div class="position-relative has-icon-left">
                                                    <select type="text" list="Paid" id="first-name-icon"
                                                        class="form-control" name="cycle" placeholder="selelct type"
                                                        required="">

                                                        <option  value="" >Select Cycle</option>
                                                        <option value="14">Fortnightly</option>
                                                        <option value="30">Monthly</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="first-name-icon">Salary Base</label>

                                                <div class="position-relative has-icon-left">
                                                    <select type="text" list="Paid" id="first-name-icon"
                                                        class="form-control" name="salary_base" placeholder="selelct type"
                                                        required="">

                                                        <option  value="" >Select</option>
                                                        <option value="0">Hourly</option>
                                                        <option value="1">Daily</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="first-name-icon">Amount</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="text" class="form-control" name="amount" placeholder="Amount">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar "></i>
                                                    </div>
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