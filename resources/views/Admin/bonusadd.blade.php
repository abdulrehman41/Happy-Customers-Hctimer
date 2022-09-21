@extends('layouts.admin')
@section('content')

<?php
use Carbon\Carbon;
use App\Models\Attendence;


?>
@php
$dep = DB::table('departments')->get();
$user = DB::table('users')->select('id','first_name','last_name','user_role')->where('user_role','!=','admin')->get();
@endphp


 <section id="basic-datatable">

 <form  action="{{route('bonus')}}" method="get" style="background-color: white;">
    <input type="hidden" class="bonus_hidden1" value={{$bonus_dep}} />
    <input type="hidden" class="bonus_hidden2" value={{$bonus_user}} />
    <input type="hidden" class="temp_inp" value={{$temp_inp}} />
  <div class="row justify-content-center">

           <div class="col-12 col-md-3 mt-2 " >
               <div class="form-group">
   <div class="controls">

    <input type="text" id="date-input" class="form-control a_b_s_d"  placeholder="Start date"
                    onfocus="(this.type='date')"
        name="start_date"  required value={{$s_date}} >
   </div>
               </div>
           </div>



            <div class="col-12 col-md-3 mt-2" >
               <div class="form-group">
   <div class="controls">
<input class="form-control a_b_e_d"    placeholder="End date"
                    onfocus="(this.type='date')"   type="text" id="range" name="end_date" required value={{$e_date}}   >
   </div>
               </div>
           </div>

<div class="col-12 col-md-3 mt-2 " >
    <div class="form-group">
        <div class="position-relative has-icon-left" >
            <select class="form-control a_b_d select2" name="dep" placeholder="Department" id="bonus-department-dropdown" >
                <option value="" >Select Department</option>
                    @foreach($dep as $list)
                    <option value="{{$list->id}}" class="select_dep">{{$list->department}}</option>
                    @endforeach
            </select>
                  
        </div>
    </div>
</div>

@php

    $user = DB::table('users')->select('id','first_name','last_name')->where('user_role','!=','admin')->get()->toArray();
    
@endphp

<div class="col-12 col-md-3 mt-2 " >
    <div class="form-group">
        <div class="position-relative has-icon-left">
            <select class="select2 a_b_u form-control" name="user" id="bonus_user_dropdown">
                <option value="" >select user</option>
            </select>
                  
        </div>
    </div>
</div>








 <div class="col-12 col-md-2 mt-2" >
<div class="form-group">

                                                   <div class="form-group  ">
                        <button type="submit" class="btn  btn-primary "><i class="fa fa-search"> </i> Search</button>
                  {{-- <button type="button" class="btn btn-info float-right" data-toggle="modal" data-target="#addattendance">
New                  </button> --}}
                    </div>
                                    </div>
 </div>
  </div>



                    </form>
</div>

  <div class="row">

      <div class="col-12">

          <div class="card" style="box-shadow: none;">


              <div class="card-content">
                  <div class="card-body card-dashboard" style="mt--5">
                      <div class="table-responsive">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>user id</th>
                                      <th>Employee Name</th>
                                      <th>Bonus Name</th>
                                      <th>Pay Period From</th>
                                      <th>Pay Period To</th>
                                      <th>Bonus</th>

                                      <th>Action</th>

                                  </tr>
                              </thead>
                              <tbody>
                                  <form  action="{{route('storeboubus')}}" method="post" style="background-color: white;">
                                 @csrf
                                    @foreach($users as $value)


<tr>
<td><input type="text" value="{{  $value->id  }}" name="user_id[]" readonly style="border-radius: none;  outline: none;
 border:none;">        <td><input type="text" value="{{  $value->first_name  }}" name="first_name[]" readonly style="border-radius: none;  outline: none;
 border:none;"><input type="text" value="{{   $value->last_name  }}" name="last_name[]" readonly style="border-radius: none;  outline: none;
 border:none;"></td>
<td><input  type="text"  name="bonus_name[]"  value="Bonus"></td>

<td><input type="text" value="{{   $period_from  }}" name="period_from[]" readonly style="border-radius: none;  outline: none;
 border:none;"></td>
<td><input type="text" value="{{   $period_to  }}" name="period_to[]" readonly style="border-radius: none; outline: none;border:none;"></td>
    <td><input  type="number"  name="bonus[]" step="0.01"  value="0"></td>


                                  </tr>
 @endforeach


                              </tbody>

                          </table>
                           <button type="submit" class="btn btn-primary">save</button>
</form>
                      </div>
                  </div>
              </div>
          </div>
      </div>

    </form>

      </div>

  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
      
    if(localStorage.getItem("a_b_s_d") === null && localStorage.getItem("a_b_e_d") === null && localStorage.getItem("a_b_d")=== null && localStorage.getItem("a_b_u")=== null)
            {
                localStorage.setItem('a_b_s_d', ""); 
                localStorage.setItem('a_b_e_d', "");
                localStorage.setItem('a_b_d', "");
                localStorage.setItem('a_b_u', "");
                
            }
            else {
                if(localStorage.getItem("a_b_s_d") === "" && localStorage.getItem("a_b_e_d") === "" && localStorage.getItem("a_b_d")=== "" && localStorage.getItem("a_b_u")=== "")
                {
                        var t_d = $(".bonus_hidden1").val();
                        if(t_d=="/")
                        {
                            t_d = "";
                        }
                        var t_u = $(".bonus_hidden2").val();
                        if(t_u=="/")
                        {
                            t_u = "";
                        }
                        localStorage.setItem("a_b_s_d",$(".a_b_s_d").val());
                        localStorage.setItem("a_b_e_d",$(".a_b_e_d").val());
                        localStorage.setItem("a_b_d",t_d);
                        localStorage.setItem("a_b_u",t_u);
                        $(".a_b_s_d").val(localStorage.getItem("a_b_s_d"));
                        $(".a_b_e_d").val(localStorage.getItem("a_b_e_d"));
                        var a_b_p = localStorage.getItem("a_b_d");
                        $(`.a_b_d option[value=${a_b_p}]`).attr("selected", "selected");
                        var a_b_u = localStorage.getItem("a_b_u");
                        $(`.a_b_u option[value=${a_b_u}]`).attr("selected", "selected");
                }
                else {
                        var temp_c = $(".temp_inp").val();
                        if(temp_c==="0")
                        {
                            $(".a_b_s_d").val(localStorage.getItem("a_b_s_d"));
                            $(".a_b_e_d").val(localStorage.getItem("a_b_e_d"));
                            var a_b_d = localStorage.getItem("a_b_d");
                            $(`.a_b_d option[value=${a_b_d}]`).attr("selected", "selected");
                            var a_b_u = localStorage.getItem("a_b_d");
                            $(`.a_b_u option[value=${a_b_u}]`).attr("selected", "selected");
                            $(".temp_inp").val("0");
                        }
                        else {
                            var t_d = $(".bonus_hidden1").val();
                            var t_u = $(".bonus_hidden2").val();
                            localStorage.setItem("a_b_s_d",$(".a_b_s_d").val());
                            localStorage.setItem("a_b_e_d",$(".a_b_e_d").val());
                            localStorage.setItem("a_b_d",t_d);
                            localStorage.setItem("a_b_d",t_u);
                            $(".a_b_s_d").val(localStorage.getItem("a_b_s_d"));
                            $(".a_b_e_d").val(localStorage.getItem("a_b_e_d"));
                            var a_b_s = localStorage.getItem("a_b_d");
                            $(`.a_b_d option[value=${a_b_s}]`).attr("selected", "selected");
                            var a_b_e = localStorage.getItem("a_h_d");
                            $(`.a_b_u option[value=${a_b_e}]`).attr("selected", "selected");
                            $(".temp_inp").val("0");
                        }
                }
                
                
            }
    
      
  </script>

</section>
@endsection

