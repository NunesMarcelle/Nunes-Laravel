@extends('layouts.app')

@section('title', 'Vendas')

@section('content')
<div class="container">


    <h2 class="mb-4 mt-4">Gerenciar Vendas</h2>

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- Botão para adicionar venda -->
                <button class="btn btn-primary" data-toggle="modal" data-target="#addSaleModal">
                    <i class="fas fa-plus mr-1"></i> Adicionar Venda
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar venda...">
                </div>

                <script>
                    // SCRIPT PARA PESQUISAR VENDAS
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#salesTable tbody tr");

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
                @if($sales_products->count())
                <table class="table table-hover table-bordered" id="salesTable">
                        <thead class="thead-light">
                            <tr>
                                <th> Cliente</th>
                                <th>Produto</th>
                                <th>Valor Total</th>
                                <th>Data de Emissão</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales_products as $sale)
                                <tr>
                                    <td>{{ $sale->customer->name }}</td>
                                    <td>{{ $sale->product->name }}</td>

                                    <td>R$ {{ number_format($sale->total_price, 2, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $sale->status == 'completed' ? 'success' : 'secondary' }}">
                                            {{ $sale->status == 'completed' ? 'Concluída' : 'Pendente' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#viewSaleModal-{{ $sale->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteSaleModal-{{ $sale->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                            @if($sale->status != 'completed')
                                            <form method="POST" action="{{ route('sales.markPaid', $sale->id) }}" style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirmar recebimento do pagamento?')">
                                                    <i class="fas fa-money-bill-wave"></i> Receber
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('sales_product.generateBoleto', $sale->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-file-invoice"></i> Gerar Boleto
                                            </button>
                                        </form>




                                    </td>
                                </tr>



                                <!-- Modal Visualizar Venda -->
                                <div class="modal fade" id="viewSaleModal-{{ $sale->id }}" tabindex="-1" role="dialog" aria-labelledby="viewSaleModalLabel-{{ $sale->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header text-grey">
                                                <h5 class="modal-title" id="viewSaleModalLabel-{{ $sale->id }}">Detalhes da Venda</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Cliente:</strong> {{ $sale->customer->name }}</p>
                                                <p><strong>Produto:</strong> {{ $sale->product->name }}</p>
                                                <p><strong>Quantidade:</strong> {{ $sale->quantity }}</p>
                                                <p><strong>Preço Unitário:</strong> R$ {{ number_format($sale->unit_price, 2, ',', '.') }}</p>
                                                <p><strong>Valor Total:</strong> R$ {{ number_format($sale->total_price, 2, ',', '.') }}</p>
                                                <p><strong>Status:</strong>
                                                    <span class="badge badge-{{ $sale->status == 'completed' ? 'success' : 'secondary' }}">
                                                        {{ $sale->status == 'completed' ? 'Concluída' : 'Pendente' }}
                                                    </span>
                                                </p>
                                                <p><strong>Data de Emissão:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                {{-- Modal Excluir Venda --}}
                                <div class="modal fade" id="deleteSaleModal-{{ $sale->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSaleModalLabel-{{ $sale->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('sales_product.destroy', $sale->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteSaleModalLabel-{{ $sale->id }}">Excluir Venda</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Tem certeza que deseja excluir a venda para o cliente <strong>{{ $sale->customer_name }}</strong>?</p>
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
                    <p class="text-muted">Nenhuma venda registrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Adicionar Venda --}}
<div class="modal fade" id="addSaleModal" tabindex="-1" role="dialog" aria-labelledby="addSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('sales_product.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-grey">
                    <h5 class="modal-title" id="addSaleModalLabel">Adicionar Nova Venda</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Cliente</label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">Selecione um cliente</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Produto</label>
                            <select name="product_id" class="form-control" id="product_select" required>
                                <option value="">Selecione um produto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                        {{ $product->name ?? $product->descricao ?? 'Produto' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Quantidade</label>
                            <input type="number" name="quantity" class="form-control" id="quantity" min="1" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="billingType">Forma de pagamento</label>
                            <select name="billingType" id="billingType" class="form-control" required>
                                <option value="">Selecione</option>
                                <option value="CREDIT_CARD">Cartão de Crédito</option>
                                <option value="PIX">Pix</option>
                                <option value="BOLETO">Boleto</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Data de vencimento</label>
                            <input type="date" name="dueDate" class="form-control" id="dueDate" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Preço Unitário</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" id="unit_price" readonly required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Desconto</label>
                            <input type="number" step="0.01" name="discount" class="form-control" id="discount" value="0">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Preço Total</label>
                            <input type="number" step="0.01" name="total_price" class="form-control" id="total_price" readonly>
                        </div>
                    </div>

                    <script>
                        document.getElementById('product_select').addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                            document.getElementById('unit_price').value = price.toFixed(2);
                            atualizarPrecoTotal();
                        });

                        function atualizarPrecoTotal() {
                            const quantidade = parseFloat(document.getElementById('quantity').value) || 0;
                            const precoUnitario = parseFloat(document.getElementById('unit_price').value) || 0;
                            const desconto = parseFloat(document.getElementById('discount').value) || 0;

                            const total = (quantidade * precoUnitario) - desconto;
                            document.getElementById('total_price').value = total.toFixed(2);
                        }

                        document.getElementById('quantity').addEventListener('input', atualizarPrecoTotal);
                        document.getElementById('discount').addEventListener('input', atualizarPrecoTotal);
                    </script>
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
