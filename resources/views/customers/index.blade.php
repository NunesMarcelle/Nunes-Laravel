@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container">
    <h2 class="mb-4 mt-4">Gerenciar Clientes</h2>
    

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
                    <i class="fas fa-user-plus mr-1"></i> Adicionar Cliente
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar cliente...">
                </div>

                <script>
                    //SCRIPT PARA PESQUISAR CLIENTES
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#customersTable tbody tr");

                        rows.forEach(row => {
                            let name = row.cells[0].textContent.toLowerCase();
                            let email = row.cells[1].textContent.toLowerCase();
                            let phone = row.cells[2].textContent.toLowerCase();
                            let birth_date = row.cells[3].textContent.toLowerCase();
                            let status = row.cells[4].textContent.toLowerCase();

                            if (
                                name.includes(query) ||
                                email.includes(query) ||
                                phone.includes(query) ||
                                status.includes(query) ||
                                birth_date.includes(query)
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
                @if($customers->count())
                    <table class="table table-hover table-bordered" id="customersTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Data de Nascimento</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? '—' }}</td>
                                    <td>{{ $customer->birth_date ? \Carbon\Carbon::parse($customer->birth_date)->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $customer->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $customer->status == 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editCustomerModal-{{ $customer->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteCustomerModal-{{ $customer->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal de Editar Cliente --}}
                                <div class="modal fade" id="editCustomerModal-{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel-{{ $customer->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header text-grey">
                                                    <h5 class="modal-title" id="editCustomerModalLabel-{{ $customer->id }}">Editar Cliente</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Nome</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>E-mail</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $customer->email }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>Telefone</label>
                                                            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Data de Nascimento</label>
                                                            <input type="date" name="birth_date" class="form-control" value="{{ $customer->birth_date }}">
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                                                <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
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

                                {{-- Modal de Excluir Cliente --}}
                                <div class="modal fade" id="deleteCustomerModal-{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModalLabel-{{ $customer->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('customers.destroy', $customer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteCustomerModalLabel-{{ $customer->id }}">Excluir Cliente</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Tem certeza que deseja excluir o cliente <strong>{{ $customer->name }}</strong>?</p>
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
                    <p class="text-muted">Nenhum cliente cadastrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal de Adicionar Cliente --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-grey">
                    <h5 class="modal-title" id="addCustomerModalLabel">Adicionar Novo Cliente</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Nome</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="phone">Telefone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="birth_date">Data de Nascimento</label>
                            <input type="date" name="birth_date" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
