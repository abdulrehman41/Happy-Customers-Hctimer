@extends('layouts.admin')
@section('content')


    <section id="basic-datatable">

        <div class="row">

            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Show All IP's List</h4>
                        <a class="btn btn-primary btn-sm text-white" href="javascript:void(0)" data-toggle="modal" data-target="#addIP">Add IP Address</a>
                    </div>

                    <div class="card-content">

                        <div class="card-body card-dashboard">
                            <p class="card-text">IP's List</p>

                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                    <tr>
                                        <th>#id</th>
                                        <th>Network Name</th>
                                        <th>IP Address</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $i=1;
                                    @endphp
                                    @foreach($data as $list)
                                        <tr id={{"row".$list->id}}>
                                            <td>{{$i++}}</td>
                                            <td>{{$list->name}}</td>
                                            <td>{{$list->ip}}</td>
                                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal" 
                                data-target={{'#editIp'.$list->id}} >
                                            Edit Ip
                                        </button>
                                                <a style="padding-top:15px;padding-bottom:15px;" class="btn btn-danger btn-sm text-white deleteIP" data-id={{$list->id}}  href="javascript:void(0)">Delete</a>
                                            </td>
                                        </tr>
                                        </div>
                                        
<div class="modal fade" id={{'editIp'.$list->id}} tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id={{"editIp".$list->id}}>Edit IP</h5>
              </div>
              <div class="modal-body">
                
                <form action="{{url('admin/ip_edit')}}/{{$list->id}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="first-name-icon">Network Name</label>
                        <div class="position-relative has-icon-left">
                            <input type="text" name="network" value="{{$list->name}}" class="form-control  @error('network_name') is-invalid @enderror"  >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="first-name-icon">IP Address</label>
                        <div class="position-relative has-icon-left">
                            <input type="text" name="ip" value="{{$list->ip}}" class="form-control @error('ip') is-invalid @enderror">
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
    </section>


    <div class="modal fade" id="addIP" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">Add IP</h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('admin/ip_add')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="first-name-icon">Network Name</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" name="network" value="" class="form-control  @error('network_name') is-invalid @enderror"  >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first-name-icon">IP Address</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" name="ip" value="" class="form-control @error('ip') is-invalid @enderror">
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
            $('.deleteIP').click(function () {
                var id = $(this).attr('data-id');
                var url = '{{ route("admin.ip.delete", ":id") }}';
                url = url.replace(":id", id);
                console.log(id,url);
                $.ajax({
                    url:url,
                    type:"get",
                    success: function (res){
                        var deleteIp = '#row'+id;
                        $(deleteIp).remove();
                    }
                })
            })
        })

    </script>



@endsection