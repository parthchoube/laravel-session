@extends('admin.layouts.app')
@section('title', 'Profile')
@section('content')
<!--toastr-->
<link rel="stylesheet" href="{{ asset('packages/toastr/toastr.min.css') }}">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card mt-3">
                <div class="card-header bg-primary">
                    <div class="card-title">@if(isset($user))My Profile @else Add user @endif</div>
                </div>
                <div class="card-body">
                    <form action="{{ isset($user) ? route('admin.updateUser',$user->id) : route('admin.storeUser') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="Name">Profile<small class="text-danger"></small></label>
                            @if(isset($user->profile_pic))
                            <input type="file" class="form-control-file border" id="exampleInputEmail1" aria-describedby="profile_picHelp" name="profile_pic" onchange="loadFile(event)" value="{{old('profile_pic')}}"><br><img src="{{URL::asset($user->profile_pic)}}"  id="profile_upload" height="50px" width="50px" />
                            @else
                            <input type="file" class="form-control-file border" id="profile_pic" name="profile_pic" value="{{old('profile_pic')}}">
                            @error('profile_pic')
                            <p class="text-danger">{{$message}}</p>
                            @enderror
                            @endif 
                        </div>

                       

                        {{-- <label for="Name">Password @if(isset($user))@else<small class="text-danger">*</small>@endif</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control input-lg" placeholder="Enter Password" id="password" name="password" value="{{old('password')}}">
                                <span class="input-group-btn"><button type="button" class="btn btn-default btn-lg getNewPass" style="padding: 0.2rem 0.7rem" onclick = "gfg_Run()"><span class="fa fa-refresh"></span></button>
                            </div>
                            @error('password')
                            <p class="text-danger">{{$message}}</p>
                            @enderror 
                        </div> --}}


                       <button type="submit" class="btn btn-primary mr-2 float-right">Update</button>
                    </form>
                    @if(isset($user))
                    <a href="{{route('admin.users')}}" class="btn btn-secondary mr-2 float-right">Back</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--toastr-->
<script src="{{ asset('packages/toastr/toastr.min.js') }}"></script>
<script type="text/javascript">
    var password = document.getElementById("password");
    function generateP() {
        var pass = '';
        var str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' + 'abcdefghijklmnopqrstuvwxyz0123456789@#$';

        for (let i = 1; i <= 10; i++) {
            var char = Math.floor(Math.random() * str.length + 1);
            pass += str.charAt(char)
        }
        return pass;
    }

    function gfg_Run() {
        const pass = generateP();
        password.value = pass;
    }
    var loadFile = function(event) {
        var reader = new FileReader();
        reader.onload = function(){
          var output = document.getElementById('profile_upload');
          output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    };
</script>
<script>
  @if(Session::has('message'))
  toastr.options =
  {
    "closeButton" : true,
    "progressBar" : true
  }
  toastr.success("{{ session('message') }}");
  @endif


    
</script>
@endsection
