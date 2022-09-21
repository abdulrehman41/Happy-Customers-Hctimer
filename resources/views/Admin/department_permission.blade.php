@extends('layouts.admin')
@section('content')
 @php


$dep = DB::table('departments')->get();
$user = DB::table('users')->select('id','first_name','last_name','user_role')->where('user_role','Supervisor')->get();


@endphp 
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">
                  <h4 class="card-title">Supervisor Department</h4>
              
       </h5>
        
        <p class="card-text">
          <div class="row justify-content-center">
   <div class="col-md-2">
            <form action="{{url('admin/department-permission/save')}}" method="post">
                @csrf
              
            </div>


                <div class="position-relative   col-md-4 has-icon-left">
             <select class="select2 form-control "   name="user_id"   required>
                                              <option value=""  selected disabled > Select User</option>

        @foreach ($user as $list)
            <option value="{{ $list->id }}" >
                 {{ $list->first_name }}</option>
        @endforeach 
    </select>

                </div>

                <div class="position-relative   col-md-4 has-icon-left">
             <select class="multipledepartment form-control "   name="department_name[]" multiple   required>
         @foreach ($dep as $list)
            <option value="{{ $list->department }}" >
                 {{ $list->department }}</option>
        @endforeach 
    </select>
                </div>

                <br>
                <button type="submit"  class="btn btn-primary " style="height: 40px;display:block;">Save</button>

            </div>
        </div>
        
      
    </form>
 <section id="basic-datatable">
  <div class="row">
      <div class="col-12">
          <div class="card">
             
              <div class="card-content">
                
                <div class="table-responsive" style="padding: 0 10px;">
                          <table class="table zero-configuration">
                              <thead>
                                  <tr>
                                      <th>#id</th>
                                                                            <th>User name</th>

                                      <th>Department</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                 
                                      
          @php
                                          $i=1;
                                                

                                      @endphp
                               @foreach($all as $list)

                               <tr>
                                                                    <td>{{$list->user->first_name}}</td>

                                    <td>{{$list->id}}</td>
                                    <td>{{$list->department_id}}</td>

                                    <td><a href="{{ url('admin/update-department-permission',$list->user->id) }}" class="btn btn-primary">Edit</a></td>

                                </tr>
                                
                                @endforeach 
                            
                              </tbody>

                          </table>
                      </div>
                           
                        </div>
                     
                    </div>

              </div>
          </div>
      </div>
  </div>
</section>
</div>

      </div>

    </div></p>
      </div>
    </div>
 







@endsection