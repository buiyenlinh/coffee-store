var order = {
  'table_id' : null,
  'table_name': null,
  'product_id' : null,
  'number' : null,
  'detail_id': null
};

var detail_id = null;
var place_id = 0;
var table_move_id = 0;

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

function handleCancelTable(url, id) {
  if (id == null || id == '') {
    alert("Vui lòng chọn bàn để hủy");
    return;
  }

  if (!confirm('Bạn chắc chắn muốn hủy bàn này?')) {
    return false;
  }

  $.ajax({
    type: 'POST',
		cache: false,
		url: url,
    data: 'id=' + id,
		dataType: 'json'
  }).done(function(json) {
    console.log(json);
    if (json.status == 'ERR') {
      alert(json.message);
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
  place_id = id;
  console.log(place_id);
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

        if (json.tables[i].active && json.tables[i].active_parent) {
          li.onclick = function() {
            order.table_id = json.tables[i].id;
            order.table_name = json.tables[i].name;
            getBillDetail(json.tables[i].name);
            $('.table-name-select').text(json.tables[i].name);
          }
          li.className = 'col-md-3 col-sm-4 col-xs-6';
        } else {
          li.className = 'col-md-3 col-sm-4 col-xs-6 active-false';
          li.innerHTML = json.tables[i].name + '<br> [Chưa kích hoạt] <br>';
        }
        
        img.setAttribute('src', 'https://cdn2.iconfinder.com/data/icons/home-linear-black/2048/4443_-_Coffee_Table-512.png');
        li.append(img);
        $('.order-table-list').append(li);
      }
    }
  })
}

function getBillDetail(name) {
  $.ajax({
    type: 'GET',
    cache: false,
    url: '/admin/order/get-bill-detail',
    data: 'name=' + name,
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
      handleDelete('/admin/order/delete', details[i].id);
    }
    a_update.className = 'btn btn-primary btn-sm';
    a_update.innerHTML = 'Sửa';
    a_update.setAttribute('data-toggle', 'modal');
    a_update.setAttribute('data-target', '#orderModal');
    a_update.onclick = function() {
      order.detail_id = details[i].id;
      $('.order-product-select-update option[value=' + details[i].id + ']').attr('selected', 'selected');
      $('.order_product_number_update').val(details[i].number);
    }

    td.append(a_delete, a_update);
    td_name.innerHTML = details[i].product;
    td_price.innerHTML = details[i].price;
    td_number.innerHTML = details[i].number;
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
        if (json.products[i].active && json.products[i].active_parent && json.products[i].status) {
          var option = document.createElement('option');
          option.innerHTML = json.products[i].name;
          option.value = json.products[i].id;
          $('.order-product-select').append(option);
        }
      }
    }
  })
}

function addProductToBill() {
  order.product_id = $('.order-product-select').val();
  order.number = $('.order_product_number').val();
  if (order.number <= 0) {
    alert('Số lượng phải lớn hơn 0');
    return;
  }
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

function handlePayBill(table_name) {
  if (table_name == null || table_name == "") {
    alert('Vui lòng chọn bàn');
    return;
  }

  if (!confirm('Bạn muốn thanh toán?')) {
    return;
  }

  $.ajax({
    type: 'POST',
    data: 'table_name=' + table_name,
    url: '/admin/order/pay',
    cache: false,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'ERR') {
      alert(json.message);
    } else {
      if (json.redirect) {
        window.location.href = json.redirect;
      }
    }
  });
}

function getTableMoveMerge(table_id, url) {
  if (table_id == null) {
    alert('Vui lòng chọn bàn');
    return;
  }

  $.ajax({
    type: 'POST',
    cache: false,
    url: url,
    data: 'table_id=' + table_id,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'ERR') {
      alert(json.message);
    } else {
      $('.order-move-to-table').text('');
      $('.order-table-name-move').val(json.table_move['name']);
      table_move_id = json.table_move['id'];
      for (let i in json.response) {
        var option = document.createElement('option');
        option.innerHTML = json.response[i]['name'];
        option.setAttribute('value', json.response[i]['id']);
        $('.order-move-to-table').append(option);
      }
      
      $('.order-button-move-table').click();
    }
  })
}


// chuyển bàn
function moveMergeTable() {
  var tableMoveTo = $('.order-move-to-table').val();
  if (tableMoveTo == null || tableMoveTo <= 0) {
    alert('Vui lòng chọn bàn chuyển đến');
    return;
  }

  if (table_move_id <= 0 || table_move_id == null) {
    alert("Đã xảy ra lỗi vui lòng thực hiện lại");
    return;
  }

  $.ajax({
    type: 'POST',
    cache: false,
    url: '/admin/order/move-table',
    data: 'table_move_id=' + table_move_id + '&table_move_to_id=' + tableMoveTo,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      if (json.redirect) {
        window.location.href = json.redirect;
      }
    }
  })
}


function deleteAvatarProfile() {
  $.ajax({
    type: 'POST', 
    cache: false,
    url: '/admin/profile/delete-avatar',
    data: '',
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      if (json.redirect) {
        window.location.href = json.redirect
      }
    } 
  })
}

function logout() {
  $.ajax({
    type: 'POST',
    cache: false,
    url: '/admin/auth/logout',
    data: '',
    dataType: 'json'
  }).done(function(json) {
    if (json.status == 'OK') {
      window.location.href = json.redirect;
    }
  })
}


function billGetDetail(bill_id) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: '/admin/bill/detail-bill',
    data: 'bill_id=' + bill_id,
    dataType: 'json'
  }).done(function(json) {
    if (json.status == "OK") {
      $('.bill-details tbody').text("");
      var moneySum = 0;
      for (i in json.details) {
        console.log(json.details[i]);
        var tr = document.createElement('tr');
        var td_name = document.createElement('td');
        var td_price = document.createElement('td');
        var td_number = document.createElement('td');
        var td_username = document.createElement('td');
        var td_time = document.createElement('td');

        td_time.innerHTML = json.details[i].created_at;
        td_name.innerHTML = json.details[i].product;
        td_price.innerHTML = json.details[i].price;
        td_number.innerHTML = json.details[i].number;
        td_username.innerHTML = json.details[i].username;

        moneySum += json.details[i].price * json.details[i].number;

        tr.append(td_time, td_name, td_price, td_number, td_username);
        $('.bill-details tbody').append(tr);
        $('.bill-money-sum').text('Tổng tiền: ' + moneySum + ' VNĐ');
      }
    }
  })
}


$(function() {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF_TOKEN
    }
  })

  $('.logout').on('click', function() {
    logout();
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
    console.log(place_id);
    searchTable('/admin/order/search-table', place_id)
  })

  $('.order-btn-update').on('click', function() {
    updateProductInBill();
  })

  $('.order-cancel-table').on('click', function() {
    handleCancelTable('/admin/order/cancel-table', order.table_id);
  })

  $('.order-pay-bill').on('click', function() {
    handlePayBill(order.table_name);
  })

  $('.order-move-table-btn').on('click', function() {
    getTableMoveMerge(order.table_id, '/admin/order/get-table-move');
  })

  $('.order-merge-table-btn').on('click', function() {
    getTableMoveMerge(order.table_id, '/admin/order/get-table-merge');
  })

  $('.cancel-select-table').on('click', function() {
    order = {
      'table_id' : null,
      'product_id' : null,
      'number' : null,
      'detail_id': null
    };
    detail_id = null;
    $('.table-name-select').text('');
    $('.order-tbody-details').text('');
  })

  $('.order-btn-move-merge-table').on('click', function() {
    moveMergeTable();
  })

  // click avatar

  $('.user-btn-add-avt').on('click', function() {
    $('.user-input-avt').click();
  })

  $('.profile-avatar-change-button').on('click', function() {
    $('#profile-avatar-change').click();
  })

  $('#profile-avatar-change').on('change', function() {
    var avt = $('#profile-avatar-change')[0].files;
    var urlPreview = URL.createObjectURL(avt[0]);
    $('.profile-avatar img').attr('src', urlPreview);
  })


  $('.profile-avatar-delete-button').on('click', function() {
    deleteAvatarProfile();
  })

})