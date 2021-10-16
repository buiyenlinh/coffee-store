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
})