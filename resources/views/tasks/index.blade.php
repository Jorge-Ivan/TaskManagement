@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Mis tareas:</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <ul class="list-group">
                        @foreach ($tasks as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <p><strong>{{$task->title}}</strong><br>
                            Asignado a: {{$task->user->username}} - Fecha vencimiento: {{(empty($task->expire_date))?'Sin vencimiento':$task->expire_date}}</p>
                            <div class="btn-group">
                                <button onclick="editStatus({{$task->id}}, {{$task->status_id}})" class="btn btn-info"><i class="bi bi-pencil-square"></i></button>
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
    function editStatus(id, status){
            Swal.fire({
                title: "Cambiar estado",
                html: `
                    <div class="form-group">
                        <label for="status_id_swal">Estado:</label>
                        <select id="status_id_swal" type="date" class="form-control">
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
                    if(originalData != null){
                        $("#status_id_swal").val(status);
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
</script>
@endsection