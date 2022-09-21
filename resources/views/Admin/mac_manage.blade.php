@extends('layouts.admin')
@section('content')


    <section id="basic-datatable">

        <div class="row">

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">User's with Mac Address Assign</h4>
                        <a class="btn btn-primary btn-sm text-white" href="javascript:void(0)" data-toggle="modal" data-target="#addIP">Assign Mac Address</a>
                    </div>

                    <div class="card-content">

                        <div class="card-body card-dashboard">
                            <p class="card-text">User's List</p>

                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Name</th>
                                        <th>Mac Address</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $i=1;
                                    @endphp
                                    @foreach($data as $list)
                                        <tr id={{$list->user_id}}>
                                            <td>{{$i++}}</td>
                                            <td>{{$list->first_name.' '.$list->last_name}}</td>
                                            <td>{{$list->mac}}</td>
                                            <td>{{$list->status}}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target={{"#editmac".$list->id}}>
                                                    Edit
                                                </button>
                                                <a style="padding-top:15px;padding-bottom:15px;" class="btn btn-danger btn-sm text-white deleteMac" data-id={{$list->user_id}}  href="javascript:void(0)">Delete</a>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id={{"editmac".$list->id}} tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id={{"editmac".$list->id}}>Edit Mac</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{url('admin/mac_address_edit')}}/{{$list->id}}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{$list->user_id}}" class="form-control"  >
                                                            <div class="form-group">
                                                                <label for="first-name-icon">MAC Address</label>
                                                                <div class="position-relative has-icon-left">
                                                                    <input type="text" name="mac" value="{{$list->mac}}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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


    <div class="modal fade" id="addIP" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">Assign Mac Address</h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('admin/mac_add')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="position-relative has-icon-left" >
                                <select class="form-control a_b_d select2" name="mac_user" placeholder="Department" id="bonus-department-dropdown" >
                                    <option value="" >Select User</option>
                                    @foreach($users as $list)
                                        <option value={{$list["id"]}}>{{$list["first_name"].' '.$list["last_name"]}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first-name-icon">IP Address</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" placeholder="Enter Mac Address" name="mac" value="" class="form-control @error('mac') is-invalid @enderror">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $("document").ready(function (){
            $('.deleteMac').click(function (){
                var id = $(this).attr('data-id');
                var url = '{{ route("admin.mac.delete", ":id") }}'
                url = url.replace(":id", id);
                console.log(id,url);
                $.ajax({
                    url:url,
                    type:"get",
                    success: function (res){
                        $('tr#'+id).remove();
                    }
                })
            })
        })

    </script>



@endsection