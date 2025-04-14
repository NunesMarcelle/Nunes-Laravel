@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agendamentos</h1>

    <div class="card">
        <div class="card-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#scheduleModal" id="openModalButton">Novo Agendamento</button>

            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="scheduleForm" action="{{ route('schedules.store') }}" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Novo Agendamento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @csrf
          <input type="hidden" id="start" name="start">
          <input type="hidden" id="end" name="end">

          <div class="mb-3">
            <label for="start_time" class="form-label">Data e Hora de Início</label>
            <input type="text" class="form-control" id="start_time" name="start_time" readonly>
          </div>

          <div class="mb-3">
            <label for="end_time" class="form-label">Data e Hora de Término</label>
            <input type="text" class="form-control" id="end_time" name="end_time" readonly>
          </div>

          <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control" id="description" name="description"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

<!-- FullCalendar + Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        initialView: 'timeGridWeek',
        selectable: true,
        events: '/schedules/events',
        select: function (info) {
            document.getElementById('start').value = info.startStr;
            document.getElementById('end').value = info.endStr;
            document.getElementById('start_time').value = info.start.toLocaleString('pt-BR');
            document.getElementById('end_time').value = info.end.toLocaleString('pt-BR');
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
        }
    });
    calendar.render();

    // Apenas abre o modal
    document.getElementById('openModalButton').addEventListener('click', function() {
        let modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        modal.show();
    });
});
</script>
