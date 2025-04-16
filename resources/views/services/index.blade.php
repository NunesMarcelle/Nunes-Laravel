@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-danger" href="{{ route('services.relatorio.pdf') }}" target="_blank">
            <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-400"></i>
            Relatório em PDF
        </a>
    </div>

    <h2 class="mb-4 mt-4">Gerenciar Serviços</h2>

    <div class="card-body">

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">
                    <i class="fas fa-plus mr-1"></i> Adicionar Serviço
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar serviço...">
                </div>

                <script>
                    // SCRIPT PARA PESQUISAR SERVIÇOS
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#servicesTable tbody tr");

                        rows.forEach(row => {
                            let name = row.cells[0].textContent.toLowerCase();
                            let price = row.cells[1].textContent.toLowerCase();
                            let date = row.cells[2].textContent.toLowerCase();
                            let status = row.cells[3].textContent.toLowerCase();

                            if (
                                name.includes(query) ||
                                price.includes(query) ||
                                date.includes(query) ||
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
                @if($services->count())
                    <table class="table table-hover table-bordered" id="servicesTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Data de Criação</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($service->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $service->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $service->status == 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editServiceModal-{{ $service->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteServiceModal-{{ $service->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Editar Serviço -->
                                <div class="modal fade" id="editServiceModal-{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel-{{ $service->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form method="POST" action="{{ route('services.update', $service->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header text-grey">
                                                    <h5 class="modal-title" id="editServiceModalLabel-{{ $service->id }}">Editar Serviço</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>Nome do Serviço</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                                                        </div>

                                                        <div class="form-group col-md-4">
                                                            <label>Descrição</label>
                                                            <input type="text" name="description" class="form-control" value="{{ $service->description }}" required>
                                                        </div>

                                                        <div class="form-group col-md-4">
                                                            <label>Preço</label>
                                                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $service->price }}" required>
                                                        </div>

                                                        <div class="form-group col-md-12">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                                                <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
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


                                {{-- Modal Excluir Serviço --}}
                                <div class="modal fade" id="deleteServiceModal-{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteServiceModalLabel-{{ $service->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('services.destroy', $service->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteServiceModalLabel-{{ $service->id }}">Excluir Serviço</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Tem certeza que deseja excluir o serviço <strong>{{ $service->name }}</strong>?</p>
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
                    <p class="text-muted">Nenhum serviço registrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Adicionar Serviço --}}
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('services.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-grey">
                    <h5 class="modal-title" id="addServiceModalLabel">Adicionar Novo Serviço</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nome do Serviço</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Descrição</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Preço</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Status</label>
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
