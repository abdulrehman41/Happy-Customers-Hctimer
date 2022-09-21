@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<section id="basic-datatable">
<section id="basic-datatable">
<div class="row">
<div class="col-12">
<div class="card">
{{--  <div class="card-header">
<h4 class="card-title"> Employee Payroll
</h4>
</div>  --}}
<div class="card-content">
<div class="card-body card-dashboard">
<div class="row">
<div class="col-md-6">
   <h3 class="box-title">Employee Payroll  </h3>
<hr>
<form  method="post" action="{{ url('admin/daily_search') }}">
@csrf
<div class="form-group">
<label for="first-name-icon">Cycle</label>

<div class="position-relative has-icon-left">
<select class="form-control cycle d_p_c" name="cycle" placeholder="Cycle" required>
<option value="">Select Cycle</option>

@foreach ($threshold as $list)
    <option value="{{ $list->days }}" cycle="{{ $list->days }}">
        {{ $list->cycle }}</option>
@endforeach
</select>
<div class="form-control-position">
<i class="feather icon-user"></i>
</div>
</div>
</div>
<input type="hidden" class="hidden3" value={{$cyc}} />
<input type="hidden" class="hidden4" value={{$dep}} />
<input type="hidden" class="hidden5" value={{$temp_check}} />
<div class="form-group">
    <label for="start_date">Start Date</label>
    <div class="position-relative has-icon-left">
        <input type="date" id="date-input" class="form-control d_p_s_d"
            name="start_date" placeholder="Start Date" value={{$start_date}}  required>
        <div class="form-control-position">
            <i class="feather icon-calendar "></i>
        </div>
    </div>
    </div>
    
    <div class="form-group">
        <label for="start_date">End Date</label>
        <div class="position-relative has-icon-left">
            <input type="date" id="date-input" class="form-control d_p_e_d"
                name="end_date" placeholder="End Date" value={{$end_date}}  required>
            <div class="form-control-position">
                <i class="feather icon-calendar "></i>
            </div>
        </div>
        </div>

<div class="form-group">
<label for="first-name-icon">Dept</label>

<div class="position-relative has-icon-left">
<select type="text" name="DEPARTMENT" list="Weekly" id="first-name-icon"
class="form-control d_p_d"  placeholder="Dept" >
<option value="">Select Department</option>

@foreach ($department as $list)
    <option value="{{ $list->id }}">{{ $list->department }}
    </option>
@endforeach
</select>
<div class="form-control-position">
<i class="feather icon-user"></i>
</div>
</div>
</div>

@php
use Illuminate\Support\Str;
$type = gettype($users);
$contains = strcmp($type,"array");

@endphp

<div class="btn-group pull-left">
<button type="reset" class="btn btn-warning pull-right">Reset</button>
</div>
<div class="btn-group pull-right">
<button type="submit" class="btn btn-success pull-right">Submit</button>
</div>


</form>
<br>
<div class="">
<table class="table table-striped table-bordered">
<thead>
<tr>
<th scope="col">Employee Name</th>
<th scope="col">Days</th>
<th scope="col">Processed</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>





@if(strcmp(gettype($users),"array")==0)
@foreach($users as $user)
<tr>
    @php $name = DB::table('users')->select('first_name','last_name')->where('id',$user[0]["user_id"])->get()->toArray();
    
        $status_pro = App\Models\Proceed::where('user_id',$user[0]["user_id"])->where('start_date',$s_date)->where('end_date',$e_date)->count();
    
    $c = intval($cycle);
                $loan_amount = 0;
                $ins_val = 0;
                $loan_flag = false;
                $pause = -1;
                $laon_data = DB::table('loan')->where('user_id',$user[0]["user_id"])->where('cycle',$cycle)->where('start_date','>=',$s_date)->where('end_date','>=',$e_date)->get()->toArray();
                $status = count($laon_data);
                if($status > 0)
                {
                    $loan_flag = true;
                    $pause = $laon_data[0]->status;
                    if(intval($laon_data[0]->remaning_ins)!==0)
                    {
                        $ins_val = intval($laon_data[0]->remaning_ins);
                        $ins_val-=1;
                        
                        if(intval($laon_data[0]->remaning)!=0)
                        {
                            $rem = intval($laon_data[0]->remaning)-intval($laon_data[0]->amount);
                            
                            $loan_amount = $laon_data[0]->amount;
                        }
                    }
                    else {
                        $loan_amount = 0;
                    }
                }
                else {
                    $loan_flag = false;
                }
                    
        
    @endphp
    <td scope="row">{{$name[0]->first_name.' '.$name[0]->last_name}}</td>
    <td scope="row">{{count($user)}}</td>
    @if($status_pro>0)
    <td><i class="fa fa-fw fa-check approve user_stat" style="color: green;"></i></td>
    @else
    <td><i class="fa fa-fw fa-remove approve user_stat" style="color: red;"></i></td>
    @endif
    
    <td><button class="btn btn-info senddata_daily btn-sm" 
    user_id={{ $user[0]["user_id"] }} pause={{ $pause }} ins_val={{ $ins_val }} loan_flag={{$loan_flag}}  loan_amount={{$loan_amount}} no_days={{count($user)}} s_date={{$s_date}} e_date={{$e_date}} cycle={{$cycle}} status={{$status_pro}} ><i
                    class="fa fa-fw fa-eye"></i></button></td>
</tr>
@endforeach
@endif
</tbody>
</table>
</div>



</div>
<div class="col-md-6">
<div class="">

<h3 class="box-title">Department &amp; Rate</h3>
<hr>
<div class="row">
<div class="col-md-3 col-sm-6 col-xs-6">Department: </div>
<div class="col-md-3 col-sm-6 col-xs-6"><strong class="department">Department Name</strong></div>
<div class="col-md-3 col-sm-6 col-xs-6 ">Daily Rate:</div>
<div class="col-md-3 col-sm-6 col-xs-6 daily_rate">0</div>


</div>

</div>
<div class="mt-4">

<h3 class="box-title">Pay Calculation</h3>
<hr>
<div class="row">
<div class="col-md-3 col-sm-6 col-xs-6">Employee: </div>
<div class="col-md-3 col-sm-6 col-xs-6"><strong class="first_name">Name</strong></div>
<div class="col-md-3 col-sm-6 col-xs-6">TRN: </div>
<div class="col-md-3 col-sm-6 col-xs-6 trn">0</div>
<div class="col-md-3 col-sm-6 col-xs-6">NIS: </div>
<div class="col-md-3 col-sm-6 col-xs-6 nis">0</div>
<br>

<div class="col-md-3 col-sm-6 col-xs-6">No of Days: </div>
<div class="col-md-3 col-sm-6 col-xs-6 days_no">0</div>

<div class="col-md-3 col-sm-6 col-xs-6">Reg Pay:</div>
<div class="col-md-3 col-sm-6 col-xs-6 daily_total">$0</div>
<div class="col-md-3 col-sm-6 col-xs-6">Bonus:</div>
<div class="col-md-3 col-sm-6 col-xs-6 bbonus">$0</div>
<div class="col-md-3 col-sm-6 col-xs-6 stat">Stat:<div class="user_stat"></div></div>
</div>

</div>
<div class="box my-4">
<div class="box-body">
<h4>Payments</h4>
<div class="table-responsive no-padding">
<table class="table table-bordered" width="100%">
<tbody>
    <tr>
        <th>Description</th>
        <th>Day</th>
        <th>Rate</th>
        <th>Total</th>
    </tr>
    <tr>
        <th>Daily Pay</th>
        <td class="no_days">0</td>
        <td class="rate_daily">$0.00</td>
        <td class="total_daily">$0.00</td>
    </tr>
    <tr>
        <th>Sick Leave Pay</th>
        <td class="sick_hour">0.00</td>
        <td class="sick_rate">0.00</td>
        <td class="sick_total">$0.00</td>
    </tr>
    <tr>
        <th>Vacation Leave Pay</th>
        <td class="vacation_hour">0.00</td>
        <td class="vacation_rate">0.00</td>
        <td class="vacation_total">$0.00</td>
    </tr>
    <tr>
        <th>Maternity Leave Pay</th>
        <td class="maternity_hour">0.00</td>
        <td class="maternity_rate">0.00</td>
        <td class="maternity_total">$0.00</td>
    </tr>
    <tr>
        <th>Holiday Pay</th>
        <td class="holiday_hour">0.00</td>
        <td class="holiday_rate">0.00</td>
        <td class="holiday_total">$0.00</td>
    </tr>
    <tr>
        <th>Bonus</th>
        <td class="bonus_name">Period Bonus</td>
        <td ></td>
        <td class="bonuspay">0</td>
    </tr>

    <tr>
        <th>Total</th>
        <td></td>
        <td class="rate"></td>
        <td class="daily_pay_total">$0.00</td>

    </tr>


</tbody>
</table>
</div>
<h4>Deductions</h4>
<div class="table-responsive no-padding">
<table class="table table-bordered" width="100%">
<tbody>
    <tr>
        <th colspan="2">Reason</th>
        <th>Name</th>
        <th>Amount</th>
    </tr>
    <input type="hidden" class="user_id">
    <tr>
        <td colspan="2">NIS</td>
        <td>NIS</td>
        <td><input type="text" class="nis_ans" /></td>
    </tr>
    <tr>
        <td colspan="2">NHT</td>
        <td>NHT</td>
        <td><input type="text" class="nht_ans" /></td>
    </tr>
    <tr>
        <td colspan="2">ED TAX</td>
        <td>ED Tax</td>
        <td><input type="text" class="edt_ans" /></td>
    </tr>
    <tr>
        <td colspan="2">INCOME TAX</td>
        <td>Income Tax</td>
        <td><input type="text" class="user_incometax" /></td>
    </tr>
    <input type="hidden" class="one_time_id" name="one_time_id" value="" />
    <tr>
        <td colspan="2">One Time Deduction</td>
        <td class="daily_onetime_name"></td>
        <td><input type="text" class="daily_onetime_deduction" /></td>
    </tr>
    <input type="hidden" class="continuous_id" name="continuous_id" value="" />
    <tr>
        <td colspan="2">Continuous Deduction</td>
        <td class="daily_continuous_name"></td>
        <td><input type="text" class="daily_continuous_deduction" /></td>
    </tr>
    <input type="hidden" class="loan_id" name="loan_id" value="" />
    <tr>
        <td colspan="2">Periodic Deduction</td>
        <td class="daily_periodic_name"></td>
        <td><input type="text" class="daily_periodic_deduction" /></td>
    </tr>
    <tr>
        <td class="remove-retrive"><strong>Contribution's</strong></td>
    </tr>
    <tr class="contribution display-none">
        <td colspan="2">NIS</td>
        <td><input type="text" class="cont_nis" value="" /></td>
    </tr>
    <tr class="contribution display-none">
        <td colspan="2">NHT</td>
        <td><input type="text" class="cont_nht" value="" /></td>
    </tr>
    <tr class="contribution display-none">
        <td colspan="2">ED Tax</td>
        <td><input type="text" class="cont_edtax" value="" /></td>
    </tr>
    <tr class="contribution display-none">
        <td colspan="2">Heart</td>
        <td><input type="text" class="cont_heart" value="" /></td>
    </tr>
    <tr>
    <tr>
        <td colspan="2">Total</td>
        <td></td>
        <td class="total_deduction">$0.00</td>
    </tr>
</tbody>
</table>
</div>
<h4 >Net Pay:<span class="netpay">0.00</span> </h4>
<input type="hidden" class="start_date" >
<input type="hidden" class="end_date" >
<div class="btn-group pull-left" style="margin-right:10px;">
    <form  method="post" action="{{ url('admin/daily_print') }}">
        @csrf
        <input type="hidden" class="pdf-data" name="pdfdata" value="">
        <button  type="submit" style="background-color:gray !important;text-decoration:none" class="btn btn-bitbucket pull-left" ><i class="fa-solid fa-print"></i></button>
    </form>
</div>
<div class="btn-group pull-left">
    <button type="button" class="btn btn-success pull-left daily_single_email">Email</button>
</div>
<div class="btn-group pull-right">
<button type="button" class="btn btn-info pull-right proceed" >Proceed</button>
</div>

</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
  
        if(localStorage.getItem("d_p_s_d") === null && localStorage.getItem("d_p_e_d") === null && localStorage.getItem("d_p_c")=== null && localStorage.getItem("d_p_d")===null)
            {
                localStorage.setItem('d_p_s_d', ""); 
                localStorage.setItem('d_p_e_d', "");
                localStorage.setItem('d_p_c', "");
                localStorage.setItem('d_p_d',"");
                
            }
            else {
                if(localStorage.getItem("d_p_s_d") === "" && localStorage.getItem("d_p_e_d") === "" && localStorage.getItem("d_p_c")=== "" && localStorage.getItem("d_p_d")=== "")
                {
                        var t_c = $(".hidden3").val();
                        var t_d = $(".hidden4").val();
                        if(t_c=="/")
                        {
                            t_c = "";
                        }
                        if(t_d=="/")
                        {
                            t_d = "";
                        }
                        localStorage.setItem("d_p_s_d",$(".d_p_s_d").val());
                        localStorage.setItem("d_p_e_d",$(".d_p_e_d").val());
                        localStorage.setItem("d_p_c",t_c);
                        localStorage.setItem("d_p_d",t_d);
                        $(".d_p_s_d").val(localStorage.getItem("d_p_s_d"));
                        $(".d_p_e_d").val(localStorage.getItem("d_p_e_d"));
                        var d_p_c = localStorage.getItem("d_p_c");
                        $(`.d_p_c option[value=${d_p_c}]`).attr("selected", "selected");
                        var d_p_d = localStorage.getItem("d_p_d");
                        if(d_p_d=="/")
                        {
                            d_p_d = "";
                        }
                        if(d_p_d!="")
                        {
                        $(`.d_p_d option[value=${d_p_d}]`).attr("selected", "selected");
                        }
                }
                else {
                        var temp_c = $(".hidden5").val();
                        if(temp_c==="0")
                        {
                            $(".d_p_s_d").val(localStorage.getItem("d_p_s_d"));
                            $(".d_p_e_d").val(localStorage.getItem("d_p_e_d"));
                            var d_p_c = localStorage.getItem("d_p_c");
                            $(`.d_p_c option[value=${d_p_c}]`).attr("selected", "selected");
                            var d_p_d = localStorage.getItem("d_p_d");
                            if(d_p_d=="/")
                            {
                                d_p_d = "";
                            }
                            if(d_p_d!="")
                            {
                            $(`.d_p_d option[value=${d_p_d}]`).attr("selected", "selected");  
                            }
                            $(".hidden5").val("0");
                        }
                        else {
                            var t_c = $(".hidden3").val();
                            var t_d = $(".hidden4").val();
                            localStorage.setItem("d_p_s_d",$(".d_p_s_d").val());
                            localStorage.setItem("d_p_e_d",$(".d_p_e_d").val());
                            localStorage.setItem("d_p_c",t_c);
                            localStorage.setItem("d_p_d",t_d);
                            $(".d_p_s_d").val(localStorage.getItem("d_p_s_d"));
                            $(".d_p_e_d").val(localStorage.getItem("d_p_e_d"));
                            var d_p_c = localStorage.getItem("d_p_c");
                            $(`.d_p_c option[value=${d_p_c}]`).attr("selected", "selected");
                            var d_p_d = localStorage.getItem("d_p_d");
                            if(d_p_d=="/")
                            {
                                d_p_d = "";
                            }
                            if(d_p_d!="")
                            {
                            $(`.d_p_d option[value=${d_p_d}]`).attr("selected", "selected");
                            }
                            $(".hidden5").val("0");
                        }
                }
                
                
            }
  
        $('document').ready(function() {
            var approve_status;
            var after_income;
            var status;
            var user_id;
            var s_date;
            var e_date;
            var payroll_cycle;
            var loan_flag;
            var loan_amount;
            var ins_val;
            var pause;
        $('.senddata_daily').click(function() {
                    user_id = $(this).attr('user_id');
                    var no_days = $(this).attr('no_days');
                    s_date = $(this).attr('s_date');
                    e_date = $(this).attr('e_date');
                    var cycle = $(this).attr('cycle');
                    var status = $(this).attr('status');
                    loan_flag = $(this).attr('loan_flag');
                    loan_amount = $(this).attr('loan_amount');
                    pause = $(this).attr('pause');
                    ins_val = $(this).attr('ins_val');
                    if(status==0)
                    {
                        
                        $('.stat').html('<i class="fa fa-fw fa-remove" style="color: red;"></i>');
                    }
                    else if(status>0) {
                         
                        $('.stat').html('<i class="fa fa-fw fa-check approve approve" style="color: green;"></i>');
                    }
          if(status>0)
          {
              $.ajax({
                  url:"{{url('daily_processed_atten_get')}}",
                  type:"get",
                  data:{
                      "user_id":user_id,"no_days":no_days,"s_date":s_date,"e_date":e_date,"cycle":cycle
                  },
                  success: function (resutl) {
                      $(".department").html(resutl.process_data[0]["dept"]),
                      $('.daily_rate').html("$"+resutl.user_info[0]["daily_pay"]),
                      $('.first_name').html(resutl.process_data[0]["emp_name"]),
                      $('.trn').html(resutl.user_info[0]["trn"]),$('.nis').html(resutl.user_info[0]["nis"]),
                      $('.daily_total').html("$"+resutl.process_data[0]["total_pay"]),
                      $('.total_daily').html(resutl.process_data[0]["basic_pay"]),
                      $('.rate_daily').html(resutl.user_info[0]["daily_pay"]),
                      $('.sick_hour').html(resutl.process_data[0]["sick_hour"]),
                      $('.sick_rate').html("$"+resutl.user_info[0]["daily_pay"]),$('.sick_total').html("$"+resutl.process_data[0]["sick_pay"]),
                      $('.vacation_hour').html(resutl.process_data[0]["vacation_hour"]),$('.vacation_rate').html("$"+resutl.user_info[0]["daily_pay"]),
                      $('.vacation_total').html("$"+resutl.process_data[0]["vacation_pay"]),$('.maternity_hour').html(resutl.process_data[0]["maternity_hour"]),
                      $('.maternity_rate').html("$"+resutl.user_info[0]["daily_pay"]),$('.maternity_total').html("$"+resutl.process_data[0]["maternity_pay"]),
                      $('.holiday_hour').html(resutl.process_data[0]["holiday_hour"]),$('.holiday_rate').html("$"+resutl.user_info[0]["daily_pay"]),
                      $('.holiday_total').html("$"+resutl.process_data[0]["holiday_pay"]),
                      $('.bonus_name').html(resutl.process_data[0]["bonus_name"]),$('.bonuspay').html("$"+resutl.process_data[0]["bonus"]),
                      $('.nis_update').val(resutl.process_data[0]["ded_nis"]),
                      $('.nht_update').val(resutl.process_data[0]["ded_nht"]),$('.edt_update').val(resutl.process_data[0]["ded_edtax"]),
                      $('.income_tax_update').val(resutl.process_data[0]["income_tax"]),$('.one_time_deduction_name').html(resutl.process_data[0]["onetime_name"]),
                      $('.one_time_deduction_value').val(resutl.process_data[0]["one_time"]),$('.continuous_name').html(resutl.process_data[0]["continuous_name"]),
                      $('.continuous_value').val(resutl.process_data[0]["continuous"]),$('.loan_name').html(resutl.process_data[0]["periodic_name"]),
                      $('.loan_update').val(resutl.process_data[0]["periodic"]),$('.total-deduction').html(""+resutl.process_data[0]["total_deduction"]),
                      $('.netpay').html(""+resutl.process_data[0]["netpay"]),$('.cont_nis').val(resutl.process_data[0]["cont_nis"]),
                      $('.cont_nht').val(resutl.process_data[0]["cont_nht"]),
                      $('.cont_edtax').val(resutl.process_data[0]["cont_edtax"]),$('.cont_heart').val(resutl.process_data[0]["cont_heart"])
                  }
              });
          }
          $.ajax({
              url:"{{url('daily_atten_get')}}",
              type:"get",
              data:{
                  "user_id":user_id,"no_days":no_days,"s_date":s_date,"e_date":e_date,"cycle":cycle
              },
              success: function (resutl) {
                $('.department').html(resutl.dep);
                $('.rate_daily').html("$"+resutl.daily_pay);
                $('.first_name').html(resutl.name);
                $('.trn').html(resutl.trn);
                $('.nis').html(resutl.nis_val);
                $('.no_days').html(resutl.no_days);
                $('.days_no').html(resutl.no_days);
                $('.daily_rate').html(resutl.daily_pay);
                $('.total_daily').html("$"+(parseFloat(resutl.reg_pay-resutl.m_pay-resutl.v_pay-resutl.s_pay-resutl.h_t)-parseFloat(resutl.bonus)));
                $('.daily_total').html(resutl.reg_pay);
                $('.nis_ans').val(resutl.nis);
                $('.cont_nis').val(resutl.nis);
                $('.nht_ans').val(resutl.nht);
                $('.cont_nht').val(resutl.nht);
                $('.edt_ans').val(resutl.edtax);
                $('.cont_edtax').val(resutl.edtax);
                $('.cont_heart').val(resutl.heart);
                $('.daily_pay_total').html("$"+parseInt(resutl.reg_pay));
                $('.user_incometax').val(resutl.income_tax);
                var tot_ded = parseFloat(resutl.nis)+parseFloat(resutl.nht)+parseFloat(resutl.edtax)+parseFloat(resutl.income_tax)+parseFloat(resutl.onetime_amount)+parseFloat(resutl.continuous_val)+parseFloat(resutl.periodic_value);
                $('.total_deduction').html(tot_ded.toFixed(2));
                $('.netpay').html((parseInt(resutl.reg_pay)- parseInt(tot_ded)));
                $('.bbonus').html(resutl.bonus);
                $('.bonuspay').html(resutl.bonus);
                payroll_cycle = resutl.cycle;
                $('.holiday_hour').html(resutl.counter);
               $('.holiday_rate').html('$'+resutl.daily_pay);
               $('.holiday_total').html('$'+resutl.h_t);
                $('.user_loan').html(loan_amount);
                $('.maternity_hour').html(resutl.m_days);
                $('.maternity_rate').html('$'+resutl.daily_pay);
                $('.maternity_total').html(resutl.m_pay);
                $('.vacation_hour').html(resutl.v_days);
                $('.vacation_rate').html('$'+resutl.daily_pay);
                $('.vacation_total').html(resutl.v_pay);
                $('.sick_hour').html(resutl.s_days);
                $('.sick_rate').html('$'+resutl.daily_pay);
                $('.sick_total').html(resutl.s_pay);
                $('.daily_onetime_name').html(resutl.onetime_name);
                $('.one_time_id').val(resutl.one_time_id);
                $('.continuous_id').val(resutl.continuous_id);
                $('.loan_id').val(resutl.periodic_id);
                $('.daily_onetime_deduction').val(resutl.onetime_amount);
                $('.daily_continuous_name').html(resutl.continuous_name);
                $('.daily_continuous_deduction').val(resutl.continuous_val);
                $('.daily_periodic_name').html(resutl.periodic_name);
                $('.daily_periodic_deduction').val(resutl.periodic_value);
                $('.bonus_name').html(resutl.bonus_name);
                let tempData = user_id+':'+no_days+':'+s_date+':'+e_date+':'+cycle;
                $('.daily_single_email').attr('dailydata',tempData);
                $('.pdf-data').val(tempData);
              }
          });
         });

 $('.proceed').click(function() {
                var id = user_id;
                var s_d = s_date;
                var e_d = e_date;
                var dep = $('.department').text();
                var daily_rate = $('.daily_rate').text();
                var name = $('.first_name').text();
                var trn = $('.trn').text();
                var nis = $('.nis').text();
                var no_days = $('.no_days').text();
                var reg_pay = $('.total_daily').text();
                var temp_reg_pay = reg_pay.replace("$","");
                reg_pay = temp_reg_pay; 
                var inc_nis = $('.nis_ans').val();
                var inc_nht = $('.nht_ans').val();
                var inc_edt = $('.edt_ans').val();
                var gross_pay = $('.daily_pay_total').text();
                var tempGrs = gross_pay.replace("$","");
                gross_pay = tempGrs;
                var inc_tax = $('.user_incometax').val();
                var tot_ded = $('.total_deduction').text();
                var net_pay = $('.netpay').text();
                var bonus = $('.bonuspay').text();


 $.ajax({
              url:"{{url('admin/daily_proceed')}}",
              type:"get",
              data:{
                  "user_id":id,"start_date":s_d,"end_date":e_d,
                  'dept':dep,'cycle':payroll_cycle,'emp_name':$('.first_name').html(),
                  'trn':$('.trn').html(),'nis':$('.nis').html(),'no_days':$('.days_no').html(),
                  'daily_rate':$('.daily_rate').html(),'total_reg':$('.total_daily').html(),
                  'bonus_name':$('.bonus_name').html(),'bonus_pay':$('.bonuspay').html(),
                  'sick_day':$('.sick_hour').html(),'gross_pay':$('.daily_pay_total').html(),
                  'sick_total':$('.sick_total').html(),'vacation_day':$('.vacation_hour').html(),
                  'vacation_total':$('.vacation_total').html(),'maternity_day':$('.maternity_hour').html(),
                  'maternity_total':$('.maternity_total').html(),'holiday_day':$('.holiday_hour').html(),
                  'holiday_total':$('.holiday_total').html(),'ded_nis':$('.nis_ans').val(),
                  'ded_nht':$('.nht_ans').val(),'ed_tax':$('.edt_ans').val(),
                  'income_tax':$('.user_incometax').val(),'one_time_name':$('.daily_onetime_name').html(),
                  'one_time_ded':$('.daily_onetime_deduction').val(),'continuous_name':$('.daily_continuous_name').html(),
                  'continuous_ded':$('.daily_continuous_deduction').val(),'periodic_name':$('.daily_periodic_name').html(),
                  'periodic_ded':$('.daily_periodic_deduction').val(),'total_deduction':$('.total_deduction').html(),
                  'netPay':$('.netpay').html()
                 ,'contr_nis':$('.cont_nis').val(),'contr_nht':$('.cont_nht').val(),
                 'contr_edtax':$('.cont_edtax').val(),'heart':$('.cont_heart').val(),'one_time_id':$('.one_time_id').val(),
                 'continuous_id':$('.continuous_id').val(),'loan_id':$('.loan_id').val()

     },
              success: function (resutl) {
                  if(resutl==0) {
                      toastr.success("Payroll Added");
                  }
                  else {
                    toastr.error("Payroll Already Exist");
                  }
              }
          });
        });



        });
        </script>
        
        <script>
            $('document').ready(function (){
                $('.daily_single_email').click(function (){
                    var dailyData = $('.daily_single_email ').attr('dailydata');
                    var tempDaily = dailyData.split(':');
                    $.ajax({
                        url:"{{url('admin/daily_single_email')}}",
                        type:"get",
                        data:{
                            "user_id":tempDaily[0],"no_days":tempDaily[1],"start_date":tempDaily[2],"end_date":tempDaily[3],'cycle':tempDaily[4]
                        },
                        success: function (res){
                            if(res.status=='1')
                            {
                                toastr.success('Email Not Found');
                            }
                            else if(res.status=='2')
                            {
                                toastr.success('First Process the Period');
                            }
                            else {
                                toastr.success('Email Sent Succesfully');
                            }
                        }
                    })
                })
            })
        </script>

        
        <script>
        
            function updateValues()
            {
                var update_nis = $('.nis_ans').val();
                    var update_nht = $('.nht_ans').val();
                    var update_edt = $('.edt_ans').val();
                    var update_inc_tax = $('.user_incometax').val();
                    var loan_up = $('.daily_periodic_deduction').val();
                    var one_time = $('.daily_onetime_deduction').val();
                    var continuous = $('.daily_continuous_deduction').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous)+parseFloat(loan_up);
                    
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total_deduction').text(update_sum_deductions); 
                    var tot_sal = $('.daily_pay_total').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
            }
            $(".nis_ans").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
            $(".nht_ans").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
            $(".edt_ans").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
            $(".user_incometax").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
            $(".daily_onetime_deduction").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
            $(".daily_continuous_deduction").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
            $(".daily_periodic_deduction").keypress(function() {
                if(event.which==13)
                {
                    updateValues();
                }
            });
        </script>
@endsection