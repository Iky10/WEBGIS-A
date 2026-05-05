{{-- SweetAlert2 Flash Notification (replaces Bootstrap alert) --}}
@if(session('flash_notification'))
    @foreach (session('flash_notification', collect())->toArray() as $message)
        @if ($message['overlay'])
            <script>
                Swal.fire({
                    title: '{!! addslashes($message["title"]) !!}',
                    html: '{!! addslashes($message["message"]) !!}',
                    icon: 'info'
                });
            </script>
        @else
            <script>
                Swal.fire({
                    icon: '{{ $message["level"] === "danger" ? "error" : ($message["level"] === "warning" ? "warning" : "success") }}',
                    title: '{!! addslashes($message["message"]) !!}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            </script>
        @endif
    @endforeach
    {{ session()->forget('flash_notification') }}
@endif
