@extends('layouts.app')

@section('title', 'Serviços de Vendas')

@section('content')
<div class="container">
    <div class="text-right">
      {{--  <a class="btn btn-danger" href="{{ route('sales_services.relatorio.pdf') }}" target="_blank">
            <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-400"></i>
            Relatório em PDF
        </a> --}}
    </div>

    <h2 class="mb-4 mt-4">Gerenciar Serviços de Vendas</h2>

    <div class="card shadow rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addServiceSaleModal">
                    <i class="fas fa-plus mr-1"></i> Vender Serviço
                </button>

                <div>
                    <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar serviço...">
                </div>

                <script>
                    // SCRIPT PARA PESQUISAR SERVIÇOS
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        let query = this.value.toLowerCase().trim();
                        let rows = document.querySelectorAll("#salesServicesTable tbody tr");

                        rows.forEach(row => {
                            let serviceName = row.cells[0].textContent.toLowerCase();
                            let price = row.cells[1].textContent.toLowerCase();
                            let status = row.cells[2].textContent.toLowerCase();

                            if (
                                serviceName.includes(query) ||
                                price.includes(query) ||
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
                @if($salesServices->count())
                    <table class="table table-hover table-bordered" id="salesServicesTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome do Serviço</th>
                                <th>Cliente</th>
                                <th>Preço</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesServices as $service)
                                <tr>
                                    <td>{{ $service->service->name ?? 'Sem nome' }}</td>
                                    <td>{{ $service->customer->name }}</td>

                                    <td>R$ {{ number_format($service->total_price, 2, ',', '.') }}</td>
                                    <td>
                                        @if($service->status === 'pending')
                                            <span class="badge bg-warning text-white">Pendente</span>
                                        @elseif($service->status === 'completed')
                                            <span class="badge bg-success text-white">Pago</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($service->status) }}</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editSalesServiceModal-{{ $service->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteSalesServiceModal-{{ $service->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        @if($service->status === 'pending')
                                        <form method="POST" action="{{ route('sales_service.markPaid', $service->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirmar recebimento do pagamento?')">
                                                <i class="fas fa-money-bill-wave"></i> Receber
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                <!-- Modal Editar Serviço de Venda -->
                                <div class="modal fade" id="editSalesServiceModal-{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="editSalesServiceModalLabel-{{ $service->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <form method="POST" action="{{ route('sales_service.update', $service->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header text-grey">
                                                    <h5 class="modal-title" id="editSalesServiceModalLabel-{{ $service->id }}">Editar Serviço de Venda</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Nome do Serviço</label>
                                                            <select name="service_id" class="form-control" required>
                                                                @foreach ($services as $s)
                                                                    <option value="{{ $s->id }}" {{ $s->id == $service->service_id ? 'selected' : '' }}>
                                                                        {{ $s->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <label>Cliente</label>
                                                            <select name="customer_id" class="form-control" required>
                                                                @foreach ($customers as $c)
                                                                    <option value="{{ $c->id }}" {{ $c->id == $service->customer_id ? 'selected' : '' }}>
                                                                        {{ $c->name }}
                                                                    </option>
                                                                @endforeach
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

                                {{-- Modal Excluir Serviço de Venda --}}
                                <div class="modal fade" id="deleteSalesServiceModal-{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSalesServiceModalLabel-{{ $service->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="POST" action="{{ route('sales_service.destroy', $service->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteSalesServiceModalLabel-{{ $service->id }}">Excluir Serviço de Venda</h5>
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
                    <p class="text-muted">Nenhum serviço cadastrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- Modal Adicionar Serviço --}}
<div class="modal fade" id="addServiceSaleModal" tabindex="-1" role="dialog" aria-labelledby="addServiceSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('sales_service.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-grey">
                    <h5 class="modal-title" id="addServiceSaleModalLabel">Vender Serviço</h5>
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
                            <label>Serviço</label>
                            <select name="service_id" class="form-control" id="service_select" required>
                                <option value="">Selecione um serviço</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                        {{ $service->name ?? $service->descricao ?? 'Serviço' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <div class="form-group col-md-4">
                            <label>Preço</label>
                            <input type="number" step="0.01" name="price" class="form-control" id="price" readonly required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Desconto</label>
                            <input type="number" step="0.01" name="discount" class="form-control" id="service_discount" value="0">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Preço Total</label>
                            <input type="number" step="0.01" name="total_price" class="form-control" id="service_total_price" readonly>
                        </div>


                    </div>

                    <script>
                        // Atualizar preço unitário ao selecionar serviço
                        document.getElementById('service_select').addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                            document.getElementById('price').value = price.toFixed(2);
                            atualizarPrecoTotalServico();  // Atualiza o preço total após mudar o serviço
                        });

                        // Função para atualizar o preço total do serviço
                        function atualizarPrecoTotalServico() {
                            const precoUnitario = parseFloat(document.getElementById('price').value) || 0;
                            const desconto = parseFloat(document.getElementById('service_discount').value) || 0;
                            const total = precoUnitario - desconto;
                            document.getElementById('service_total_price').value = total.toFixed(2);
                        }

                        // Adiciona os eventos para atualizar o total sempre que os valores forem alterados
                        document.getElementById('service_discount').addEventListener('input', atualizarPrecoTotalServico);
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
