@extends('base')
@section('css')

@endsection

@section('content')
<div>
  <div class="container">
    <div class="row">

    </div>
  </div>
</div>
<div class="table-responsive">
  <table class="table table-striped table-sm">
    <thead>
      <tr>
        <th>ID</th>
        <th>日付</th>
        <th>開始時間</th>
        <th>終了時間</th>
        <th>休憩時間</th>
        <th>労働時間</th>
        <th>時間外労働時間</th>
      </tr>
    </thead>
    <tbody id="scheduleTable">
      @foreach ($schedules as $schedule)
      <tr data-id="{{ $schedule->id }}" data-start-time="{{ $schedule->start_time }}" data-end-time="{{ $schedule->end_time }}" data-break-time="{{ $schedule->break_time }}">
        <td>{{ $schedule->id }}</td>
        <td>{{ $schedule->date }}</td>
        <td>{{ $schedule->start_time }}</td>
        <td>{{ $schedule->end_time }}</td>
        <td>{{ $schedule->break_time }}</td>
        <td class="working-hours"></td>
        <td class="overtime-hours"></td>
      </tr>
      @endforeach
    </tbody>

  </table>

  <!-- モーダルのHTML -->
  <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="scheduleModalLabel">勤務時間の記録</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <form id="scheduleForm">
            <input type="hidden" id="id" name="id">
            <div class="form-group">
              <label for="date">日付</label>
              <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
              <label for="start_time">開始時間</label>
              <input type="time" class="form-control" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
              <label for="end_time">終了時間</label>
              <input type="time" class="form-control" id="end_time" name="end_time">
            </div>
            <div class="form-group">
              <label for="break_time">休憩時間</label>
              <input type="time" class="form-control" id="break_time" name="break_time">
            </div>
            <!-- 計算結果を表示 -->
            <div id="working_hours" class="mt-3"></div>
            <div id="overtime_hours" class="mt-3"></div>
            <button type="submit" class="btn btn-primary">保存</button>
          </form>



        </div>
      </div>
    </div>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr');
    const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));

    rows.forEach(row => {
      row.addEventListener('click', function() {
        const id = this.querySelector('td:nth-child(1)').textContent;
        const date = this.querySelector('td:nth-child(2)').textContent;
        const start_time = this.querySelector('td:nth-child(3)').textContent;
        const end_time = this.querySelector('td:nth-child(4)').textContent;
        const break_time = this.querySelector('td:nth-child(5)').textContent;

        document.getElementById('id').value = id;
        document.getElementById('date').value = date;
        document.getElementById('start_time').value = start_time;
        document.getElementById('end_time').value = end_time;
        document.getElementById('break_time').value = break_time;

        calculateWorkingHours(start_time, end_time, break_time);

        modal.show();
      });
    });

    const form = document.getElementById('scheduleForm');
    if (form) {
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch('/schedules', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('保存に失敗しました');
          }
        })
        .catch(error => console.error('Error:', error));
      });
    } else {
      console.error('scheduleForm が見つかりません');
    }

    // 修正点：scheduleRows変数とループの修正
    const scheduleRows = document.querySelectorAll('#scheduleTable tr');

    scheduleRows.forEach(row => {
      const startTime = row.getAttribute('data-start-time');
      const endTime = row.getAttribute('data-end-time');
      const breakTime = row.getAttribute('data-break-time');

      const workingHours = calculateWorkingHours(startTime, endTime, breakTime);
      const overtimeHours = calculateOvertimeHours(endTime);

      row.querySelector('.working-hours').textContent = `${workingHours.toFixed(2)}`;
      row.querySelector('.overtime-hours').textContent = `${overtimeHours.toFixed(2)}`;
    });

    function calculateWorkingHours(startTime, endTime, breakTime) {
      const start = new Date(`1970-01-01T${startTime}:00`);
      const end = new Date(`1970-01-01T${endTime}:00`);
      const breakDuration = new Date(`1970-01-01T${breakTime}:00`);
      const endOfRegularHours = new Date(`1970-01-01T18:00:00`);

      let workingHours = (end - start - breakDuration) / (1000 * 60 * 60); // 労働時間
      if (workingHours < 0) workingHours += 24; // 負の値を修正

      let overtimeHours = (end - endOfRegularHours) / (1000 * 60 * 60); // 時間外労働
      if (overtimeHours < 0) overtimeHours = 0; // 時間外労働時間が負の値を持たないように修正

      console.log(`労働時間: ${workingHours} 時間`);
      console.log(`時間外労働時間: ${overtimeHours} 時間`);

      // フォームに表示
      document.getElementById('working_hours').textContent = `労働時間: ${workingHours.toFixed(2)} 時間`;
      document.getElementById('overtime_hours').textContent = `時間外労働時間: ${overtimeHours.toFixed(2)} 時間`;

      return workingHours;
    }

    function calculateOvertimeHours(endTime) {
      const end = new Date(`1970-01-01T${endTime}:00`);
      const endOfRegularHours = new Date(`1970-01-01T18:00:00`);

      let overtimeHours = (end - endOfRegularHours) / (1000 * 60 * 60); // 時間外労働
      if (overtimeHours < 0) overtimeHours = 0; // 時間外労働時間が負の値を持たないように修正

      return overtimeHours;
    }
  });
</script>

