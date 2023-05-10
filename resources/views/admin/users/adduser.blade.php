@extends('admin.layouts.app')
@section('title')
@if(isset($user))Edit User @else Add User @endif
@endsection
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card mt-3">
                <div class="card-header bg-primary">
                    <div class="card-title">@if(isset($user))Edit user @else Add user @endif</div>
                </div>
                <div class="card-body">
                    <form action="{{ isset($user) ? route('admin.updateUser',$user->id) : route('admin.storeUser') }}" method="POST" enctype="multipart/form-data" id="addUserForm">
                        @csrf
                        <div class="form-group">
                            <label for="Name">Name <small class="text-danger">*</small></label>
                            @if(isset($user->name))
                            <input type="text" class="form-control" placeholder="Enter name" id="name" name="name" value="{{ old('name',$user->name) }}">
                            @else
                            <input type="text" class="form-control" placeholder="Enter name" id="name" name="name" value="{{ old('name') }}">
                            @endif
                            @error('name')
                            <p class="text-danger">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="Name">Email <small class="text-danger">*</small></label>
                            @if(isset($user->email))
                            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email" value="{{ $user->email }}" disabled>
                            @else
                            <input type="text" class="form-control" placeholder="Enter email" id="email" name="email" value="{{old('email')}}">
                            @error('email')
                            <p class="text-danger">{{$message}}</p>
                            @enderror
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="Name">Profile<small class="text-danger">*</small></label>
                            @if(isset($user->profile_pic))
                            <input type="file" class="form-control-file border" onchange="loadFile(event)" id="exampleInputEmail1" aria-describedby="profile_picHelp" name="profile_pic"  value="{{old('profile_pic')}}"><br><img id="profile_upload" src="{{URL::asset($user->profile_pic)}}"  alt="Image" height="50px" width="50px" />
                            @else
                            <input type="file" class="form-control-file border" id="profile_pic" onchange="loadFile(event)"  name="profile_pic" value="{{old('profile_pic')}}"><br><img id="profile_upload" src="{{URL::asset(config('app.url').'/images/user.webp')}}" height="50px" width="50px" alt="Image" />
                            @error('profile_pic')
                            <p class="text-danger">{{$message}}</p>
                            @enderror
                            @endif 
                        </div>
                        @if(!isset($user))
                        <label for="password">Password<small class="text-danger">*</small></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control " placeholder="Enter Password" id="password" name="password" value="{{old('password')}}">
                            </div>
                            @error('password')
                            <p class="text-danger">{{$message}}</p>
                            @enderror 
                        </div>
                        <label for="confirm_password">Confirm Password<small class="text-danger">*</small></label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control input-lg" placeholder="Enter Confirm Password" id="password" name="confirm_password" value="">
                            </div>
                            @error('confirm_password')
                            <p class="text-danger">{{$message}}</p>
                            @enderror 
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary float-right">@if(isset($user))Update @else Submit @endif</button>
                    </form>
                   
                    <a href="{{route('admin.users')}}" class="btn btn-secondary mr-2 float-right">Back</a>
    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    var loadFile = function(event) {
        var reader = new FileReader();
        reader.onload = function(){
          var output = document.getElementById('profile_upload');
          output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    };
</script>