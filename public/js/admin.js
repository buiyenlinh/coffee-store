var order = {
  'table_id' : null,
  'product_id' : null,
  'number' : null,
  'detail_id': null
};

var detail_id = null;

function handleDelete(url, id) {
  if (!confirm('Bạn chắc chắn muốn xóa?')) {
    return false;
  }

  $.ajax({
    type: 'POST',
		cache: false,
		url: url,
    data: 'id=' + id,
		dataType: 'json'
  }).done(function(json) {
    if (json.status == 'ERR') {
      alert(json.error);
    } else {
      if (json.redirect) {
        window.location.href = json.redirect;
      } else {
        if (json.act) {
          showDetail(json.details);
        } else {
          window.location.reload();
        }
      }
    }
  })
}

function searchTable(url, id) {
  $.ajax({
    type: 'GET',
    cache: false,
    url: url,
    data: 'id=' + id,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      $('.order-table-list').text('');
      for (let i in json.tables) {
        var li = document.createElement('li');
        var img = document.createElement('img');
        
        if (json.tables[i].status) {
          li.innerHTML = json.tables[i].name + '<br> [Có người] <br>';
        } else {
          li.innerHTML = json.tables[i].name + '<br> [Trống] <br>';
        }

        if (json.tables[i].active) {
          li.onclick = function() {
            order.table_id = json.tables[i].id;
            getBillDetail(json.tables[i].id);
            $('.table-name-select').text(json.tables[i].name);
          }
          li.className = 'col-md-3 col-sm-4 col-xs-6';
        } else {
          li.className = 'col-md-3 col-sm-4 col-xs-6 active-false';
        }
        
        img.setAttribute('src', 'https://cdn2.iconfinder.com/data/icons/home-linear-black/2048/4443_-_Coffee_Table-512.png');
        li.append(img);
        $('.order-table-list').append(li);
      }
    }
  })
}

function getBillDetail(id) {
  $.ajax({
    type: 'GET',
    cache: false,
    url: '/admin/order/get-bill-detail',
    data: 'id=' + id,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      showDetail(json.details);
    }
  })
}

function showDetail(details) {
  $('.order-tbody-details').text('');
  for (i in details) {
    var tr = document.createElement('tr');
    var td_name = document.createElement('td');
    var td_price = document.createElement('td');
    var td_number = document.createElement('td');
    var td = document.createElement('td');
    var a_delete = document.createElement('a');
    var a_update = document.createElement('a');

    a_delete.className = 'btn btn-danger btn-sm mr-2';
    a_delete.innerHTML = 'Xóa';
    a_delete.onclick = function() {
      handleDelete('/admin/order/delete', details[i].dt.id);
    }
    a_update.className = 'btn btn-primary btn-sm';
    a_update.innerHTML = 'Sửa';
    a_update.setAttribute('data-toggle', 'modal');
    a_update.setAttribute('data-target', '#orderModal');
    a_update.onclick = function() {
      order.detail_id = details[i].dt.id;
      $('.order-product-select-update option[value=' + details[i].product.id + ']').attr('selected', 'selected');
      $('.order_product_number_update').val(details[i].dt.number);
    }

    td.append(a_delete, a_update);
    td_name.innerHTML = details[i].product?.name;
    td_price.innerHTML = details[i].product?.price;
    td_number.innerHTML = details[i].dt?.number;
    tr.append(td_name, td_price, td_number, td);
    $('.order-tbody-details').append(tr);
  }
}

function getProductByCategory(url, id) {
  $.ajax({
    type: 'GET',
    cache: false,
    url: url,
    data: 'id=' + id,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      console.log(json);
      $('.order-product-select').text('');
      for (i in json.products) {
        var option = document.createElement('option');
        option.innerHTML = json.products[i].name;
        option.value = json.products[i].id;
        $('.order-product-select').append(option);
      }
    }
  })
}

function addProductToBill() {
  order.product_id = $('.order-product-select').val();
  order.number = $('.order_product_number').val();
  $.ajax({
    type: 'POST',
    url: '/admin/order/add',
    data: order,
    catch: false,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      showDetail(json.details);
    }
  })
}

function updateProductInBill() {
  order.number = $('.order_product_number_update').val();
  if (order.number == '' || order.number == 0) {
    alert('Số lượng sản phẩm là lớn hơn 0');
    return;
  }

  order.product_id = $('.order-product-select-update').val();
  $.ajax({
    type: 'POST',
    url: '/admin/order/update',
    data: order,
    catch: false,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      showDetail(json.details);
    }
  })
}

function cancelTable(table_id) {
  if (table_id == null) {
    alert('Vui lòng chọn bàn');
    return;
  }

  $.ajax({
    type: 'POST',
    cache: false,
    url: '/admin/order/cancel-table',
    data: 'table_id=' + table_id,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      console.log(json);
    }
  });
}

$(function() {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  })

  $('[data-toggle="sidebar"]').on('click', function() {
    $('.wrapper').toggleClass('wrapper-sidebar-mini');
  })

  $('.sidebar-overlay').on('click', function() {
    $('.wrapper').removeClass('wrapper-sidebar-mini');
  })

  searchTable('/admin/order/search-table', 0);

  $('.order-btn-submit').on('click', function() {
    addProductToBill();
  })

  $('.order-btn-update').on('click', function() {
    updateProductInBill();
  })

  $('.order-cancel-table').on('click', function() {
    handleDelete('/admin/order/cancel-table', order.table_id);
  })
})