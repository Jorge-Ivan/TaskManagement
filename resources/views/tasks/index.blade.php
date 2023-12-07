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
                            <p><strong>{{$task->title}}</strong><br><strong>Estado: {{$task->status->name}}</strong><br>
                            Asignado a: {{$task->user->username}} - Fecha vencimiento: {{(empty($task->expire_date))?'Sin vencimiento':$task->expire_date}}</p>
                            <div class="btn-group">
                                <button onclick="editStatus({{$task->id}}, {{$task->status_id}})" class="btn btn-info"><i class="bi bi-pencil-square"></i></button>
                            </div>
                        </li>
                        @endforeach
                        @if($tasks->count()==0)
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
                    $("#status_id_swal").val(status);
                },
                preConfirm: async () => {
                    return {status_id:document.getElementById("status_id_swal").value};
                }
            }).then((result)=>{
                if(result.value && result.isConfirmed){
                    $.ajax({
                        url: `{{route('tasks.status')}}/${id}`,
                        method: 'POST',
                        type: 'json',
                        accept: 'json',
                        data: {status_id:result.value.status_id},
                        headers:{
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(error){
                            Swal.fire({type:'danger', title:'No pudimos btener la información de la tarea.'});
                        }
                    }).done(function(data){
                        location.reload();
                    });
                }
            });
        }
</script>
@endsection