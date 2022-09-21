@extends('layouts.admin')
@section('content')
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>  --}}
{{--  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>  --}}
<!-- Year Picker CSS -->
{{--  <link rel="stylesheet" href="{{asset('css/yearpicker.css')}}" />  --}}

<!-- Year Picker Js -->
{{--  <script src="{{asset('js/yearpicker.js')}}"></script>  --}}
        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Deductions
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <form class="my-5" method="post" action="{{url('admin/update_deduction')}}/{{$edit_deduction->id}}" >
                                            @csrf
                                            <div class="form-group">
                                                <label for="first-name-icon">Name</label>

                                                <div class="position-relative has-icon-left">
                                                    <input type="text" class="form-control" value={{ $edit_deduction->name }} name="name" placeholder="name" min="2021" max="2050">
                                            
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label for="first-name-icon">Select Type</label>

                                                <div class="position-relative has-icon-left">
                                                    <select type="text" list="browsers" id="first-name-icon"
                                                        class="form-control" name="type_value" placeholder="Select type value"
                                                        required="">

                                                        <option value="">select type value</option>
                                                        <option value="doller" @if($edit_deduction->percentage_value =='doller') selected  @endif>$</option>
                                                        <option value="percentage" @if($edit_deduction->percentage_value =='percentage') selected  @endif >%</option>
                                                </select>
                                                    
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password-icon">value percentage</label>
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="password-icon" value={{$edit_deduction->nis_fix_value}} class="form-control"
                                                                name="percentage" placeholder="percentage">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password-icon">NIS FIX Value Percentage</label>
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="password-icon" class="form-control"
                                                                name="nis" placeholder="Nis" value={{ $edit_deduction->nis }}>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="first-name-icon">value</label>

                                                <div class="position-relative has-icon-left">
                                                    <select type="text" list="Paid" id="first-name-icon"
                                                        class="form-control" name="type" placeholder="selelct type"
                                                        required="">

                                                        <option  value="" >select type</option>
                                                        <option value="employe_decduction" @if($edit_deduction->type_deduction =='employe_decduction') selected  @endif >Employe decduction</option>
                                                        <option value="employe_contribition" @if($edit_deduction->type_deduction =='employe_contribition') selected  @endif >Employe contribition</option>
                                                    </select>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-user"></i>
                                                    </div>
                                                    @error('type')
                                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="btn-group pull-left">
                                                <button type="reset" class="btn btn-warning pull-right">Reset</button>
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