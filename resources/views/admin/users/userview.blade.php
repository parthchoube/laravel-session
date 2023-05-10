@extends('admin.layouts.app')
@section('title', 'User Preview')
@section('content')
<div class="content-header">
	<div class="container-fluid">
		<div class="col-sm-12 mb-8">
            <div class="card">
                <div class="card-header">
        			<div class="col-sm-12">
        				<h1 class="m-0">Users Preview</h1>
        			</div>
                </div>
                <div class="card-body">
        			<div class="col-sm-12">
        				<table class="table table-bordered table-striped">
							<tr>
								<td>Full Name  </td><td>{{$users->name}} </td>
							</tr>
							<tr>
								<td>Email  </td><td>{{$users->email}} </td>
							</tr>
							<tr>
								<td>Profile Image  </td><td><img src="{{$users->profile_pic}}" alt="Image" height="50" width="50"></td>
							</tr>
						</table>
						<a type="button" class="btn btn-info btn-left" href="{{route('admin.users')}}">Back</a>
        			</div>
        		</div>
			</div>
		</div>
	</div>
</div>
@endsection
