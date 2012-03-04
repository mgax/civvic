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
