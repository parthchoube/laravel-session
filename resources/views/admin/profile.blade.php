@extends('admin.layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12 mb-8">
            <div class="card">
                <div class="card-header">
                    <div class="col-sm-12">
                        <h1>Users View</h1>
                         <a class="btn btn-primary btn-close float-right" href="{{ route('admin.users.add') }}" >Add Users</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered data-table">
                                <thead>
                               <tr>
                                  <th>ID</th>
                                  <th>Name</th>
                                  <th>Email</th>
                                  <th>Profile Picture</th>
                                  <th>Action</th>
                               </tr>
                                </thead>
                                 <tbody>
                                </tbody>
                            </table>
                         </div>
                     </div>
                </div>
            </div>
        </div>
</div>


@endsection
@section('scripts')
<script type="text/javascript">
 $(function () {
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'profile_pic', name: 'profile_pic'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
  });
 
</script>
@endsection
