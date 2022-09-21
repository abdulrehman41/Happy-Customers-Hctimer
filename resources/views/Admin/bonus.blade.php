@extends('layouts.admin')
@section('content')
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>  --}}
{{--  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>  --}}
<!-- Year Picker CSS -->
{{--  <link rel="stylesheet" href="{{asset('css/yearpicker.css')}}" />  --}}
<div class="card">
    <div class="card-header">
      <h4 class="card-title">Show All Bonuses</h4>
    </div>


    <div class="card-content">
        @if ($message = Session::get('success'))
                  <div class="alert alert-success ">    
                      <strong>{{ $message }}</strong>
                  </div>
        @endif
        @if ($message = Session::get('error'))
                  <div class="alert alert-danger ">    
                      <strong>{{ $message }}</strong>
                  </div>
        @endif 
        <div class="card-body card-dashboard">
            <p class="card-text">Bonus List</p>
            <div class="table-responsive">
                <table class="table zero-configuration">
                    <thead>
                        <tr>
                            <th>User Id</th>
                            <th>Employee Name</th>
                            <th>Bonus Name</th>
                            <th>Department</th>
                            <th>Pay Period From</th>
                            <th>Pay Period To</th>
                            <th>Bonus</th>
                                                                            @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        
                    @php
                    
                    $contains = Str::contains(Auth::user()->user_role, 'Supervisor');
                    @endphp
                    
                    @if($contains)
                    @foreach ($bonus_arr as $bonus)
                    <tr id={{"bonus_row".$bonus[0]["user_id"]}}>
                        <td>{{$bonus[0]["user_id"]}}</td>
                        <td>{{$bonus[0]["name"]}}</td>
                        <td>{{$bonus[0]["bonus_name"]}}</td>
                        <td>{{$bonus[0]["dep"]}}</td>
                        <td>{{$bonus[0]["start_date"]}}</td>
                        <td>{{$bonus[0]["end_date"]}}</td>
                        <td>{{$bonus[0]["bonus"]}}</td>
                        @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                        <td>
                            <a href="{{url('admin/edit_bonus',$bonus[0]["user_id"].'/'.$bonus[0]["start_date"].'/'.$bonus[0]["end_date"])}}" class="text-primary mr-2"><i class="feather icon-edit" title="Edit"></i></a>
                            <a href="{{url('admin/delete_bonus',$bonus[0]["user_id"].'/'.$bonus[0]["start_date"].'/'.$bonus[0]["end_date"])}}" class="text-danger"><i class="feather icon-trash " title="Delete"></i></a>
                        </td>
                    @endif
                    </tr>
                    @endforeach
                    
                    @else
                    @foreach ($bonusData as $bonus)
                    <tr id={{"bonus_row".$bonus->user_id}}>
                        <td>{{$bonus->user_id}}</td>
                        <td>{{$bonus->name}}</td>
                        <td>{{$bonus->bonus_name}}</td>
                        <td>{{$bonus->dep}}</td>
                        <td>{{$bonus->start_date}}</td>
                        <td>{{$bonus->end_date}}</td>
                        <td>{{$bonus->bonus}}</td>
                        @if($Employepermision['edit_access']==1 || $Employepermision['full_access']==1 )

                        <td>
                            <a href="{{url('admin/edit_bonus',$bonus->user_id.'/'.$bonus->start_date.'/'.$bonus->end_date)}}" class="text-primary mr-2"><i class="feather icon-edit" title="Edit"></i></a>
                            <button style="border:none;background-color:white;" id={{$bonus->user_id.".".$bonus->start_date.".".$bonus->end_date}} 
                            class="text-danger mr-2" onClick="deleteBonus(this.id)"><i class="feather icon-trash " title="Delete"></i></button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    @endif
                    
        <script>
            function deleteBonus(e)
            {
                const arr = e.split(".");
                var url = "https://hctimer.com/admin/delete_bonus/"+arr[0]+"/"+arr[1]+"/"+arr[2];
                console.log(url);
                
                $.ajax({
                  type:"get",
                  url:url,
                  success: function (resutl) {
                      if(resutl.status===200)
                      {
                          $("#bonus_row"+arr[0]).remove();
                          toastr.success("Delete Successfully!!");
                      }
                      else {
                          toastr.error("Error in Deleting!!");
                      }
                  }
              });
            }
        </script>
    @endsection


     
    