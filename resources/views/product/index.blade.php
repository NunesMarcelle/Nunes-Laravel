@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container">
    <div class="d-sm-flex justify-content-end ">
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Gerar relatório</a>
    </div>
    <h2 class="mb-4 mt-4">Gerenciar Produtos</h2>


    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                    <i class="fas fa-plus mr-1"></i> Adicionar Produto
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar produto...">
                </div>

                <script>
                    // SCRIPT PARA PESQUISAR PRODUTOS
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#productsTable tbody tr");

                        rows.forEach(row => {
                            let name = row.cells[0].textContent.toLowerCase();
                            let price = row.cells[1].textContent.toLowerCase();
                            let amount = row.cells[2].textContent.toLowerCase();
                            let status = row.cells[3].textContent.toLowerCase();

                            if (
                                name.includes(query) ||
                                price.includes(query) ||
                                amount.includes(query) ||
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
                @if($products->count())
                    <table class="table table-hover table-bordered" id="productsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Estoque mínimo</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->amount < $product->min_amount ? 'warning' : 'success' }}">
                                            {{ $product->amount }}
                                        </span>
                                    </td>                                    <td>{{ $product->min_amount }}</td>

                                    <td>
                                        <span class="badge badge-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $product->status == 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editProductModal-{{ $product->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteProductModal-{{ $product->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Editar Produto --}}
                                <div class="modal fade" id="editProductModal-{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel-{{ $product->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form method="POST" action="{{ route('product.update', $product->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header text-grey">
                                                    <h5 class="modal-title" id="editProductModalLabel-{{ $product->id }}">Editar Produto</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-3">
                                                            <label>Nome</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>Preço</label>
                                                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>Estoque</label>
                                                            <input type="number" name="amount" class="form-control" value="{{ $product->amount }}" required>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>Quantidade mínima</label>
                                                            <input type="number" name="min_amount" class="form-control" value="{{ $product->min_amount }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status" class="form-control">
                                                            <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                                            <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
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

                                {{-- Modal Excluir Produto --}}
                                <div class="modal fade" id="deleteProductModal-{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel-{{ $product->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('product.destroy', $product->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteProductModalLabel-{{ $product->id }}">Excluir Produto</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Tem certeza que deseja excluir o produto <strong>{{ $product->name }}</strong>?</p>
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
                    <p class="text-muted">Nenhum produto cadastrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Adicionar Produto --}}
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('product.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-grey">
                    <h5 class="modal-title" id="addProductModalLabel">Adicionar Novo Produto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Preço</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>


                        <div class="form-group col-md-3">
                            <label>Estoque</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Estoque mínimo</label>
                            <input type="number" name="min_amount" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
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
