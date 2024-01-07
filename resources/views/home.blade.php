@extends('layouts.template')

@section('content')

        <div class="mt-4">
            @if(Session::get ('couldntAccess'))
        <div class="alert alert-danger">{{ Session::get('couldntAccess') }}</div>
        @endif

            <h1 class="display-4">
                Selamat Datang ! {{ Auth::user()->name }}!
            </h1>

            <hr>
            
            <h3>Dashboard</h3>
            <div class="d-flex">

                <h6 style="margin-right: 0.4rem;"><a class="nav-link text-secondary" href="/home">Home /</a></h6>
                <h6><a class="nav-link text-secondary" href="">Dashboard</a></h6>
            </div>
        </div>

        @if (Auth::user()->role == 'staff')
            <div class="container d-flex">
                <div class="card p-4 m-3" style="width: 700px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">    
                    <h5>Surat Keluar</h5><br>
                    <h2><i class="fa-solid fa-envelope" style="color: royalblue"></i>{{ count(App\Models\Letter_type::all()) }} </h2>
                </div>
                <div class="card p-4 m-3" style="width: 400px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <h5>Klasifikasi Surat</h5><br>
                    <h2><i class="fa-solid fa-envelope" style="color: royalblue"></i>{{ count(App\Models\Letter_type::all()) }}</h2>
                </div><br>
            </div>
            <div class="container d-flex">
                <div class="card p-4 m-3" style="width: 400px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <h5>Staff Tata Usaha</h5><br>
                    <h2><i class="fa-solid fa-circle-user" style="color: royalblue"></i>{{ count(App\Models\Letter_type::all()) }} </h2>
                </div>
                <div class="card p-4 m-3" style="width: 700px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <h5>Guru</h5><br>
                    <h2><i class="fa-solid fa-circle-user" style="color: royalblue"></i>{{ count(App\Models\User::where('role', 'guru')->get()) }}</h2>
                </div>
            </div>
        @endif
        @if (Auth::user()->role == 'guru')
            <div class="card p-4 m-3" style="width: 700px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">    
                <h5>Surat Masuk</h5><br>
                <h2><i class="fa-solid fa-envelope" style="color: royalblue"></i>{{ count(App\Models\User::where('role', 'guru')->get()) }} </h2>
            </div>
        @endif
    </div>

        @endsection