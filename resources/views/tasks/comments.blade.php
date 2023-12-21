@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tarea: {{$task->title}}</div>
                <div class="card-body">
                    <p><strong>Estado: {{$task->status->name}}</strong><br>Asignado a: {{$task->user->username}} - Fecha vencimiento: {{(empty($task->expire_date))?'Sin vencimiento':$task->expire_date}}</p>
                    <p>{{$task->description}}</p>
                    <div>
                        <p><strong><i class="bi bi-chat-right-dots-fill"></i> Comentarios</strong></p>
                        <form action="" name="form-comment-{{$task->id}}" id="form-comment-{{$task->id}}" onsubmit="addComment();return false;">
                            <div class="input-group">
                                <span class="input-group-text">Tú comentario</span>
                                <textarea name="content" id="task-comment" class="form-control" aria-label="Tú comentario" required minlength="10"></textarea>
                                <button class="btn btn-outline-primary" id="button-form-comments"><i class="bi bi-send-fill"></i> Enviar</button>
                            </div>
                        </form>
                        <div class="card">
                            <ul class="list-group list-group-flush">
                                @if($comments->total()==0)
                                <li class="list-group-item">Sin comentarios</li>
                                @endif
                                @foreach ($comments as $comment)
                                <li class="list-group-item">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><strong>{{$comment->user->username}}</strong></div>
                                        {{$comment->content}}<br>
                                        <small>{{$comment->created_at}}</small>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @if($comments->total()>$comments->perPage())
                            <div class="card-footer">
                                {{$comments->links()}}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    function addComment(e)
    {
        const comment = $('#task-comment').val();
        if(comment.length<10){
            swal.fire({icon:'warning', title:'El comentario es obligatorio, mínimo 10 carácteres.'});
        }else{
            $('#task-comment').prop('disabled', true);
            $('#button-form-comments').prop('disabled', true);
            $.ajax({
                url: '{{route('comments.store', $task->id)}}',
                data: {
                    content: comment
                },
                method: 'post',
                type: 'json',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (data)=>{
                    $('#task-comment').val('');
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
                },
                error: (error)=>{
                    Swal.fire({type:'danger', title: `${(error.responseJSON.message)?error.responseJSON.message:'No pudimos agregar el comentario de la tarea.'}${((error.responseJSON.errors)?': '+JSON.stringify(error.responseJSON.errors):'')}`});
                },
                complete: ()=>{
                    $('#task-comment').prop('disabled', false);
                    $('#button-form-comments').prop('disabled', false);
                }
            });
        }
    }
</script>
@endsection