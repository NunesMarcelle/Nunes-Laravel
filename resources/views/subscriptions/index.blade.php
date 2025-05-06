@extends('layouts.app')

@section('title', 'Assinaturas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
    </div>

    <h2 class="mb-4 mt-4">Gerenciar Assinaturas</h2>

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addSubscriptionModal">
                    <i class="fas fa-plus mr-1"></i> Adicionar Assinatura
                </button>

                <input type="text" class="form-control w-25" id="searchInput" placeholder="Pesquisar assinatura...">
            </div>

            <script>
                document.getElementById('searchInput').addEventListener('keyup', function () {
                    let query = this.value.toLowerCase().trim();
                    let rows = document.querySelectorAll("#subscriptionsTable tbody tr");

                    rows.forEach(row => {
                        let name = row.cells[0].textContent.toLowerCase();
                        let price = row.cells[1].textContent.toLowerCase();
                        let duration = row.cells[2].textContent.toLowerCase();
                        let status = row.cells[3].textContent.toLowerCase();

                        row.style.display =
                            name.includes(query) || price.includes(query) || duration.includes(query) || status.includes(query)
                                ? "" : "none";
                    });
                });
            </script>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                @if($subscriptions->count())
                    <table class="table table-hover table-bordered" id="subscriptionsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>Valor</th>
                                <th>Duração</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->nome }}</td>
                                    <td>R$ {{ number_format($subscription->value, 2, ',', '.') }}</td>
                                    <td>{{ $subscription->cycle }} dias</td>
                                    <td>
                                        <span class="badge badge-{{ $subscription->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $subscription->status == 'active' ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editSubscriptionModal-{{ $subscription->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteSubscriptionModal-{{ $subscription->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Editar Assinatura --}}
                                <div class="modal fade" id="editSubscriptionModal-{{ $subscription->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg">
                                        <form method="POST" action="{{ route('signatures.update', $subscription->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Assinatura</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>Nome</label>
                                                            <input type="text" name="nome" class="form-control" value="{{ $subscription->nome }}" required>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Valor</label>
                                                            <input type="number" step="0.01" name="valor" class="form-control" value="{{ $subscription->value }}" required>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Duração (dias)</label>
                                                            <input type="number" name="duracao" class="form-control" value="{{ $subscription->cycle }}" required>
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control">
                                                                <option value="active" {{ $subscription->status == 'active' ? 'selected' : '' }}>Ativa</option>
                                                                <option value="inactive" {{ $subscription->status == 'inactive' ? 'selected' : '' }}>Inativa</option>
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

                                {{-- Modal Excluir Assinatura --}}
                                <div class="modal fade" id="deleteSubscriptionModal-{{ $subscription->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('assinaturas.destroy', $subscription->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Excluir Assinatura</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    Tem certeza que deseja excluir a assinatura <strong>{{ $subscription->nome }}</strong>?
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
                    <p class="text-muted">Nenhuma assinatura registrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Adicionar Assinatura --}}
<div class="modal fade" id="addSubscriptionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('subscriptions.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Nova Assinatura</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cliente">Selecione o Cliente</label>
                            <select name="cliente_id" id="cliente" class="form-control" required>
                                <option value="">Selecione um cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Valor</label>
                            <input type="number" step="0.01" name="valor" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Duração (dias)</label>
                            <input type="number" name="duracao" class="form-control" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Ativa</option>
                                <option value="inactive">Inativa</option>
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
