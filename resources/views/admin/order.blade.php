@extends ('admin.layout.adminLayout')

@section('content')
  <div class="order">
    <div class="row">
      <div class="col-md-5 col-sm-8 col-xs-8">
        <div class="order-place">
          <ul class="nav nav-pills">
            <li 
              class="nav-item mr-3"
              onclick="searchTable('/admin/order/search-table', 0)"
            >
              <b class="nav-link active" data-toggle="pill">Tất cả</b>
            </li>
            @foreach ($places as $_place)
            <li
              class="nav-item mr-3"
              onclick="searchTable('/admin/order/search-table', {{ $_place['id'] }})"
            >
              <b class="nav-link" data-toggle="pill">{{ $_place['name'] }}</b>
            </li>
            @endforeach
          </ul>
        </div>
        <div class="order-table">
          <ul class="order-table-list row"></ul>
        </div>
      </div>

      <div class="col-md-2 col-sm-4 col-xs-4">
        <table class="table table-hover table-striped">
          <thead class="bg-primary text-light">
            <th>Sản phẩm</th>
          </thead>
          <tbody>
            @foreach($products as $_product)
              <tr>
                <td>{{ $_product['name'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="col-md-5 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table table-hover table-striped table-bordered">
            <thead class="bg-primary text-light">
              <th>Sản phẩm</th>
              <th>Giá</th>
              <th>Số lượng</th>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection