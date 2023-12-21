@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Bienvenido al gesto de tareas de proyectos:
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                          <a class="nav-link" href="{{route('projects.index')}}">Gestionar mis proyectos <span class="badge badge-primary badge-pill"> {{auth()->user()->projects()->count()}}</span></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="{{ route('tasks.index') }}">Mis Tareas <span class="badge badge-primary badge-pill"> {{auth()->user()->tasks()->count()}}</span></a>
                        </li>
                      </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
