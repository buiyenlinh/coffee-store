var order = {
  'table_id' : null,
  'product_id' : null,
  'number' : null
};

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
        window.location.reload();
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
        li.className = 'col-md-3 col-sm-4 col-xs-6'
        li.innerHTML = json.tables[i].name;
        // li.setAttribute('data-toggle', 'modal');
        // li.setAttribute('data-target', '#orderModal');
        li.onclick = function() {
          getBillDetail(json.tables[i].id);
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
      $('.order-tbody-details').text('');
      for (i in json.details) {
        var tr = document.createElement('tr');
        var td_name = document.createElement('td');
        var td_price = document.createElement('td');
        var td_number = document.createElement('td');
        td_name.innerHTML = json.details[i].product?.name;
        td_price.innerHTML = json.details[i].product?.price;
        td_number.innerHTML = json.details[i].dt?.number;
        tr.append(td_name, td_price, td_number);
        $('.order-tbody-details').append(tr);
      }
      console.log(json);
    }
  })
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
})