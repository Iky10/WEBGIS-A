{{-- Form fields untuk create & edit User --}}
@php
    // Generate avatar inisial untuk preview (max 2 char)
    $previewName = old('name', isset($user) ? $user->name : '');
    $previewInitials = '';
    if ($previewName) {
        $parts = preg_split('/\s+/', trim($previewName));
        if (count($parts) >= 2) {
            $previewInitials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
        } else {
            $previewInitials = strtoupper(mb_substr($previewName, 0, 2));
        }
    }
@endphp

<!-- Nama Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Nama Lengkap:') !!} <span class="text-danger">*</span>
    {!! Form::text('name', null, [
        'class'       => 'form-control',
        'placeholder' => 'Contoh: Rizky Iky',
        'required'    => true,
        'maxlength'   => 255,
        'autofocus'   => true,
    ]) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-12">
    {!! Form::label('email', 'Email:') !!} <span class="text-danger">*</span>
    {!! Form::email('email', null, [
        'class'       => 'form-control',
        'placeholder' => 'contoh@webgis.com',
        'required'    => true,
        'maxlength'   => 255,
    ]) !!}
    <small class="text-muted">Email akan digunakan untuk login.</small>
</div>

<!-- Password Field -->
<div class="form-group col-sm-12">
    {!! Form::label('password', isset($user) ? 'Password Baru:' : 'Password:') !!}
    @if(!isset($user))
        <span class="text-danger">*</span>
    @endif
    <div class="input-group">
        {!! Form::password('password', [
            'class'       => 'form-control',
            'placeholder' => isset($user) ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 6 karakter',
            'minlength'   => 6,
            'maxlength'   => 255,
            'id'          => 'password-input',
            'required'    => !isset($user),
        ]) !!}
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                <i class="fas fa-eye" id="togglePasswordIcon"></i>
            </button>
        </div>
    </div>
    @if(isset($user))
        <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Kosongkan jika tidak ingin mengubah password.
        </small>
    @else
        <small class="text-muted">
            Minimal 6 karakter. Disarankan kombinasi huruf, angka & simbol.
        </small>
    @endif
</div>

<!-- Role Field -->
<div class="form-group col-sm-12">
    {!! Form::label('role', 'Role:') !!} <span class="text-danger">*</span>
    @php
        $isSelf = isset($user) && $user->id === Auth::id();
        $isLastAdmin = isset($user) && $user->isAdmin()
            && \App\Models\User::where('role', 'admin')->count() <= 1;
        $disableRole = $isSelf || $isLastAdmin;
    @endphp
    <div class="row">
        <div class="col-sm-6">
            <div class="card role-card {{ (old('role', isset($user) ? $user->role : 'user') === 'admin') ? 'role-card-active' : '' }}">
                <div class="card-body p-3">
                    <label class="d-flex align-items-center mb-0" style="cursor: pointer;">
                        {!! Form::radio('role', 'admin', null, [
                            'class' => 'mr-2',
                            'id' => 'role-admin',
                            'disabled' => $disableRole,
                        ]) !!}
                        <div>
                            <strong style="color:#dc3545;">
                                <i class="fas fa-user-shield mr-1"></i> Admin
                            </strong>
                            <small class="d-block text-muted">Akses penuh ke dashboard admin & semua data</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card role-card {{ (old('role', isset($user) ? $user->role : 'user') === 'user') ? 'role-card-active' : '' }}">
                <div class="card-body p-3">
                    <label class="d-flex align-items-center mb-0" style="cursor: pointer;">
                        {!! Form::radio('role', 'user', null, [
                            'class' => 'mr-2',
                            'id' => 'role-user',
                            'disabled' => $disableRole,
                        ]) !!}
                        <div>
                            <strong style="color:#3498db;">
                                <i class="fas fa-user mr-1"></i> User
                            </strong>
                            <small class="d-block text-muted">Hanya bisa lihat peta & ajukan pengajuan</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    @if($disableRole)
        @if($isSelf)
            <small class="text-warning d-block mt-2">
                <i class="fas fa-lock"></i>
                Role tidak bisa diubah karena ini adalah akun Anda sendiri.
            </small>
            {{-- Hidden input supaya role tetap ter-submit walau radio disabled --}}
            <input type="hidden" name="role" value="{{ $user->role }}">
        @elseif($isLastAdmin)
            <small class="text-warning d-block mt-2">
                <i class="fas fa-shield-alt"></i>
                Ini admin terakhir di sistem. Role tidak bisa diubah ke User.
            </small>
            <input type="hidden" name="role" value="admin">
        @endif
    @endif
</div>

@push('page_css')
<style>
.role-card {
    border: 2px solid #e0e0e0;
    cursor: pointer;
    transition: all 0.2s;
}
.role-card:hover {
    border-color: #3498db;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.role-card-active {
    border-color: #3498db !important;
    background: rgba(52, 152, 219, 0.05);
}
.role-card label {
    width: 100%;
}
</style>
@endpush

@push('page_scripts')
<script>
$(function() {
    // Role card click → toggle radio
    $('.role-card').on('click', function() {
        var $radio = $(this).find('input[type="radio"]:not(:disabled)');
        if ($radio.length === 0) return;
        $radio.prop('checked', true);
        $('.role-card').removeClass('role-card-active');
        $(this).addClass('role-card-active');
    });

    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        var $input = $('#password-input');
        var $icon = $('#togglePasswordIcon');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
@endpush
