var wwwRoot = getWwwRoot();

function getWwwRoot() {
  var pos = window.location.href.indexOf('/www/');
  if (pos == -1) {
    return '/';
  } else {
    return window.location.href.substr(0, pos + 5);
  }
}

$.datepicker.setDefaults({
  changeMonth: true,
  changeYear: true,
  minDate: new Date(1800, 0, 1),
  maxDate: new Date(2100, 11, 31),
  yearRange: 'c-30:c+2',
});

// Facebook stuff
function facebookInit(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}

function actSelectYearChange(actType, yearSelect, numberSelect, button) {
  var options = '<option value="">numărul...</option>';
  numberSelect.attr('disabled', 'disabled');
  numberSelect.html(options);
  button.attr('disabled', 'disabled');
  if (yearSelect.val()) {
    $.getJSON(wwwRoot + 'ajax/actList.php', { type: actType, year: yearSelect.val() }, function(data) {
      for (var i = 0; i < data.length; i++) {
        options += '<option value="' + data[i][0] + '">' + data[i][1] + '</option>';
      }
      numberSelect.html(options);
      if (data.length) {
        numberSelect.removeAttr('disabled');
      }
    })
  }
}

function actSelectNumberChange(numberSelect, button) {
  if (numberSelect.val()) {
    button.removeAttr('disabled');
  } else {
    button.attr('disabled', 'disabled');
  }
}
