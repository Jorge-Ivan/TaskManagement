@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Proyectos <button type="button" class="btn btn-primary float-right" onclick="addProject()">Crear</button></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <ul class="list-group">
                        @foreach ($projects as $project)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{$project->name}}
                            <div>
                                <span class="badge badge-primary badge-pill" title="Tareas">{{$project->tasks()->count()}}</span>
                                <div class="btn-group">
                                    <a href="{{route('projects.edit', $project->id)}}" class="btn btn-info"><i class="bi bi-pencil-square"></i></a>
                                    <button onclick="deleteProject({{$project->id}})" class="btn btn-danger"><i class="bi bi-trash2-fill"></i></button>
                                </div>
                            </div>
                        </li>
                        @endforeach
                        @if($projects->count()==0)
                        <li class="list-group-item d-flex justify-content-between align-items-center text-mutted">
                            Sin proyectos.
                        </li>
                        @endempty
                      </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        async function addProject(){
            Swal.fire({
                title: "Crear proyecto",
                html: `
                    <div class="form-group">
                        <label for="name_swal">Nombre</label>
                        <input id="name_swal" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description_swal">Descripción</label>
                        <textarea id="description_swal" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="start_date_swal">Fecha inicio</label>
                        <input id="start_date_swal" type="date" class="form-control">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Enviar',
                allowOutsideClick: () => !Swal.isLoading(),
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const validationUrl = `
                        {{route('projects.create.validate')}}
                    `;
                    const dataForm = {
                        name:document.getElementById("name_swal").value,
                        description:document.getElementById("description_swal").value,
                        start_date:document.getElementById("start_date_swal").value
                    };
                    return await $.ajax({
                        url: validationUrl,
                        method: 'POST',
                        data: dataForm,
                        type: 'json',
                        accept: 'json',
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(error){
                            Swal.showValidationMessage(`${error.responseJSON.message}: ${JSON.stringify(error.responseJSON.errors)}`);
                            Swal.hideLoading();
                        }
                    }).done(function(data){
                        return data;
                    });
                }
            }).then((result)=>{
                console.log(result);
                if(result.value && result.isConfirmed){
                    $.ajax({
                        url: '{{route('projects.store')}}',
                        method: 'POST',
                        data: result.value,
                        type: 'json',
                        accept: 'json',
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(error){
                            Swal.showValidationMessage(`${error.responseJSON.message}: ${((error.responseJSON.errors)?JSON.stringify(error.responseJSON.errors):'')}`);
                            Swal.hideLoading();
                        }
                    }).done(function(data){
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: (data.message)?data.message:'Creado correctamente'
                        });
                        if(data.project_id){
                            setTimeout(() => {
                                location.href = '/proyectos/edit/'+data.project_id;
                            }, 3000);
                        }
                    });
                }
            });
        }
        function deleteProject(id){
            Swal.fire({
                title: "Eliminar proyecto?",
                text: 'Esto eliminara la información completa, incluyendo las tareas. No es reversible.',
                showCancelButton: true,
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{route('projects.index')}}/'+id,
                        method: 'POST',
                        type: 'json',
                        accept: 'json',
                        data: {_method:'DELETE'},
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(error){
                            Swal.fire({type:'danger', title:'No pudimos eliminar la información del proyecto.'});
                        }
                    }).done(function(data){
                        Swal.fire("Eliminado!", "", "success");
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    });
                }
            });
        }
    </script>
@endsection