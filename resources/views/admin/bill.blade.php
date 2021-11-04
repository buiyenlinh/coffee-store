@extends ('admin.layout.adminLayout')

@section('content')
  <h3>Danh sách hóa đơn</h3>
  <div class="table-responsive">
    <table class="table table-hover table-stripted table-bordered">
      <thead class="bg-primary text-light">
        <th>Thời gian tạo</th>
        <th>Tên bàn</th>
        <th>Người tạo</th>
        <th>Trạng thái</th>
      </thead>
      <tbody>
        @foreach($bills as $_bill)
          <tr onclick="billGetDetail({{  $_bill['id'] }})">
            <td>{{ date('H:i, d-m-Y', strtotime($_bill['created_at'])) }}</td>
            <td>{{ $_bill['table_name'] }}</td>
            <td>{{ $_bill['username'] }}</td>
            <td>{{ $_bill['status'] ? 'Đã thanh toán' : 'Chưa thanh toán' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="bill-details">
    <h3>Danh sách sản phẩm hóa đơn</h3>
    <b class="text-danger bill-money-sum"></b>
    <div class="table-responsive">
      <table class="table table-hover table-stripted table-bordered">
        <thead class="bg-primary text-light">
          <th>Thời gian tạo</th>
          <th>Tên sản phẩm</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <th>Người tạo</th>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
@endsection