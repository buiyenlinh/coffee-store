@extends ('admin.layout.adminLayout')

@section('content')
  <div class="order">
    <div class="row">
      <div class="col-md-6 col-sm-6 col-12">
        <div class="order-place">
          <ul class="nav nav-pills">
            <li
              class="nav-item mr-3"
              onclick="searchTable('/admin/order/search-table', 0)"
            >
              <b class="nav-link active" data-toggle="pill">Tất cả</b>
            </li>
            @foreach ($places as $_place)
              @if($_place['active'])
                <li
                  class="nav-item mr-3"
                  onclick="searchTable('/admin/order/search-table', {{ $_place['id'] }})"
                >
                  <b class="nav-link" data-toggle="pill">{{ $_place['name'] }}</b>
                </li>
              @endif
            @endforeach
          </ul>
        </div>
        <div class="order-table">
          <ul class="order-table-list row"></ul>
        </div>
      </div>

      <div class="col-md-6 col-sm-6 col-12">
        <div class="order-category">
          <ul class="nav nav-pills">
            <li
              class="nav-item"
              onclick="getProductByCategory('/admin/order/get-product', 0)"
            >
              <b class="nav-link active" data-toggle="pill">Tất cả</b>
            </li>
            @foreach ($categories as $_category)
              @if($_category['active'])
                <li
                  class="nav-item mr-3"
                  onclick="getProductByCategory('/admin/order/get-product', {{ $_category['id'] }})"
                >
                  <b class="nav-link" data-toggle="pill">{{ $_category['name'] }}</b>
                </li>
              @endif
            @endforeach
          </ul>
        </div>

        <div class="order-product">
          <div class="form-group">
            <label for="">Sản phẩm:</label>
            <select name="order-product-select" class="form-control order-product-select">
              @foreach($products as $_product)
                @if($_product['active'] && $_product['active_parent'])
                  <option value="{{ $_product['id'] }}">{{ $_product['name'] }}</option>
                @endif
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="">Số lượng</label>
            <input type="number" class="form-control order_product_number" name="order_product_number" min="0">
          </div>
          <div class="form-group">
            <button class="btn btn-primary order-btn-submit" type="button">Thêm</button>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between mb-2 mt-2">
      <h3 class="table-name-select mb-0"></h3>
      <div>
        <button class="btn btn-success btn-sm order-pay-bill">Thanh toán</button>
        <button class="btn btn-info btn-sm text-light cancel-select-table">Hủy chọn bàn</button>
        <button class="btn btn-primary btn-sm order-move-table-btn">Chuyển bàn</button>
        <button class="btn btn-primary btn-sm order-merge-table-btn">Gộp bàn</button>
        <button class="btn btn-danger btn-sm order-cancel-table">Hủy bàn</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-striped table-bordered">
        <thead class="bg-primary text-light">
          <th>Sản phẩm</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <th>Thao tác</th>
        </thead>
        <tbody class="order-tbody-details"></tbody>
      </table>
    </div>
    

    <div class="modal fade" id="orderModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Sửa chi tiết</h3>
            <button class="close" data-dismiss="modal" type="button">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="">Sản phẩm:</label>
              <select name="order-product-select" class="form-control order-product-select-update">
                @foreach($products as $_product)
                  <option value="{{ $_product['id'] }}">{{ $_product['name'] }}</option>
                  @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="">Số lượng</label>
              <input type="number" class="form-control order_product_number_update" name="order_product_number" min="0">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary order-btn-update" data-dismiss="modal">Lưu</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <button data-toggle="modal" data-target="#orderMoveTable" class="order-button-move-table" style='display: none'></button>
    <div class="modal fade" id="orderMoveTable">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Chuyển bàn</h3>
            <button class="close" data-dismiss="modal" type="button">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="">Bàn chuyển:</label>
              <input type="text" class="order-table-name-move form-control" value="" disabled>
            </div>
            <div class="form-group">
              <label for="">Sang bàn:</label>
              <select name="order-move-to-table" class="form-control order-move-to-table"></select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary order-btn-move-table" data-dismiss="modal">Chuyển</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection