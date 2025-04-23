@extends('layouts.app')


@section('content')
<div class="container">
   {{--   <div class="text-right">
      <a class="btn btn-danger" href="{{ route('employees.report.pdf') }}" target="_blank">
            <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-400"></i>
            Relatório em PDF
        </a>
    </div> --}}
    <h2 class="mb-4 mt-4">Gerenciar Funcionários</h2>

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                    <i class="fas fa-user-plus mr-1"></i> Adicionar Funcionário
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar funcionário...">
                </div>

                <script>
                    //SCRIPT PARA PESQUISAR FUNCIONÁRIOS
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#employeesTable tbody tr");

                        rows.forEach(row => {
                            let name = row.cells[0].textContent.toLowerCase();
                            let email = row.cells[1].textContent.toLowerCase();
                            let phone = row.cells[2].textContent.toLowerCase();
                            let position = row.cells[3].textContent.toLowerCase();
                            let status = row.cells[4].textContent.toLowerCase();

                            if (name.includes(query) || email.includes(query) || phone.includes(query) || position.includes(query) || status.includes(query)) {
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
                                <th>Telefone</th>
                                <th>Cargo</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->phone_number ?? '—' }}</td>
                                    <td>{{ $employee->employee_position ?? '—' }}</td>
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

                                        </div>
                                        </div>

                                    </td>
                                </tr>

                                {{-- Modal de Editar Funcionário --}}
                                <div class="modal fade" id="editEmployeeModal-{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel-{{ $employee->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header text-grey">
                                                    <h5 class="modal-title" id="editEmployeeModalLabel-{{ $employee->id }}">Editar Funcionário</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Nome</label>
                                                            <input type="text" name="first_name" class="form-control" value="{{ $employee->first_name }}" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Sobrenome</label>
                                                            <input type="text" name="last_name" class="form-control" value="{{ $employee->last_name }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>E-mail</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $employee->email }}" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Telefone</label>
                                                            <input type="text" name="phone_number" class="form-control" value="{{ $employee->phone_number }}">
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Cargo</label>
                                                            <input type="text" name="position" class="form-control" value="{{ $employee->position }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                                                <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                                            </select>
                                                        </div>
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

                                {{-- Modal de Excluir Funcionário --}}
                                <div class="modal fade" id="deleteEmployeeModal-{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteEmployeeModalLabel-{{ $employee->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('employees.destroy', $employee->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteEmployeeModalLabel-{{ $employee->id }}">Excluir Funcionário</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Tem certeza que deseja excluir o funcionário <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>?</p>
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
                    <p class="text-muted">Nenhum funcionário cadastrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal de Adicionar Funcionário --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-grey">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Adicionar Novo Funcionário</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">Nome</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Sobrenome</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone_number">Telefone</label>
                            <input type="text" name="phone_number" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="employee_position">Cargo</label>
                            <input type="text" name="employee_position" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="access_level">Nível de Acesso</label>
                            <select name="access_level" class="form-control" required>
                                <option value="admin">Administrador</option>
                                <option value="funcionario">Funcionário</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Adicionar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
