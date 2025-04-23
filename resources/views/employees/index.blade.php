@extends('layouts.app')

@section('title', 'Funcionários')

@section('content')
<div class="container">
    <div class="text-right">
        <a class="btn btn-danger" href="{{ route('employees.relatorio.pdf') }}" target="_blank">
            <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-400"></i>
            Relatório em PDF
        </a>
    </div>

    <h2 class="mb-4 mt-4">Gerenciar Funcionários</h2>

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                    <i class="fas fa-plus mr-1"></i> Adicionar Funcionário
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar funcionário...">
                </div>

                <script>
                    document.getElementById('searchInput').addEventListener('keyup', function () {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#employeesTable tbody tr");

                        rows.forEach(row => {
                            let name = row.cells[0].textContent.toLowerCase();
                            let email = row.cells[1].textContent.toLowerCase();
                            let role = row.cells[2].textContent.toLowerCase();
                            let status = row.cells[3].textContent.toLowerCase();

                            if (
                                name.includes(query) ||
                                email.includes(query) ||
                                role.includes(query) ||
                                status.includes(query)
                            ) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        });
                    });
                </script>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                @if($employees->count())
                    <table class="table table-hover table-bordered" id="employeesTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Cargo</th>
                                <th>Salário</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->position }}</td>
                                    <td>R${{ $employee->salary }}</td>
                                    <td>
                                        <span class="badge badge-{{ $employee->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $employee->status == 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editEmployeeModal-{{ $employee->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteEmployeeModal-{{ $employee->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Editar --}}
                                <div class="modal fade" id="editEmployeeModal-{{ $employee->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Funcionário</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nome</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $employee->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Cargo</label>
                                                        <input type="text" name="position" class="form-control" value="{{ $employee->position }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Salário</label>
                                                        <input type="text" name="salary" class="form-control" value="{{ $employee->salary }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" class="form-control">
                                                            <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                                            <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Atualizar</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Modal Excluir --}}
                                <div class="modal fade" id="deleteEmployeeModal-{{ $employee->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('employees.destroy', $employee->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Excluir Funcionário</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja excluir o funcionário <strong>{{ $employee->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Nenhum funcionário encontrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Adicionar Funcionário --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Funcionário</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>


                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Cargo</label>
                        <input type="text" name="position" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Salário</label>
                        <input type="number" name="salary" class="form-control" required>
                    </div>


                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nível de acesso</label>
                        <select name="access_level" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="funcionario">Funcionário</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
