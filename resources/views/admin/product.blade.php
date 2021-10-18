@extends ('admin.layout.adminLayout')

@section('content')
  @if($data_form)
    <h3>{{ $data_form['id'] ? 'Cập nhật' : 'Thêm' }} sản phẩm</h3>
    <div class="row">
      <div class="col-md-5 col-sm-12 col-xs-12">
        <div class="product-form">
            @if(session('error_role'))
              <div class="text-danger">
                {{ session('error_role') }}
              </div>
            @endif

            @if($data_form['id'] > 0)
            <form action="/admin/product/edit?id={{$data_form['id']}}" method="POST">
            @else
            <form action="/admin/product/add" method="POST">
            @endif
            @csrf
            <div class="form-group">
              <label for="">Tên sản phẩm</label>
              <input type="text" name="name" class="form-control" value="{{ old('old_name', $data_form['name']) }}">
              @error('name')
                <div class="text-danger">
                  {{ $message }}
                </div>
              @enderror

              @if(session('error_name'))
                <div class="text-danger">
                  {{ session('error_name') }}
                </div>
              @endif
            </div>

            <div class="form-group">
              <label for="">Giá sản phẩm:</label>
              <input type="number" name="price" min="0" step="1000" class="form-control" value="{{ old('old_price', $data_form['price']) }}">
              @if(session('error_price'))
                <div class="text-danger">
                  {{ session('error_price') }}
                </div>
              @endif
            </div>

            <div class="form-group">
              <label for="">Loại sản phẩm:</label>
              <select name="category_id" id="" class="form-control">
                @foreach($categories as $_category)
                  <option value="{{ $_category['id'] }}" {{ $_category['id'] == $data_form['category_id'] ? 'selected' : '' }} >{{ $_category['name'] }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="">Kích hoạt:</label>
              <input type="checkbox" name="active" {{ $data_form['active'] ? 'checked' : ''}}>
            </div>

            <div class="form-group">
              <label for="">Trạng thái (Còn hàng):</label>
              <input type="checkbox" name="status" {{ $data_form['status'] ? 'checked' : ''}} >
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary">
                {{ $data_form ? 'Cập nhật' : 'Thêm' }}
              </button>
              <a href="/admin/product" class="btn btn-secondary">Trở về</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  @else
    <div class="d-flex justify-content-between pb-2">
      <h3 class="mb-0">Danh sách sản phẩm</h3>
      <a href="/admin/product?add" class="btn btn-primary">Thêm</a>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-bordered table-striped">
        <thead class="bg-primary text-light">
          <th>Tên sản phẩm</th>
          <th>Giá</th>
          <th>Trạng thái</th>
          <th>Hoạt động</th>
          <th>Loại sản phẩm</th>
          <th>Thao tác</th>
        </thead>
        <tbody>
          @foreach($products as $_product)
            <tr>
              <td>{{ $_product['name'] }}</td>
              <td>{{ $_product['price'] }}</td>
              <td>{{ $_product['status'] ? 'Còn' : 'Hết' }}</td>
              <td>{{ $_product['active'] ? 'Đã kích hoạt' : 'Chưa kích hoạt' }}</td>
              <td>
                @foreach($categories as $_category)
                  @if($_category['id'] == $_product['category_id']) {{$_category['name']}} @endif
                @endforeach
              </td>
              <td>
                <a onclick="handleDelete('/admin/product/delete', {{ $_product['id'] }})" class="text-danger">
                  <i class="fas fa-trash-alt"></i>
                </a>
                <a href="/admin/product?edit={{ $_product['id'] }}" class="text-primary">
                  <i class="fas fa-edit"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
@endsection