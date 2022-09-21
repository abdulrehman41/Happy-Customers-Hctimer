@extends('layouts.admin')
@section('content')
@php
        $s_time = date('H:i:s A',strtotime($attn->start_time));
        $e_time = date('H:i:s A',strtotime($attn->end_time));

//        dd($attn->start_time,$attn->end_time);
@endphp

        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Attendance</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <form class="my-5" method="get" action="{{url('admin/update_attendance_return')}}/{{$attn->id}}/{{$attn->user_id}}" >
                                            <div class="form-group">
                                                <label for="first-name-icon">Date</label>

                                                <div class="position-relative has-icon-left">
                                                <input class="form-control" name="date" required placeholder="Date" type="text" value={{$attn->date}} >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="first-name-icon">Start Time</label>

                                                <div class="position-relative has-icon-left">
                                                <input class="form-control" name="start_time" placeholder="Start Time" type="time" value={{$s_time}} step="1" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="first-name-icon">End Time</label>

                                                <div class="position-relative has-icon-left">
                                                <input class="form-control" name="end_time" placeholder="End Time" type="time" value={{$e_time}} step="1" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="position-relative has-icon-left">
                                                <input class="form-control" name="user" required placeholder="End Time" type="hidden" value={{$attn->id}}  >
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <div class="position-relative has-icon-left">
                                                <input class="form-control" name="user_id" required placeholder="End Time" type="hidden" value={{$attn->user_id}}  >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="position-relative has-icon-left">
                                                <input class="form-control" name="auth_id" required placeholder="End Time" type="hidden" value={{$auth_id}}  >
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
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

@endsection