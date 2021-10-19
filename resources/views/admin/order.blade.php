@extends ('admin.layout.adminLayout')

@section('content')
  <div class="order">
    <div class="row">
      <div class="col-md-4 col-sm-4 col-12">
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

      <div class="col-md-8 col-sm-8 col-12">
        <div class="row">
          <div class="col-md-2 col-sm-4 col-5">
            <div class="order-category">
              <ul class="nav nav-pills flex-column">
                <li
                  class="nav-item"
                  onclick="getProductByCategory('/admin/order/get-product', 0)"
                >
                  <b class="nav-link active" data-toggle="pill">Tất cả</b>
                </li>
                @foreach ($categories as $_category)
                <li 
                  class="nav-item mr-3"
                  onclick="getProductByCategory('/admin/order/get-product', {{ $_category['id'] }})"
                >
                  <b class="nav-link" data-toggle="pill">{{ $_category['name'] }}</b>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
          <div class="col-md-3 col-sm-8 col-7">
            <div class="table-responsive">
              <div class="order-product">
                <div class="form-group">
                  <label for="">Sản phẩm:</label>
                  <select name="order-product-select" class="form-control order-product-select">
                    @foreach($products as $_product)
                      <option value="{{ $_product['id'] }}">{{ $_product['name'] }}</option>
                      @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="">Số lượng</label>
                  <input type="number" class="form-control" name="number" min="0">
                </div>
                <div class="form-group">
                  <button class="btn btn-primary btn-sm">Thêm</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-7 col-sm-12 col-12">
            <div class="table-responsive">
              <table class="table table-hover table-striped table-bordered">
                <thead class="bg-primary text-light">
                  <th>Sản phẩm</th>
                  <th>Giá</th>
                  <th>Số lượng</th>
                </thead>
                <tbody class="order-tbody-details"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    



    <!-- <div class="modal fade" id="orderModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">This is title</h3>
            <button class="close" data-dismiss="modal" type="button">&times;</button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-2 col-sm-4 col-5">
                <div class="order-category">
                  <ul class="nav nav-pills flex-column">
                    <li
                      class="nav-item mr-3"
                      onclick="getProductByCategory('/admin/order/get-product', 0)"
                    >
                      <b class="nav-link active" data-toggle="pill">Tất cả</b>
                    </li>
                    @foreach ($categories as $_category)
                    <li 
                      class="nav-item mr-3"
                      onclick="getProductByCategory('/admin/order/get-product', {{ $_category['id'] }})"
                    >
                      <b class="nav-link" data-toggle="pill">{{ $_category['name'] }}</b>
                    </li>
                    @endforeach
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-8 col-7">
                <div class="table-responsive">
                  <div class="order-product">
                    <select name="order-product-select" class="form-control order-product-select">
                      @foreach($products as $_product)
                        <option value="{{ $_product['id'] }}">{{ $_product['name'] }}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-7 col-sm-12 col-12">
                <div class="table-responsive">
                  <table class="table table-hover table-striped table-bordered">
                    <thead class="bg-primary text-light">
                      <th>Sản phẩm</th>
                      <th>Giá</th>
                      <th>Số lượng</th>
                    </thead>
                    <tbody class="order-tbody-details"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Xong</button>
          </div>
        </div>
      </div>
    </div> -->
  </div>
@endsection