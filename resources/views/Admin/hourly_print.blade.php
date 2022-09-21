<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  
    <style>
    * {
        margin:2%;
    }
    @page {
    size: auto;
    margin: 0;
    }
    .payment-data table{
        width:70% !important;
    }
    .payslip {
        display:flex;
        justify-content:center;
        align-items:center;
    }
</style>


</head>
<body>
    
    <hr>
    <h5 style="display:inline">CARI SUPPORT INTERNATIONAL</h5>
    <h5 style="display:inline;margin-left:100px">DATE: {{$data_time}}</h5>
    <h5>LIMITED TA HAPPY CUSTOMER</h5>
    <hr>
    <h5>PERIOD: {{$start_period}} TO {{$end_period}}</h5>
    <hr>
    <h5 style="display:inline">ID: {{$user[0]["employee_id"]}}</h5>
    <h5 style="display:inline;margin-left:100px">NAME: {{$user[0]["first_name"]." ".$user[0]["last_name"]}}</h5>
    <h5 style="display:inline;margin-left:100px">TRN: {{$user[0]["trn"]}}</h5>
    <h5 style="display:inline;margin-left:100px">NIS: {{$user[0]["nis"]}}</h5>
    <hr>
    
    
    @php
        $pay_sum = 0.0;
        $ded_sum = 0.0;
    @endphp
    <div class="payslip">
        <table class="table table-bordered" >
            <tr class="table-secondary">
                <th colspan="4">Payment</th>
            </tr>
            <tr class="table-secondary">
                <th>DESCRIPTION</th>
                <th>HOUR/DAYS</th>
                <th>RATE</th>
                <th>AMOUNT</th>
            </tr>
            
            @if($basic_pay>0)
            <tr>
                <td>
                    Basic Pay
                </td>
                <td>
                    {{$basic_hour}}
                </td>
                <td>
                    ${{$user[0]["hourly_rate"]}}
                </td>
                <td>
                    ${{$basic_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($basic_pay);
            @endphp
            @endif
            
            @if($atten_pay>0)
            <tr>
                <td>
                    Attendance Incentive
                </td>
                <td>
                    {{$atten_hour}}
                </td>
                <td>
                    ${{$user[0]["attn_inc_rate"]}}
                </td>
                <td>
                    ${{$atten_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($atten_pay);
            @endphp
            @endif
            
            @if($sick_pay>0)
            <tr>
                <td>
                    Sick Leave
                </td>
                <td>
                    {{$sick_hour}}
                </td>
                <td>
                    ${{$user[0]["hourly_rate"]}}
                </td>
                <td>
                    ${{$sick_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($sick_pay);
            @endphp
            @endif
            
            @if($vacation_pay>0)
            <tr>
                <td>
                    Vacation Leave
                </td>
                <td>
                    {{$vacation_hour}}
                </td>
                <td>
                    ${{$user[0]["hourly_rate"]}}
                </td>
                <td>
                    ${{$vacation_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($vacation_pay);
            @endphp
            @endif
            
            @if($maternity_pay>0)
            <tr>
                <td>
                    Maternity Leave
                </td>
                <td>
                    {{$maternity_hour}}
                </td>
                <td>
                    ${{$user[0]["hourly_rate"]}}
                </td>
                <td>
                    ${{$maternity_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($maternity_pay);
            @endphp
            @endif
            
            @if($holiday_pay>0)
            <tr>
                <td>
                    Holiday Pay
                </td>
                <td>
                    {{$holiday_hour}}
                </td>
                <td>
                    ${{$user[0]["hourly_rate"]}}
                </td>
                <td>
                    ${{$holiday_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($holiday_pay);
            @endphp
            @endif
            
            @if($overtime_pay>0)
            <tr>
                <td>
                    Overtime Pay
                </td>
                <td>
                    {{$overtime_hour}}
                </td>
                <td>
                    ${{$user[0]["hourly_rate"]}}
                </td>
                <td>
                    ${{$overtime_pay}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($overtime_pay);
            @endphp
            @endif
            @php
                $i = 0;
            @endphp
            @foreach($bonus_name as $b_n)
                <tr>
                <td>
                    Bonus
                </td>
                <td>
                    {{$b_n}}
                </td>
                <td>
                </td>
                <td>
                    {{$bonus_p[$i]}}
                </td>
            </tr>
            @php
                $pay_sum = $pay_sum+floatval($bonus_p[$i]);
                $i+=1;
            @endphp
            @endforeach
            @if($pay_sum>0)

            <tr>
                <th>Total</th>
                <td></td>
                <td></td>
                <th>${{$pay_sum}}</th>
            </tr>
            @endif
        </table>
        
        <table class="table table-bordered" >
            <tr class="table-secondary">
                <th colspan="2">Deductions</th>
            </tr>
            <tr class="table-secondary">
                <th>DESCRIPTION</th>
                <th>Amount</th>
            </tr>
            
            @if($nis_ded>0)
            <tr>
                <td>NIS</td>
                <td>${{$nis_ded}}</td>
            </tr>
            @php
                $ded_sum = $ded_sum+floatval($nis_ded);
            @endphp
            @endif
            
            @if($nht_ded>0)
            <tr>
                <td>NHT</td>
                <td>${{$nht_ded}}</td>
            </tr>
            @php
                $ded_sum = $ded_sum+floatval($nht_ded);
            @endphp
            @endif
            
            @if($edtax_ded>0)
            <tr>
                <td>ED TAX</td>
                <td>${{$edtax_ded}}</td>
            </tr>
            @php
                $ded_sum = $ded_sum+floatval($edtax_ded);
            @endphp
            @endif
            
            @php
                $i = 0;
            @endphp
            @foreach($onetime_name as $onetime)
                <tr>
                <td>
                    {{$onetime}}
                </td>
                <td>
                    ${{$onetime_pay[$i]}}
                </td>
            </tr>
            @php
                $ded_sum = $ded_sum+floatval($onetime_pay[$i]);
                $i+=1;
            @endphp
            @endforeach
            
            @php
                $i = 0;
            @endphp
            @foreach($continuous_name as $continuous)
                <tr>
                <td>
                    {{$continuous}}
                </td>
                <td>
                    ${{$continuous_pay[$i]}}
                </td>
            </tr>
            @php
                $ded_sum = $ded_sum+floatval($continuous_pay[$i]);
                $i+=1;
            @endphp
            @endforeach
            
            @php
                $i = 0;
            @endphp
            @foreach($periodic_name as $periodic)
                <tr>
                <td>
                    {{$periodic}}
                </td>
                <td>
                    ${{$periodic_pay[$i]}}
                </td>
            </tr>
            @php
                $ded_sum = $ded_sum+floatval($periodic_pay[$i]);
                $i+=1;
            @endphp
            @endforeach
            @if($ded_sum>0)
            <tr>
                <th>Total</th>
                <th>${{$ded_sum}}</th>
            </tr>
            @endif
        </table>
    </div>
    
    <h5>Year to Date</h5>
    <div class="payslip">
        <table class="table table-bordered" >
            <tr class="table-secondary">
                <th>NIS</th>
                <th>NHT</th>
                <th>ED TAX</th>
                <th>GROSS</th>
            </tr>
            <tr>
                <td>${{$process[0]['nis_total']}}</td>
                <td>${{$process[0]['nht_total']}}</td>
                <td>${{$process[0]['edtax_total']}}</td>
                <td>${{$process[0]['total_pay']}}</td>
            </tr>
        </table>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
</script>
<script>
        window.print();
</script>
</body>
</html>
