@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Editar Perfil</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group mt-3">
                    <label for="company_name">Nome da Empresa</label>
                    <input type="text" class="form-control" name="company_name" value="{{ old('company_name', $user->company_name) }}" required>
                </div>

                <div class="form-group mt-3">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group mt-3">
                    <label for="password">Nova Senha <small class="text-muted">(Deixe em branco para manter a atual)</small></label>
                    <input type="password" class="form-control" name="password">
                </div>

                <button type="submit" class="btn btn-primary mt-4">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
