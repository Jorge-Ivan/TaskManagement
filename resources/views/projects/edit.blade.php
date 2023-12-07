@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Proyecto: {{$project->name}} <button type="button" class="btn btn-primary float-right" onclick="getProject()">Editar</button></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <p>Fecha inicio: {{$project->start_date}}</p>
                    <p>Descripción: {{$project->description}}</p>
                    <h3>Tareas <button type="button" class="btn btn-primary float-right" onclick="addTask()">Crear Tarea</button></h3>
                    <ul class="list-group">
                        @foreach ($project->tasks()->cursor() as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <p><strong>{{$task->title}}</strong><br><strong>Estado: {{$task->status->name}}</strong><br>
                            Asignado a: {{$task->user->username}} - Fecha vencimiento: {{(empty($task->expire_date))?'Sin vencimiento':$task->expire_date}}</p>
                            <div class="btn-group">
                                <button onclick="editTask({{$task->id}})" class="btn btn-info"><i class="bi bi-pencil-square"></i></button>
                                <button onclick="deleteTask({{$task->id}})" class="btn btn-danger"><i class="bi bi-trash2-fill"></i></button>
                            </div>
                        </li>
                        @endforeach
                        @if($project->tasks()->count()==0)
                        <li class="list-group-item d-flex justify-content-between align-items-center text-mutted">
                            Sin tareas.
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
        function getProject()
        {
            $.ajax({
                url: '{{route('projects.show', $project->id)}}',
                method: 'GET',
                type: 'json',
                accept: 'json',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(error){
                    Swal.fire({type:'danger', title:'No pudimos btener la información del proyecto.'});
                }
            }).done(function(data){
                updateProject(data);
            });
        }

        async function updateProject(data){
            Swal.fire({
                title: "Crear proyecto",
                html: `
                    <div class="form-group">
                        <label for="name_swal">Nombre</label>
                        <input id="name_swal" type="text" class="form-control" value="${data.name}">
                    </div>
                    <div class="form-group">
                        <label for="description_swal">Descripción</label>
                        <textarea id="description_swal" class="form-control" rows="3">${data.description}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="start_date_swal">Fecha inicio</label>
                        <input id="start_date_swal" type="date" class="form-control" value="${data.start_date}">
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
                    let data = result.value;
                    data._method = 'PUT';
                    $.ajax({
                        url: '{{route('projects.update', $project->id)}}',
                        method: 'POST',
                        data: data,
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
                            title: (data.message)?data.message:'Actualizado correctamente'
                        });
                        if(data.project_id){
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        }
                    });
                }
            });
        }

        function editTask(id)
        {
            $.ajax({
                url: '{{route('tasks.show')}}/'+id,
                method: 'GET',
                type: 'json',
                accept: 'json',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(error){
                    Swal.fire({type:'danger', title:'No pudimos btener la información de la tarea.'});
                }
            }).done(function(data){
                addTask(data);
            });
        }
        
        function addTask(originalData = null){
            Swal.fire({
                title: "Crear tarea",
                html: `
                    <div class="form-group">
                        <label for="title_swal">Título</label>
                        <input id="title_swal" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description_swal">Descripción</label>
                        <textarea id="description_swal" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="expire_date_swal">Fecha vencimiento <small>(Opcional)</small></label>
                        <input id="expire_date_swal" type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="user_id_swal">Asignado a:</label>
                        <select id="user_id_swal" type="date" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="status_id_swal">Estado:</label>
                        <select id="status_id_swal" type="date" class="form-control">
                            <option value="">-- Seleccionar --</option>
                        @foreach (\App\Status::orderBy('name')->cursor() as $status)
                            <option value="{{$status->id}}">{{$status->name}}</option>
                        @endforeach
                        </select>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Enviar',
                allowOutsideClick: () => !Swal.isLoading(),
                showLoaderOnConfirm: true,
                willOpen: () => {
                    Swal.showLoading();
                    Swal.disableButtons();
                    $.ajax({
                        url: '{{route('users.list')}}',
                        type: 'json',
                        method: 'GET',
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data){
                            if(data.length>0){
                                $('#user_id_swal').append(`<option value="">-- Seleccionar --</option>`);
                                data.forEach((item)=>{
                                    $('#user_id_swal').append(`<option value="${item.id}">${item.username}</option>`);
                                });
                                if(originalData!=null)
                                    $("#user_id_swal").val(originalData.user_id);
                            }else{
                                $('#user_id_swal').append(`<option value="" disabled>-- Sin Usuarios --</option>`);
                            }
                        },
                        error: function(){
                            Swal.showValidationMessage(`Hubo un error validando los usuarios. Cantacte al admin.`);
                        },
                        complete: function(){
                            Swal.hideLoading();
                            Swal.enableButtons();
                        }
                    })
                    if(originalData != null){
                        document.getElementById("title_swal").value = originalData.title;
                        document.getElementById("description_swal").value = originalData.description;
                        document.getElementById("expire_date_swal").value= originalData.expire_date
                        $("#status_id_swal").val(originalData.status_id);
                    }
                },
                preConfirm: async () => {
                    const validationUrl = `
                        {{route('tasks.create.validate')}}
                    `;
                    const dataForm = {
                        title:document.getElementById("title_swal").value,
                        description:document.getElementById("description_swal").value,
                        expire_date:document.getElementById("expire_date_swal").value,
                        user_id:document.getElementById("user_id_swal").value,
                        status_id:document.getElementById("status_id_swal").value
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
                    let data = result.value;
                    data.project_id = {{$project->id}};
                    if(originalData!=null){
                        data.id = originalData.id;
                        data._method = 'PUT';
                    }

                    saveDataTask(data)
                }
            });
        }

        function saveDataTask(data)
        {
            $.ajax({
                url: '{{route('tasks.store')}}',
                method: 'POST',
                data: data,
                type: 'json',
                accept: 'json',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(error){
                    Swal.fire({
                        type: 'danger',
                        text:`${((error.responseJSON.message)?error.responseJSON.message:'Error la procesar la solicitud.')} ${((error.responseJSON.errors)?': '+JSON.stringify(error.responseJSON.errors):'')}`
                    });
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
                    title: (data.message)?data.message:'Petición correcta'
                });
                setTimeout(() => {
                    location.reload();
                }, 3000);
            });
        }
        
        function deleteTask(id){
            Swal.fire({
                title: "Eliminar tarea?",
                showCancelButton: true,
                confirmButtonText: "Eliminar",
                cancelButtonText: "Cancelar",
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{route('tasks.show')}}/'+id,
                        method: 'POST',
                        type: 'json',
                        accept: 'json',
                        data: {_method:'DELETE'},
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(error){
                            Swal.fire({type:'danger', title:'No pudimos eliminar la información de la tarea.'});
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