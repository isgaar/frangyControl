@extends('layouts.dashboard')

@section('title', 'Chat Interno')

@section('content_header')
    <div class="col-md-12 alert-section d-none" id="chat-alert">
        <div class="alert alert-success dashboard-legacy-alert">
            <span class="dashboard-legacy-alert__text" id="chat-alert-text"></span>
        </div>
    </div>
@stop

@section('content')
    <div class="resource-page">
        <section class="resource-hero">
            <div class="resource-hero__top">
                <div class="resource-hero__copy">
                    <span class="resource-hero__eyebrow">Comunicación</span>
                    <h1 class="resource-hero__title">Chat Interno</h1>
                    <p>Comunícate con otros empleados del sistema de forma directa.</p>
                </div>
            </div>
        </section>

        <div class="row mt-4" style="height: 60vh;">
            <!-- Lista de Usuarios -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark fw-bold">Contactos</h5>
                    </div>
                    <div class="card-body p-0" style="overflow-y: auto;">
                        <ul class="list-group list-group-flush" id="user-list">
                            @foreach($users as $user)
                                <li class="list-group-item list-group-item-action chat-user-item" 
                                    data-id="{{ $user->id }}" 
                                    data-name="{{ $user->name }}" 
                                    style="cursor: pointer;">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 text-dark fw-bold">{{ $user->name }}</h6>
                                    </div>
                                    <small class="text-muted">{{ $user->roles->pluck('name')->implode(', ') }}</small>
                                </li>
                            @endforeach
                            @if($users->isEmpty())
                                <li class="list-group-item text-center text-muted py-4">No hay otros usuarios disponibles.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Ventana de Chat -->
            <div class="col-md-8">
                <div class="card h-100 shadow-sm border-0 d-flex flex-column">
                    <div class="card-header bg-white border-bottom d-flex align-items-center">
                        <h5 class="mb-0 text-dark fw-bold" id="chat-with-name">Selecciona un contacto</h5>
                    </div>
                    <div class="card-body chat-messages-container" id="chat-messages" style="overflow-y: auto; flex: 1; background-color: #f8f9fa; padding: 20px;">
                        <div class="text-center text-muted mt-5">Selecciona un usuario de la lista para comenzar a chatear.</div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <form id="chat-form" class="d-flex" onsubmit="return false;">
                            <input type="hidden" id="active-user-id" value="">
                            <input type="text" id="chat-input" class="form-control me-2" placeholder="Escribe un mensaje..." disabled autocomplete="off">
                            <button type="submit" class="btn btn-primary px-4" id="btn-send-chat" disabled>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        const myId = {{ Auth::id() }};
        let activeUserId = null;
        let chatInterval = null;

        $(document).ready(function() {
            // Seleccionar usuario
            $('.chat-user-item').on('click', function() {
                $('.chat-user-item').removeClass('active bg-light border-primary');
                $(this).addClass('active bg-light border-primary');
                
                activeUserId = $(this).data('id');
                const userName = $(this).data('name');
                
                $('#chat-with-name').text('Chat con ' + userName);
                $('#active-user-id').val(activeUserId);
                $('#chat-input').prop('disabled', false).focus();
                $('#btn-send-chat').prop('disabled', false);
                
                fetchMessages();
                
                if(chatInterval) clearInterval(chatInterval);
                chatInterval = setInterval(fetchMessages, 3000); // Poll every 3 seconds
            });

            // Enviar mensaje
            $('#chat-form').on('submit', function(e) {
                e.preventDefault();
                const content = $('#chat-input').val().trim();
                const receiverId = $('#active-user-id').val();
                
                if(!content || !receiverId) return;
                
                $('#chat-input').val('');
                
                $.ajax({
                    url: '{{ route("chat.send") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        receiver_id: receiverId,
                        content: content
                    },
                    success: function(res) {
                        appendMessage(res, true);
                        scrollToBottom();
                    },
                    error: function(err) {
                        console.error('Error al enviar mensaje', err);
                    }
                });
            });
        });

        function fetchMessages() {
            if(!activeUserId) return;
            
            $.ajax({
                url: '/admin/chat/messages/' + activeUserId,
                type: 'GET',
                success: function(messages) {
                    $('#chat-messages').empty();
                    if(messages.length === 0) {
                        $('#chat-messages').append('<div class="text-center text-muted mt-5">No hay mensajes. ¡Di hola!</div>');
                        return;
                    }
                    
                    messages.forEach(msg => {
                        appendMessage(msg, msg.sender_id == myId);
                    });
                    
                    scrollToBottom();
                }
            });
        }

        function appendMessage(msg, isMine) {
            // Eliminar texto vacio
            $('.text-center.text-muted.mt-5').remove();
            
            const alignClass = isMine ? 'text-end' : 'text-start';
            const bgClass = isMine ? 'bg-primary text-white' : 'bg-white border';
            const floatClass = isMine ? 'float-end' : 'float-start';
            
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            const html = `
                <div class="mb-3 w-100 d-inline-block">
                    <div class="p-2 rounded ${bgClass} ${floatClass}" style="max-width: 75%; display: inline-block;">
                        <div style="word-wrap: break-word;">${escapeHtml(msg.content)}</div>
                        <div class="small mt-1 text-end" style="font-size: 0.7em; opacity: 0.8;">${time}</div>
                    </div>
                </div>
            `;
            $('#chat-messages').append(html);
        }

        function scrollToBottom() {
            const container = $('#chat-messages');
            container.scrollTop(container[0].scrollHeight);
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
@endsection
