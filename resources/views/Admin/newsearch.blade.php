@extends('layouts.admin')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .display-none {
            display:none;
        }
    </style>
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
                                        <h3 class="box-title">Employee Payroll</h3>
                                        <hr>
                                        <form  method="post" action="{{ url('admin/search') }}">
                                            @csrf
                                            <input type="hidden" class="hidden_input" value={{$cycle}} />
                                            <input type="hidden" class="hidden_input2" value={{$dep}} />
                                            <div class="form-group">
                                                <label for="first-name-icon">Cycle</label>

                                                <div class="position-relative has-icon-left">
                                                    <select class="form-control cycle h_p_c" name="cycle" placeholder="Cycle" required>
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

                                            <div class="form-group">
                                                <label for="start_date">Start Date</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" id="date-input" class="form-control h_p_s_d"
                                                           name="start_date" placeholder="Start Date" value={{$startdate}} required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar "></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="start_date">End Date</label>
                                                <div class="position-relative has-icon-left">
                                                    <input type="date" id="date-input" class="form-control h_p_e_d"
                                                           name="end_date" placeholder="End Date"  value={{$end_date}} required>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-calendar "></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="first-name-icon">Dept</label>

                                                <div class="position-relative has-icon-left">
                                                    <select type="text"  name="DEPARTMENT" list="Weekly" id="first-name-icon department-dropdown"
                                                            class="form-control h_p_d"  placeholder="Dept" >
                                                        <option value="-99">Select Department</option>

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


                                            <div class="btn-group pull-left">
                                                <button type="reset" class="btn btn-warning hard-reset-2 pull-right">Reset</button>
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
                                                    <th scope="col">Hrs.</th>
                                                    <th scope="col">Processed</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>




                                                @php
                                                    use Illuminate\Support\Str;
                                                    for($i = 0; $i<count($userArray)-1; $i+=1)

                                                    { @endphp

                                                <tr>
                                                    @php $tempId = $userArray[$i]; @endphp
                                                    <td scope="row">@php echo $userArray[$i+=1] @endphp</td>


                                                    <td scope="row">@php

                                                            $payroll_cycle = $cycle;
                                                             $id = $userArray[$i-1];
                                                            $basic = $userArray[$i+=1];
                                                            $overTime = $userArray[$i+=1];
                                                            $ip=intval($basic);

                                                            $ip2 = intval($overTime);
                                                            $bmin=gmdate("",$ip);

                                                            $hoursBasictime=$ip/3600;

                                                            $forBasicMin = floor($hoursBasictime);
                                                            $mulBasic = $forBasicMin*3600;
                                                            $totBasicMin = $ip-$mulBasic;
                                                            $minBasic = $totBasicMin/60;


                                                            $hoursOverTime = $ip2/3600;
                                                            $basicOverMin = floor($hoursOverTime); //Total INT Hour divided *3600
                                                            $totOverSec = $basicOverMin*3600;
                                                            $totOverMinSec = $ip2-$totOverSec;
                                                            $totOverTimeMin = $totOverMinSec/60;
                                                            $totalhours=$forBasicMin+$hoursOverTime;


                                                            $tempTotMin = $minBasic + $totOverTimeMin;
                                                            $TotMin = 0.0;
                                                            if($tempTotMin>=59) {
                                                                $TotMin = $tempTotMin-59;
                                                                $totalhours+=1;
                                                            }
                                                            else {
                                                                $TotMin = $tempTotMin;
                                                            }
                                                                $totalMinFinal = 0;
                                                                if(floor($totalhours)>80) {
                                                                    $hoursOverTime =(floor($totalhours))-80;
                                                                    $minBasic = 0;
                                                                    $hoursBasictime = 80;
                                                                    $totOverTimeMin = floor($TotMin);

                                                                }
                                                                else {
                                                                    $hoursOverTime = 0;
                                                                    $totOverTimeMin = 0;
                                                                    $minBasic = $TotMin;
                                                                    $hoursBasictime = $totalhours;
                                                                }

                                                                $check_status = App\Models\Proceed::where("user_id",$id)->where('start_date',$startdate)
                                                                ->where('end_date',$end_date)->count();

                                                                $atten_get = App\Models\User::where('id', $id)->first();


                                                                // Regular Hour's Pay Calculation's
                                                                $hourlyRate = $atten_get->hourly_rate;
                                                                $overTimeRate = $atten_get->ot_rate;
                                                                $hRATE = intval($hourlyRate);
                                                                $oRATE = intval($overTimeRate);

                                                                $hoursREGPAY = floor($hoursBasictime) * $hRATE;
                                                                $userBasicMin = ($hRATE / 60) * floor($minBasic);
                                                                $total_basic_pay_rate = $hoursREGPAY + $userBasicMin;

                                                                $Overtimepay = floor($hoursOverTime) * $oRATE;
                                                                $overtimeminutespay = ($oRATE / 60) * floor($totOverTimeMin);
                                                                $total_basic_pay = $Overtimepay + $overtimeminutespay;

                                                                $totalUserSalary = $total_basic_pay_rate + $total_basic_pay;


                                                                $USERNIS = App\Models\Deduction::select('nis_fix_value')->where('name','nis')->get()->toArray();
                                                                $USERNIS = intval($USERNIS[0]['nis_fix_value']);
                                                                $userSalaryNis = ($totalUserSalary/100)*$USERNIS;


                                                                $period_bonus = App\Models\Bonuse::where('start_date',$startdate)->where('end_date',$end_date)->where('user_id',$id)->first();
                                                                $p_bonus = 0;
                                                                if($period_bonus==null)
                                                                {
                                                                    $p_bonus = 0;
                                                                }
                                                                else {
                                                                    $p_bonus = intval($period_bonus->bonus);
                                                                }
                                                                $tot_sal = 0;
                                                                $tot_nis = 0;
                                                                $income = 0;
                                                                $inc_tax = 0;

                                                                $c = intval($cycle);
                                                                        $loan_amount = 0;
                                                                        $ins_val = 0;
                                                                        $loan_flag = false;
                                                                        $pause = -1;
                                                                        $laon_data = DB::table('loan')->where('user_id',$id)->where('cycle',$c)->where('start_date','>=',$startdate)->where('end_date','>=',$end_date)->get()->toArray();
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
                                                                $TotMin = round((floatval($TotMin)/60),2);
                                                                $minBasic = round((floatval($minBasic)/60),2);
                                                                $totOverTimeMin = round((floatval($totOverTimeMin)/60),2);
                                                                if(Str::contains($TotMin, '.'))
                                                                {

                                                                    $TotMin = Str::replace("0.", "", $TotMin);
                                                                }
                                                                $savedHour = floor($totalhours).".".$TotMin;
                                                                $tempData = floor($totalhours).":".$TotMin.",".floor($hoursBasictime).":".$minBasic.",".floor($hoursOverTime).":".$totOverTimeMin;

                                                        @endphp
                                                        <input type="text" class="input_values_data" style="width:80px;" value={{$savedHour}} id={{"PHour.".$tempId}} data-hours={{$tempData}} data-cycle={{ $payroll_cycle }} />
                                                    </td>
                                                    @if($check_status>0)

                                                        <td><i class="fa fa-fw fa-check approve" style="color: green;"></i></td>
                                                    @else
                                                        <td><i class="fa fa-fw fa-remove not-approve" style="color: rgb(250, 21, 40);"></i></td>

                                                    @endif
                                                    <td><button class="btn btn-info senddata btn-sm" status={{ $check_status }} total_sal={{$totalUserSalary}} pause={{ $pause }} ins_val={{ $ins_val }} loan_flag={{$loan_flag}}  loan_amount={{$loan_amount}} incometax={{ $inc_tax }} user_id={{ $id }} totalhours="{{ floor($totalhours)  }}"    totalm="{{ $TotMin }}"
                                                                hoursBasihourctime="{{ floor($hoursBasictime) }}"  minBasic="{{ $minBasic}}"   cycle={{ $payroll_cycle }}  hoursOverTime={{  floor($hoursOverTime) }}  totOverTimeMin="{{ $totOverTimeMin }}"><i
                                                                    class="fa fa-fw fa-eye"></i></button></td>


                                                </tr>



                                                @php  } @endphp



                                                </tbody>
                                            </table>

                                            <!--<button type="button" style="background-color:green !important" class="btn email-all btn-info pull-left" >Email All</button>-->

                                            <button type="button" style="background-color:green !important" class="btn process-all btn-info pull-right" >Process All</button>

                                        </div>



                                    </div>
                                    <div class="col-md-6">
                                        <div class="">

                                            <h3 class="box-title">Department &amp; Rate</h3>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-6 col-xs-6">Department: </div>
                                                <div class="col-md-3 col-sm-6 col-xs-6"><strong class="department">Department Name</strong></div>
                                                <div class="col-md-3 col-sm-6 col-xs-6">Regular Hours: </div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 regular_hours">0</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6">Overtime Rate: </div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 over_time_rate">$0</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 ">Hourly Rate: </div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 hourly_rate hourly_r">$0</div>

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

                                                <div class="col-md-3 col-sm-6 col-xs-6">Work Hours: </div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 total_work_hours_and_minits">0.00</div>

                                                <div class="col-md-3 col-sm-6 col-xs-6">Reg Pay:</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 total_basic_pay">$0</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 ">OT Pay:</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6 total_over_time_pay">$0</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6">Bonus:</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6">$0</div>
                                                <div class="col-md-3 col-sm-6 col-xs-6">Stat:<div class="user_stat"></div></div>
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
                                                            <th>Hr/Day</th>
                                                            <th>Rate</th>
                                                            <th>Total</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Basic Pay</th>
                                                            <td class="total_work_Basic_hours_and_minits">0.00</td>
                                                            <td class="hourly_rate">$0.00</td>
                                                            <td class="total_basic_pay total_basic_pa">$0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Attn.Inc</th>
                                                            <td class="atten_hour">0.00</td>
                                                            <td class="atten_rate">0.00</td>
                                                            <td class="atten_total">$0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Sick Leave Pay</th>
                                                            <td class="sick_hour">0.00</td>
                                                            <td class="sick_rate">0.00</td>
                                                            <td class="sick_total sick_tol">$0.00</td>
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
                                                            <th>Overtime</th>
                                                            <td class="total_work_Over_hours_and_minits">0.00</td>
                                                            <td class="over_time_rate overtime_r">$0.00</td>
                                                            <td class="total_over_time_pay total_overtime_pay ">$0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Bonus</th>
                                                            <td class="bonus_name">Period Bonus</td>
                                                            <td class=""></td>
                                                            <td class="bonuspay">$0.00</td>
                                                        </tr>

                                                        <tr>
                                                            <th>Total</th>
                                                            <td></td>
                                                            <td class="rate"></td>
                                                            <td class="sum_basic_and_over_pay">$0.00</td>

                                                        </tr>


                                                        </tbody>
                                                    </table>
                                                </div>
                                                <h4>Deductions</h4>
                                                <div class="table-responsive no-padding">
                                                    <table class="table table-bordered" width="100%">
                                                        <tbody>
                                                        <tr>
                                                            <th colspan="3">Reason</th>
                                                            <th>Name</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                        <input type="hidden" class="user_id">
                                                        <tr>
                                                            <td colspan="3">NIS</td>
                                                            <td>NIS</td>
                                                            <td class="NIS_ANS"><input type="text" class="nis_update" value="" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">NHT</td>
                                                            <td>NHT</td>
                                                            <td class="NIS_NHT"><input type="text" class="nht_update" value="" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">ED TAX</td>
                                                            <td>ED Tax</td>
                                                            <td class="NIS_EDT"><input type="text" class="edt_update" value="" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">INCOME TAX</td>
                                                            <td>Income Tax</td>
                                                            <td class="user_incometax"><input type="text" class="income_tax_update" value="" /></td>
                                                        </tr>
                                                        <input type="hidden" class="one_time_id" name="one_time_id" value="" />
                                                        <tr>
                                                            <td colspan="3">One Time Deduction</td>
                                                            <td class="one_time_deduction_name"></td>
                                                            <td ><input type="text" class="one_time_deduction_value" value="" /></td>
                                                        </tr>
                                                        <input type="hidden" class="continuous_id" name="continuous_id" value="" />
                                                        <tr>
                                                            <td colspan="3">Continuous Deduction</td>
                                                            <td class="continuous_name"></td>
                                                            <td ><input type="text" class="continuous_value" value="" /></td>
                                                        </tr>
                                                        <input type="hidden" class="loan_id" name="loan_id" value="" />
                                                        <tr>
                                                            <td colspan="3">Periodic Deduction</td>
                                                            <td class="loan_name"></td>
                                                            <td class="user_loan"><input type="text" class="loan_update" value="" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="remove-retrive"><strong>Contribution's</strong></td>
                                                        </tr>
                                                        <tr class="contribution display-none">
                                                            <td colspan="3">NIS</td>
                                                            <td>NIS</td>
                                                            <td><input type="text" class="cont_nis" value="" /></td>
                                                        </tr>
                                                        <tr class="contribution display-none">
                                                            <td colspan="3">NHT</td>
                                                            <td>NHT</td>
                                                            <td><input type="text" class="cont_nht" value="" /></td>
                                                        </tr>
                                                        <tr class="contribution display-none">
                                                            <td colspan="3">ED Tax</td>
                                                            <td>ED Tax</td>
                                                            <td><input type="text" class="cont_edtax" value="" /></td>
                                                        </tr>
                                                        <tr class="contribution display-none">
                                                            <td colspan="3">Heart</td>
                                                            <td>Heart</td>
                                                            <td><input type="text" class="cont_heart" value="" /></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">Total</td>
                                                            <td></td>
                                                            <td class="total-deduction">$0.00</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <h4 >Net Pay:<span class="netpay">0.00</span> </h4>
                                                <input type="hidden" class="start_date" value="{{$startdate}}">
                                                <input type="hidden" class="end_date" value="{{$end_date}}">
                                                <div class="btn-group pull-left" style="margin-right:10px;">
                                                    <form  method="post" action="{{ url('admin/hourly_print') }}">
                                                        @csrf
                                                        <input type="hidden" class="print_s_d" name="print_s_d" value="{{$startdate}}">
                                                        <input type="hidden" class="print_e_d" name="print_e_d" value="{{$end_date}}">
                                                        <input type="hidden" class="print_u_id" name="print_u_id" value="">
                                                        <input type="hidden" class="print_data" name="print_data" value="">
                                                        <input type="hidden" class="print_cycle" name="print_cycle" value="">
                                                        <button  type="submit" style="background-color:gray !important;text-decoration:none" class="btn btn-info pull-left" ><i class="fa-solid fa-print"></i></button>
                                                    </form>
                                                </div>

                                                <button type="button" class="btn btn-primary email_get" data-toggle="modal" data-target="#exampleModal">
                                                    Email
                                                </button>

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


            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Send Email</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="btn-group pull-left">
                                <form method="post" action="{{ route('admin.single.email')}}">
                                    @csrf
                                    <input type="hidden" class="email_u_id" name="email_u_id" value="">
                                    <input type="hidden" class="print_s_d" name="print_s_d" value="{{$startdate}}">
                                    <input type="hidden" class="print_e_d" name="print_e_d" value="{{$end_date}}">
                                    <input type="hidden" class="print_u_id" name="print_u_id" value="">
                                    <input type="hidden" class="print_cycle" name="print_cycle" value="">
                                    <input type="text" style="width:25rem;" class="email_pass form-control" name="email_pass" value="">
                                    <button type="submit" style="background-color:green !important" class="btn btn-info pull-left" >Email</button>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script>
            $('.email_get').click(function(){
                var use_id = $('.print_u_id').val();
                $.ajax({
                    url:"{{url('email_pass')}}",
                    type:"get",
                    data:{
                        "user_id":use_id,
                    },
                    success: function(res){
                        $('.email_pass').val(res.email_);
                    }

                })
            })
        </script>
        <script>


            var basic_hour = $('.total_work_Basic_hours_and_minits').html();
            var basic_pay = $('.total_basic_pay').html();
            var overtime_hour = $('.total_work_Over_hours_and_minits').html();
            var overtime_pay = $('.total_over_time_pay').html();
            var sick_hour = $('.sick_hour').html();
            var sick_pay = $('.sick_total').html();
            var vacation_hour = $('.vacation_hour').html();
            var vacation_pay = $('.vacation_total').html();
            var maternity_hour = $('.maternity_hour').html();
            var maternity_pay = $('.maternity_total').html();
            var holiday_hour = $('.holiday_hour').html();
            var holiday_total = $('.holiday_total').html();
            var onetime_name = $('.one_time_deduction_name').html();
            var onetime_deduction = $('.one_time_deduction_value').val();
            var continuous_name = $('.continuous_name').html();
            var continuous_deduction = $('.continuous_value').val();
            var periodic_name = $('.loan_name').html();
            var periodic_deduction = $('.loan_update').val();
            var bonus = $('.bonuspay').html();
            var nis = $('.nis_update').val();
            var nht = $('.nht_update').val();
            var edTax = $('.edt_update').val();

            var arr = [];
            arr.push(basic_hour,basic_pay,overtime_hour,overtime_pay,sick_hour,sick_pay,vacation_hour,vacation_pay,maternity_hour,maternity_pay,holiday_hour,holiday_total,onetime_name,onetime_deduction,continuous_name,continuous_deduction,periodic_name,periodic_deduction,bonus,nis,nht,edTax);
            $('.print_data').attr("value",arr);


            $(".hard-reset-2").click(function(){
                localStorage.setItem('h_p_s_d', "");
                localStorage.setItem('h_p_e_d', "");

                var s_date = localStorage.getItem('h_p_s_d');
                $(".h_p_s_d").attr("value",s_date);
                var e_date = localStorage.getItem('h_p_e_d');
                $(".h_p_e_d").attr("value",e_date);
                var cycle = localStorage.getItem('h_p_c');
                $(`.h_p_c option[value=${cycle}]`).removeAttr("selected");
                var deP_ = localStorage.getItem('h_p_d');
                $(`.h_p_d option[value=${deP_}]`).removeAttr("selected");
                localStorage.setItem('h_p_c', "");
                localStorage.setItem('h_p_d',"");
                $(`.h_p_c option[value=${"1"}]`).attr("selected", "selected");
                $(`.h_p_d option[value=${"-99"}]`).attr("selected", "selected");
            });

            if(localStorage.getItem("h_p_s_d")==="" && localStorage.getItem("h_p_s_d")==="" && localStorage.getItem("h_p_c")==="" && localStorage.getItem("h_p_d")==="")
            {
                var s_d = $(".h_p_s_d").attr("value");
                var e_d = $(".h_p_e_d").attr("value");
                var cycle = $(".hidden_input").val();
                var deP_ = $(".hidden_input2").val();
                $(`.h_p_c option[value=${cycle}]`).attr("selected", "selected");
                $(`.h_p_d option[value=${deP_}]`).attr("selected", "selected");
                localStorage.setItem("h_p_s_d",s_d);
                localStorage.setItem("h_p_e_d",e_d);
                localStorage.setItem("h_p_c",cycle);
                localStorage.setItem("h_p_d",deP_);
            }
            else {
                localStorage.setItem("h_p_s_d",$(".h_p_s_d").attr("value"));
                localStorage.setItem("h_p_e_d",$(".h_p_e_d").attr("value"));
                $(".h_p_s_d").attr("value",localStorage.getItem("h_p_s_d"));
                $(".h_p_e_d").attr("value",localStorage.getItem("h_p_e_d"));
                var cycle = $(".hidden_input").val();
                var deP_ = $(".hidden_input2").val();
                localStorage.setItem("h_p_c",cycle);
                localStorage.setItem("h_p_d",deP_);
                $(`.h_p_c option[value=${cycle}]`).attr("selected", "selected");
                $(`.h_p_d option[value=${deP_}]`).attr("selected", "selected");
            }
            function updateRecords(e)
            {
                const id = e.split(".");
                $("body").on("change",function(){
                    const tempChange = $("#PHour.42").val();
                })
                var element = document.getElementById('PHour.'+id[1]);
                var dataID = element.getAttribute('data-hours');
                var hoursData = dataID.split(",");
                var total = hoursData[0];
                var basic = hoursData[1];
                var overtime = hoursData[2];

            }
        </script>
        <script>
            $('document').ready(function() {
                var approve_status;
                var after_income;
                var payroll_cycle;
                var loan_flag;
                var loan_amount;
                var ins_val;
                var pause;
                $('.senddata').click(function() {
                    var start_date=$('.start_date').val();
                    var end_date=$('.end_date').val();
                    var tot_salary = $(this).attr('total_sal');
                    var user_id=$(this).attr('user_id');
                    var totalhours=$(this).attr('totalhours');
                    var totalm=$(this).attr('totalm');
                    var hoursBasihourctime=$(this).attr('hoursBasihourctime');
                    var cycle = $(this).attr('cycle');
                    var minBasic=$(this).attr('minBasic');
                    loan_flag = $(this).attr('loan_flag');
                    loan_amount = $(this).attr('loan_amount');
                    pause = $(this).attr('pause');
                    ins_val = $(this).attr('ins_val');
                    var hoursOverTime=$(this).attr('hoursOverTime');

                    var totOverTimeMin=$(this).attr('totOverTimeMin');
                    var incometax=$(this).attr('incometax');
                    var after_income=$(this).attr('incometax');
                    after_income = parseFloat(after_income);
                    var status = $(this).attr('status');
                    if(status==0) {
                        $('.user_stat').html('<i class="fa fa-fw fa-remove" style="color: red;"></i>');
                        var approve_status = $('.user_stat').html();
                    }
                    else if(status>0) {
                        $('.user_stat').html('<i class="fa fa-fw fa-check approve approve" style="color: green;"></i>');
                        var approve_status = $('.user_stat').html();
                    }

                    if(status>0)
                    {
                        $.ajax({
                            url:"{{url('processed_atten_get')}}",
                            type:"get",
                            data:{
                                "user_id":user_id,"start_date":start_date,"end_date":end_date,'cycle':cycle
                            },
                            success: function (resutl) {
                                $(".department").html(resutl.dep_name),$('.regular_hours').html("80"),$('.over_time_rate').html("$"+resutl.overtime_rate),$('.hourly_rate').html("$"+resutl.hourly_rate),$('.first_name').html(resutl.emp_name),$('.trn').html(resutl.trn),$('.nis').html(resutl.nis),$('.total_work_hours_and_minits').html(resutl.work_hours),$('.total_basic_pay').html("$"+resutl.reg_pay),$('.total_over_time_pay').html("$"+resutl.overtime_pay),$('.total_work_Basic_hours_and_minits').html(resutl.basic_hour),$('.atten_hour').html(resutl.basic_hour),$('.atten_rate').html("$"+resutl.atten_rate),$('.atten_total').html("$"+resutl.atten_pay),$('.sick_hour').html(resutl.sick_hour),$('.sick_rate').html("$"+resutl.hourly_rate),$('.sick_total').html("$"+resutl.sick_pay),$('.vacation_hour').html(resutl.vacation_hour),$('.vacation_rate').html("$"+resutl.hourly_rate),$('.vacation_total').html("$"+resutl.vacation_pay),$('.maternity_hour').html(resutl.maternity_hour),$('.maternity_rate').html("$"+resutl.hourly_rate),$('.maternity_total').html("$"+resutl.maternity_pay),$('.holiday_hour').html(resutl.holiday_hour),$('.holiday_rate').html("$"+resutl.hourly_rate),$('.holiday_total').html("$"+resutl.holiday_pay),$('.total_work_Over_hours_and_minits').html(resutl.overtime_hour),$('.bonus_name').html(resutl.bonus_name),$('.bonuspay').html("$"+resutl.bonus),$('.sum_basic_and_over_pay').html("$"+resutl.total_pay),$('.nis_update').val(resutl.ded_nis),$('.nht_update').val(resutl.ded_nht),$('.edt_update').val(resutl.ded_edtax),$('.income_tax_update').val(resutl.income_tax),$('.one_time_deduction_name').html(resutl.onetime_name),$('.one_time_deduction_value').val(resutl.one_time),$('.continuous_name').html(resutl.continuous_name),$('.continuous_value').val(resutl.continuous),$('.loan_name').html(resutl.periodic_name),$('.loan_update').val(resutl.periodic),$('.total-deduction').html(""+resutl.total_deduction),$('.netpay').html(""+resutl.netpay),$('.cont_nis').val(resutl.cont_nis),$('.cont_nht').val(resutl.cont_nht),$('.cont_edtax').val(resutl.cont_edtax),$('.cont_heart').val(resutl.cont_heart)


                                var basic_hour = $('.total_work_Basic_hours_and_minits').html();
                                var basic_pay = $('.total_basic_pay').html();
                                var overtime_hour = $('.total_work_Over_hours_and_minits').html();
                                var overtime_pay = $('.total_over_time_pay').html();
                                var sick_hour = $('.sick_hour').html();
                                var sick_pay = $('.sick_total').html();
                                var vacation_hour = $('.vacation_hour').html();
                                var vacation_pay = $('.vacation_total').html();
                                var maternity_hour = $('.maternity_hour').html();
                                var maternity_pay = $('.maternity_total').html();
                                var holiday_hour = $('.holiday_hour').html();
                                var holiday_total = $('.holiday_total').html();
                                var onetime_name = $('.one_time_deduction_name').html();
                                var onetime_deduction = $('.one_time_deduction_value').val();
                                var continuous_name = $('.continuous_name').html();
                                var continuous_deduction = $('.continuous_value').val();
                                var periodic_name = $('.loan_name').html();
                                var periodic_deduction = $('.loan_update').val();
                                var bonus = $('.bonuspay').html();
                                var nis = $('.nis_update').val();
                                var nht = $('.nht_update').val();
                                var edTax = $('.edt_update').val();

                                var arr = [];
                                arr.push(basic_hour,basic_pay,overtime_hour,overtime_pay,sick_hour,sick_pay,vacation_hour,vacation_pay,maternity_hour,maternity_pay,holiday_hour,holiday_total,onetime_name,onetime_deduction,continuous_name,continuous_deduction,periodic_name,periodic_deduction,bonus,nis,nht,edTax);
                                $('.print_data').attr("value",arr);
                                $('.email_u_id').attr("value",user_id);
                                $('.print_u_id').attr("value",user_id);
                                $('.print_cycle').attr("value",cycle);
                            }
                        });
                    }
                    else {

                        $.ajax({
                            url:"{{url('atten_get')}}",
                            type:"get",
                            data:{
                                "user_id":user_id,"start_date":start_date,"end_date":end_date,"totalhours":totalhours,"totalm":totalm,"hoursBasihourctime":hoursBasihourctime,
                                "minBasic":minBasic, 'hoursOverTime':hoursOverTime,'totOverTimeMin':totOverTimeMin,'incometax':incometax,'total_sala':tot_salary, 'cycle':cycle
                            },
                            success: function (resutl) {
                                if(resutl.status==2){
                                    toastr.error("Accumulate Threshold Does not Exist for Fortnightly Payroll Update the Threshold!!");
                                }
                                if(resutl.status==3){
                                    toastr.error("Accumulate Threshold Does not Exist for Monthly Payroll Update the Threshold!!");
                                }
                                else {
                                    $('.department').html(resutl.department);
                                    $('.first_name').html(resutl.first_name);
                                    $('.totalhors').html(resutl.totalhors);
                                    $('.over_time_rate').html('$'+resutl.over_time_rate);
                                    $('.hourly_rate').html('$'+resutl.hourly_rate);
                                    $('.trn').html(resutl.trn);
                                    $('.nis_update').val(resutl.nis);
                                    $('.cont_nis').val(resutl.nis);
                                    $('.nht_update').val(resutl.nht_val);
                                    $('.cont_nht').val(resutl.nht_val);
                                    $('.edt_update').val(resutl.edTax_val);
                                    $('.cont_edtax').val(resutl.edTax_val);
                                    $('.cont_heart').val(resutl.heart);
                                    $('.bonuspay').html('$'+resutl.bonusPay);
                                    $('.one_time_deduction_name').html(resutl.one_time_name);
                                    $('.one_time_deduction_value').val(resutl.one_time_value);
                                    $('.one_time_id').val(resutl.one_time_id);
                                    $('.continuous_name').html(resutl.continuous_name);
                                    $('.continuous_value').val(resutl.continuous_value);
                                    $('.continuous_id').val(resutl.continuous_id);
                                    $('.loan_id').val(resutl.periodic_id);
                                    $('.total_work_hours_and_minits').html(resutl.total_work_hours_and_minits);
                                    $('.total_basic_pay').html('$'+resutl.total_basic_pay);
                                    $('.total_over_time_pay').html('$'+resutl.total_over_time_pay);

                                    $('.regular_hours').html(resutl.regular_hours);

                                    $('.totalbasichours').html('$'+resutl.totalbasichours);
                                    $('.total_work_Over_hours_and_minits').html(resutl.total_work_Over_hours_and_minits);

                                    $('.total_work_Basic_hours_and_minits').html(resutl.total_work_Basic_hours_and_minits);
                                    $('.totalovertime').html('$'+resutl.totalovertime);
                                    $('.user_id').val(resutl.id_user);
                                    $('.sum_basic_and_over_pay').html('$'+resutl.sum);
                                    $('.income_tax_update').val(resutl.user_incometax);
                                    var hour_min = resutl.basic_hour+":"+resutl.basic_min;
                                    $('.atten_hour').html(hour_min);
                                    $('.atten_rate').html("$"+resutl.atten_rate);
                                    $('.atten_total').html("$"+resutl.atten_inc);
                                    payroll_cycle = resutl.cycle;
                                    $('.holiday_hour').html(resutl.h_t_h+":"+resutl.h_t_m);
                                    $('.holiday_rate').html('$'+resutl.hourly_rate);
                                    $('.holiday_total').html('$'+resutl.h_t);
                                    $('.loan_name').html(resutl.periodic_name);
                                    $('.loan_update').val(resutl.periodic_value);
                                    loan_amount = resutl.periodic_value;
                                    $('.sick_hour').html(resutl.sick_hour+':0');
                                    $('.sick_rate').html('$'+resutl.hourly_rate);
                                    $('.sick_total').html('$'+resutl.sick_pay);

                                    $('.vacation_hour').html(resutl.vacation_hour+':0');
                                    $('.vacation_rate').html('$'+resutl.hourly_rate);
                                    $('.vacation_total').html('$'+resutl.vacation_pay);
                                    $('.maternity_hour').html(resutl.m_hour+':'+resutl.m_min);
                                    $('.maternity_rate').html('$'+resutl.hourly_rate);
                                    $('.maternity_total').html('$'+resutl.m_pay);
                                    $('.bonus_name').html(resutl.bonus_name);


                                    var cal_nis = $('.nis_update').val();
                                    var cal_nht = $('.nht_update').val();
                                    var cal_edt = $('.edt_update').val();
                                    var tot_sal = $('.sum_basic_and_over_pay').text().split("$");
                                    var after_income = resutl.user_incometax;
                                    // after_income = tempIncomeTax.toFixed(2);
                                    var sum_deductions = parseFloat(cal_edt)+parseFloat(cal_nis)+parseFloat(cal_nht)+parseFloat(after_income)+parseFloat($('.one_time_deduction_value').val())+parseFloat($('.continuous_value').val())+parseFloat($('.loan_update').val());


                                    sum_deductions = sum_deductions.toFixed(2);
                                    $('.total-deduction').text(sum_deductions);


                                    var net_pay = tot_sal[1] - sum_deductions;
                                    var fixedNet_apy = parseFloat(net_pay).toFixed(2)
                                    var rate_sum = parseInt(resutl.rate)+parseFloat(resutl.bonusPay);
                                    $('.netpay').text(fixedNet_apy);
                                    // $('.rate').html('$'+rate_sum);
                                    var income=$('.income_tax_hd').val();
                                    $('.INC_TAX').text(income);

                                    var basic_hour = $('.total_work_Basic_hours_and_minits').html();
                                    var basic_pay = $('.total_basic_pay').html();
                                    var overtime_hour = $('.total_work_Over_hours_and_minits').html();
                                    var overtime_pay = $('.total_over_time_pay').html();
                                    var sick_hour = $('.sick_hour').html();
                                    var sick_pay = $('.sick_total').html();
                                    var vacation_hour = $('.vacation_hour').html();
                                    var vacation_pay = $('.vacation_total').html();
                                    var maternity_hour = $('.maternity_hour').html();
                                    var maternity_pay = $('.maternity_total').html();
                                    var holiday_hour = $('.holiday_hour').html();
                                    var holiday_total = $('.holiday_total').html();
                                    var onetime_name = $('.one_time_deduction_name').html();
                                    var onetime_deduction = $('.one_time_deduction_value').val();
                                    var continuous_name = $('.continuous_name').html();
                                    var continuous_deduction = $('.continuous_value').val();
                                    var periodic_name = $('.loan_name').html();
                                    var periodic_deduction = $('.loan_update').val();
                                    var bonus = $('.bonuspay').html();
                                    var nis = $('.nis_update').val();
                                    var nht = $('.nht_update').val();
                                    var edTax = $('.edt_update').val();

                                    var arr = [];
                                    arr.push(basic_hour,basic_pay,overtime_hour,overtime_pay,sick_hour,sick_pay,vacation_hour,vacation_pay,maternity_hour,maternity_pay,holiday_hour,holiday_total,onetime_name,onetime_deduction,continuous_name,continuous_deduction,periodic_name,periodic_deduction,bonus,nis,nht,edTax);
                                    $('.print_data').attr("value",arr);
                                    $('.email_u_id').attr("value",user_id);
                                    $('.print_u_id').attr("value",user_id);
                                    $('.print_cycle').attr("value",cycle);

                                }

                            }
                        });


                    }


                });


                $('.proceed').click(function() {
                    var emp_name=  $('.first_name').text();
                    var id=  $('.user_id').val();

                    var bonus= $('.bonuspay').text();
                    var nis= $('.nis_update').val();
                    var dept = $('.department').text();

                    var nht=$('.nht_update').val();
                    var edtax=$('.edt_update').val();
                    var s_d = $('.start_date').val();
                    var e_d = $('.end_date').val();
                    var netpay=$('.netpay').text();
                    var tot_sal2 = $('.sum_basic_and_over_pay').text().split("$");
                    var gross__pay = tot_sal2[1];
                    var tot_deduc = $(".total-deduction").text();
                    var income_save=$('.income_tax_update').val();
                    var loan_update = $('.loan_update').val();
                    var one_time_pass = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var periodic = $('.loan_update').val();


                    $.ajax({
                        url:"{{url('admin/proceed')}}",
                        type:"get",
                        data:{
                            "user_id":id,"start_date":s_d,"end_date":e_d,"nis":nis,
                            "nht":nht, 'edtax':edtax,'netpay':netpay,'income_save':income_save,'bonus':bonus,'dept':dept,'emp_name':emp_name,
                            "gross__pay":gross__pay,'tot_deduc':tot_deduc, 'payroll_cycle':payroll_cycle,'loan_amount':loan_update,'loan_flag':loan_flag,'ins_val':ins_val,'pause':pause,'one_time_pass':one_time_pass,'continuous':continuous,'periodic':periodic,'cycle':cycle,
                            'work_hours':$('.total_work_hours_and_minits').text(),'basic_hours':$('.total_work_Basic_hours_and_minits').text(),'basic_pay':$('.total_basic_pa').text(),'atten_hour':$('.atten_hour').text(),'atten_pay':$('.atten_total').text(),'sick_hour':$('.sick_hour').text(),'sick_pay':$('.sick_tol').text(),'vacation_hour':$('.vacation_hour').text(),'vacation_pay':$('.vacation_total').text(),'maternity_hour':$('.maternity_hour').text(),'maternity_pay':$('.maternity_total').text(),'holiday_hour':$('.holiday_hour').text(),'holiday_pay':$('.holiday_total').text(),'overtime_hour':$('.total_work_Over_hours_and_minits').text(),'overtime_pay':$('.total_overtime_pay').text(),'bonus_name':$('.bonus_name').text(),'onetime_name':$('.one_time_deduction_name').text(),'continuous_name':$('.continuous_name').text(),'periodic_name':$('.loan_name').text(),'cont_nis':$('.cont_nis').val(),'cont_nht':$('.cont_nht').val(),'cont_edtax':$('.cont_edtax').val(),'cont_heart':$('.cont_heart').val(),'one_time_id':$('.one_time_id').val(),'continuous_id':$('.continuous_id').val(),'loan_id':$('.loan_id').val()
                        },
                        success: function (resutl) {
                            if(resutl==0) {
                                toastr.success("Payroll Added");
                                window.location.reload(true);
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
            $('document').ready(function()
            {
                $('.process-all').click(function(){
                    $.ajax({
                        url:"{{url('admin/proceed_all')}}",
                        type:"get",
                        data:{
                            "cycle":$('.hidden_input').val(),'department':$('.hidden_input2').val(),
                            'start_date':$('.h_p_s_d').val(),'end_date':$('.h_p_e_d').val()
                        },
                        success: function (resutl) {
                            if(resutl.response==1)
                            {
                                toastr.success("All Payroll Added Successfully!!");
                            }
                        }
                    });
                })
            })


        </script>

    <script>
        $('document').ready(function()
        {
            $('.email-all').click(function(){
                $.ajax({
                  url:"{{url('admin/email_all')}}",
                  type:"get",
                  data:{
                        "cycle":$('.hidden_input').val(),'department':$('.hidden_input2').val(),
                        'start_date':$('.h_p_s_d').val(),'end_date':$('.h_p_e_d').val()
                    },
                  success: function (resutl) {
                      if(resutl.status==1)
                      {
                        toastr.success("Pay Slip Sent to Every Email Successfully!!");
                      }
                      else if(resutl.status==2)
                      {
                        toastr.error("All the Agents Must be Processed First!!");
                      }
                  }
              });
            })
        })
    </script>

        <script>
            $(".input_values_data").keypress(function(){
                if(event.which==13){
                    var split_input = [];
                    var input_id = $(this).attr("id");
                    var input_val = $(this).val();
                    if(input_val.includes('.'))
                    {
                        split_input = input_val.split(".");
                        var tempStr = "0."+split_input[1];
                        split_input[1] = tempStr;
                    }
                    else {
                        split_input[0] = input_val;
                        split_input[1] = '0';
                    }
                    var temp_start_date=$('.start_date').val();
                    var temp_end_date=$('.end_date').val();
                    var temp;
                    var temp_split_input = input_val.split(":");
                    var dataID = $(this).attr('data-hours');
                    var cycle = $(this).attr("data-cycle");
                    var hoursData = dataID.split(",");
                    var temp_attn_inc_pay = 0;
                    var total = hoursData[0];
                    var basic = hoursData[1];
                    var overtime = hoursData[2];
                    $('.total_work_hours_and_minits').html(input_val);
                    var h_rate = $('.hourly_rate').text();
                    h_rate = h_rate.replace("$","");

                    var temp_over_time_rate = $(".over_time_rate").html();
                    temp_over_time_rate = temp_over_time_rate.replace("$","");
                    if(parseInt(split_input[0])<=80)
                    {
                        //Basic Pay
                        var temp_basic_pay = parseFloat(h_rate)*parseFloat(split_input[0])+parseFloat(h_rate)*parseFloat(split_input[1]);
                        $(".total_work_Basic_hours_and_minits").html(temp_basic_pay);
                        $(".total_basic_pay").html("$"+temp_basic_pay);
                        $(".total_work_Basic_hours_and_minits").html(input_val);

                        $(".total_work_Over_hours_and_minits").html("0:0");
                        $(".total_over_time_pay ").html("$0");

                        //Attn Inc Rate
                        var temp_attn_rate = $(".atten_rate").text();
                        temp_attn_rate = temp_attn_rate.replace("$","");
                        $(".atten_hour").html(input_val);

                        if(parseInt(temp_attn_rate)!=0)
                        {
                            temp_attn_inc_pay = parseFloat(temp_attn_rate)*parseFloat(split_input[0])+parseFloat(temp_attn_rate)*parseFloat(split_input[1]);
                            $(".atten_total").html("$"+temp_attn_inc_pay);
                        }
                        var temp_bonus = $(".bonuspay").text();
                        var temp_split_bonus = temp_bonus.replace("$","");

                        temp = parseFloat(temp_basic_pay) + (temp_attn_inc_pay)+parseFloat(temp_split_bonus);
                        $(".sum_basic_and_over_pay").html("$"+temp);
                    }
                    else if(parseInt(split_input[0])>=80) {
                        var temp_overtime_hour = split_input[0]-80;
                        var temp_overtime_min = split_input[1];
                        var tempPlace;
                        if(temp_overtime_min!=='0')
                        {
                            temp_overtime_min = temp_overtime_min.replace("0","");
                            tempPlace = temp_overtime_hour+temp_overtime_min;
                        }
                        else {
                            tempPlace = temp_overtime_hour+'0';
                        }


                        //Basic Pay
                        var temp_basic_pay = parseFloat(h_rate)*parseFloat(80)+parseFloat(h_rate)*parseFloat(0);
                        $(".total_work_Basic_hours_and_minits").html(temp_basic_pay);
                        $(".total_basic_pay").html("$"+temp_basic_pay);
                        $(".total_work_Basic_hours_and_minits").html("80.0");

                        //Attn Inc Rate
                        var temp_attn_rate = $(".atten_rate").text();
                        temp_attn_rate = temp_attn_rate.replace("$","");
                        $(".atten_hour").html("80.0");
                        if(parseInt(temp_attn_rate)!=0)
                        {
                            temp_attn_inc_pay = parseFloat(temp_attn_rate)*parseFloat(80)+parseFloat(temp_attn_rate)*parseFloat(0);
                            $(".atten_total").html("$"+temp_attn_inc_pay);
                        }
                        //Overtime Pay
                        var temp_overtime_pay = parseFloat(temp_over_time_rate)*parseFloat(temp_overtime_hour)+parseFloat(temp_over_time_rate)*parseFloat(temp_overtime_min);
                        $(".total_work_Over_hours_and_minits").html(tempPlace);
                        $(".total_over_time_pay").html("$"+temp_overtime_pay);
                        $(".total_work_Over_hours_and_minits").html(temp_overtime_hour+":"+temp_overtime_min);
                        var temp_bonus = $(".bonuspay").text();
                        var temp_split_bonus = temp_bonus.replace("$","");
                        temp = parseFloat(temp_basic_pay) + parseFloat(temp_attn_inc_pay) + parseFloat(temp_overtime_pay)+parseFloat(temp_split_bonus);
                        $(".sum_basic_and_over_pay").html("$"+temp);


                    }

                    var h_r = $('.hourly_r').text();
                    var hourly_r = h_r.replace("$","");

                    var a_r = $(".atten_rate").text();
                    var att_rate = a_r.replace("$","");

                    var ot_r = $('.overtime_r').text();
                    var overtime_r = ot_r.replace("$","");

                    var bonus_p = $('.bonuspay').text();
                    var t_bonus_p = bonus_p.replace("$","");

                    var temp_input_id = input_id.split(".");
                    var url = '{{ route("admin.temp_deduction", ":id") }}'
                    url = url.replace(":id", temp_input_id[1]);
                    $.ajax({
                        type:"get",
                        url:url,
                        data: {
                            "total":temp,'cycle':cycle,'s_date':temp_start_date,'e_date':temp_end_date,'input_val':input_val,'hourly_rate':hourly_r,'atten_rate':att_rate,'overtime_rate':overtime_r,'bonus_pay':t_bonus_p
                        },
                        success: function (result) {
                            if(result.status==2)
                            {
                                toastr.error("Accumulate Threshold Does not Exist for this payroll Update the Threshold!!");
                            }
                            else if(result.status==1)
                            {

                                $('.total_work_Basic_hours_and_minits').html(parseFloat(result.basic_time));
                                $('.atten_hour').html(parseFloat(result.basic_time))
                                $('.total_work_Over_hours_and_minits').html(parseFloat(result.overtime_time));
                                $('.sick_hour').html(parseFloat(result.sick_time));
                                $('.vacation_hour').html(parseFloat(result.vacationTime));
                                $('.holiday_h').html(result.holiday_h);



                                var h_r = $('.hourly_r').text();
                                var hourly_r = h_r.replace("$","");
                                var b_h = parseFloat(result.basic_time);
                                var b_p = parseFloat(b_h*hourly_r);
                                $('.total_basic_pay').html("$"+b_p);

                                var a_r = $(".atten_rate").text();
                                var att_rate = a_r.replace("$","");
                                var atten_hour = $('.atten_hour').text();
                                var atten_pay = parseFloat(att_rate*atten_hour);
                                $(".atten_total").html("$"+atten_pay);

                                var ot_r = $('.overtime_r').text();
                                var overtime_r = ot_r.replace("$","");
                                var o_h = parseFloat(result.overtime_time);
                                var o_p = parseFloat(o_h*overtime_r);
                                $('.total_over_time_pay ').html("$"+o_p);

                                var sick_pay = parseFloat(result.sick_time*hourly_r);
                                $('.sick_tol').html("$"+sick_pay);

                                var vacation_pay = parseFloat(result.vacationTime*hourly_r);
                                $('.vacation_total').html("$"+vacation_pay);

                                var holiday_pay = parseFloat(result.holiday_h*hourly_r);
                                $('.holiday_total').html("$"+holiday_pay);

                                $('.maternity_hour').html(result.maternityHour);
                                $('.maternity_rate').html("$"+hourly_r);
                                $('.maternity_total').html("$"+result.maternityPay);

                                var bonus_p = $('.bonuspay').text();
                                var t_bonus_p = bonus_p.replace("$","");
                                var temp_total_pay = parseFloat(b_p)+parseFloat(o_p)+parseFloat(atten_pay)+parseFloat(sick_pay)+parseFloat(vacation_pay)+parseFloat(holiday_pay)+parseFloat(t_bonus_p)+parseFloat(result.maternityPay);
                                $(".sum_basic_and_over_pay").html("$"+temp_total_pay);

                                var one_time = $('.one_time_deduction_value').val();
                                var continuous = $('.continuous_value').val();
                                var periodic = $('.loan_update').val();
                                $(".nis_update").val(result.nis);
                                $(".nht_update").val(result.nht);
                                $(".edt_update").val(result.edtax);
                                $('.cont_nis').val(result.nis);
                                $('.cont_nht').val(result.nht);
                                $('.cont_edtax').val(result.edtax);
                                $('.cont_heart').val(result.heart);
                                $(".income_tax_update").val(result.income_tax);
                                var deduction_sum = parseFloat(result.nis)+parseFloat(result.nht)+parseFloat(result.edtax)+parseFloat(result.income_tax)+parseFloat(one_time)+parseFloat(continuous)+parseFloat(periodic);
                                $(".total-deduction").html("$"+deduction_sum.toFixed(2));

                                var temp_net = parseFloat(temp_total_pay)-parseFloat(deduction_sum);
                                $(".netpay").html("$"+temp_net.toFixed(2));


                                var basic_hour = $('.total_work_Basic_hours_and_minits').html();
                                var basic_pay = $('.total_basic_pay').html();
                                var overtime_hour = $('.total_work_Over_hours_and_minits').html();
                                var overtime_pay = $('.total_over_time_pay').html();
                                var sick_hour = $('.sick_hour').html();
                                var sick_pay = $('.sick_total').html();
                                var vacation_hour = $('.vacation_hour').html();
                                var vacation_pay = $('.vacation_total').html();
                                var maternity_hour = $('.maternity_hour').html();
                                var maternity_pay = $('.maternity_total').html();
                                var holiday_hour = $('.holiday_hour').html();
                                var holiday_total = $('.holiday_total').html();
                                var onetime_name = $('.one_time_deduction_name').html();
                                var onetime_deduction = $('.one_time_deduction_value').val();
                                var continuous_name = $('.continuous_name').html();
                                var continuous_deduction = $('.continuous_value').val();
                                var periodic_name = $('.loan_name').html();
                                var periodic_deduction = $('.loan_update').val();
                                var bonus = $('.bonuspay').html();
                                var nis = $('.nis_update').val();
                                var nht = $('.nht_update').val();
                                var edTax = $('.edt_update').val();

                                var arr = [];
                                arr.push(basic_hour,basic_pay,overtime_hour,overtime_pay,sick_hour,sick_pay,vacation_hour,vacation_pay,maternity_hour,maternity_pay,holiday_hour,holiday_total,onetime_name,onetime_deduction,continuous_name,continuous_deduction,periodic_name,periodic_deduction,bonus,nis,nht,edTax);
                                $('.print_data').attr("value",arr);
                                $('.email_u_id').attr("value",temp_input_id[1]);
                                $('.print_u_id').attr("value",temp_input_id[1]);
                            }
                        }
                    })
                }
            })
        </script>
        <script>
            $(".nis_update").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });

            $(".nht_update").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);

                }
            });

            $(".edt_update").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });

            $(".income_tax_update").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });

            $(".loan_update").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });

            $(".one_time_deduction_value").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });

            $(".continuous_value").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });
            $(".loan_update").keypress(function() {
                if(event.which==13)
                {
                    var update_nis = $('.nis_update').val();
                    var update_nht = $('.nht_update').val();
                    var update_edt = $('.edt_update').val();
                    var update_inc_tax = $('.income_tax_update').val();
                    var loan_up = $('.loan_update').val();
                    var one_time = $('.one_time_deduction_value').val();
                    var continuous = $('.continuous_value').val();
                    var update_sum_deductions = parseFloat(update_edt)+parseFloat(update_nis)+parseFloat(update_nht)+parseFloat(update_inc_tax)+parseFloat(one_time)+parseFloat(continuous);

                    // sum_deductions = sum_deductions+parseFloat(loan_amount);
                    if(jQuery.type(loan_up)==='string' && parseInt(loan_up)>0)
                    {
                        update_sum_deductions = update_sum_deductions+parseFloat(loan_up);
                    }
                    update_sum_deductions = update_sum_deductions.toFixed(2);
                    $('.total-deduction').text(update_sum_deductions);
                    var tot_sal = $('.sum_basic_and_over_pay').text();
                    var res_sal = tot_sal.split("$");
                    var res = (parseFloat(res_sal[1])-parseFloat(update_sum_deductions)).toFixed(2);
                    $('.netpay').html(res);
                }
            });
        </script>

        <script>
            $('document').ready(function()
            {
                $('.print_hourly').click(function()
                {
                    alert("Clicked!!!");
                    $.ajax({
                        type:"get",
                        url:"{{url('admin/hourly_print')}}",
                        data: {
                            'print_s_d':$('.print_s_d').val(),'print_e_d':$('.print_e_d').val(),'print_u_id':$('.print_u_id').val(),'print_data':$('.print_data').val(),'print_cycle':$('.print_cycle').val()
                        },
                        success: function (result){
                            if(result.status==1)
                            {
                                toastr.error("Payroll is not processed yet!!");
                            }

                        }
                    });
                })
            })
        </script>
@endsection