document.addEventListener('DOMContentLoaded', function() {
  let dateinput = document.querySelector(".dat:checked");
  let timesdivid = ( dateinput ) ? dateinput.value : ''; ;
  let timesdiv = (document.querySelector("#t"+timesdivid)) ? document.querySelector("#t"+timesdivid) : '';
  (timesdiv) ? timesdiv.style.display = '' : false;

  var el = document.querySelectorAll(".dat");
  for (var i = 0; i < el.length; i++) {
    el[i].onclick = function(e) {
      document.querySelectorAll('.master_times').forEach(function(ee) {
         if (ee.id+'d' == 't'+e.target.id) {
           ee.style.display = '';
         }
         else {
           ee.style.display = 'none';
         }
      });
      let times = document.querySelectorAll('.master_datetime input[name="time"]');
      times.forEach(function(eee){
        if (eee.checked) {
          eee.checked = false;
        }
      });
    };
  }
});
