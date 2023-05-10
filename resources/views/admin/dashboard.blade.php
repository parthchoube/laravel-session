@extends('admin.layouts.app')
@section('title')
Dashboard
@endsection
@section('content')
<div class="content-header">
	<div class="container-fluid animated fadeIn">

		<div class="jumbotron mb-2">

			<h1 class="display-3">Welcome!</h1>

			<p>Use the sidebar to the left to create, edit or delete content.</p>

			<p style="margin: 0px 0px 0px -15px;">
				<a href="{{ url('admin/logout') }}" class="btn btn-success m-3">Logout</a>
			</p>
		</div>
	</div>
</div>
<style type="text/css">
	.display-3 {
	    font-size: 3.5rem;
	    font-style: inherit;
	    font-weight: 350;
	    line-height: 1.2;
	    font-weight: 600;
	    color: #28a745;
	}
</style>
@endsection