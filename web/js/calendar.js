(function ($) {
  
//$(".event").popover();

// навигация
$('.select_month>li>a').click(function(){
  $.ajax({
    url: "ajax?day_inc="+$(this).attr('inc'),
    success:function(r){
      location.reload()
    }
  });
  return false;
});

// добавить событие
$('#addevent').click(function(){
  $.ajax({
    url: "ajax?addevent=1",
    data:collectData($("#myModal .modal-body")),
    success:function(r){
      if(r)alert(r);
      else location.reload();
    }
  });
  return false;
});

// удалить событие
$('.event .del').click(function(){
  if(!confirm("Вы действительно хотите удалить событие?"))return false;
  $.ajax({
    url: "ajax?delevent="+$(this).closest('.event').attr('i'),
    success:function(r){
      location.reload();
    }
  });
  return false;
});

// вызов формы добавления
$('.addevent_modal').click(function(){
  var tm="09:00";
  if($(this).closest('table').hasClass('calendar_day'))tm=$(this).closest('[tm]').attr('tm');

    $('#calendar-date').val($(this).closest('[dt]').attr('dt')+' '+tm);
    $("#myModal .modal-body *[name=eventid]").val(0);
});

// вызов формы редактирования
$('.event .edit').click(function(){
  $("#myModal .modal-body *[name=type]").val($(this).closest('.event').attr('tp'));
  $("#myModal .modal-body *[name=eventid]").val($(this).closest('.event').attr('i'));
  
  
  if($(this).closest('table').hasClass('month')){
    $("#myModal .modal-body *[name=descr]").val($(this).closest('.event').attr('data-content'));
    $("#myModal .modal-body *[name=name]").val($(this).closest('.event').find('.name').text());
    $('#datetimepicker').data("DateTimePicker").setDate($(this).closest('[dt]').attr('dt')' '+$(this).closest('.event').find('.time').text());
  }
  if($(this).closest('table').hasClass('calendar_week')){
    $("#myModal .modal-body *[name=descr]").val($(this).closest('.event').find("[name=descr]").text());
    $("#myModal .modal-body *[name=name]").val($(this).closest('.event').find('[name=name]').text());
    $('#datetimepicker').data("DateTimePicker").setDate($(this).closest('[dt]').attr('dt')+' '+$(this).closest('.event').find('[name=time]').text());
  }
  if($(this).closest('table').hasClass('calendar_day')){
    $("#myModal .modal-body *[name=descr]").val($(this).closest('.event').find("[name=descr]").text());
    $("#myModal .modal-body *[name=name]").val($(this).closest('.event').find('[name=name]').text());
    $('#datetimepicker').data("DateTimePicker").setDate($(this).closest('[dt]').attr('dt')+' '+$(this).closest('[tm]').attr('tm'));
  }
    

  $("#myModal").modal();
  return false;
});

// клик на типах событий
$('.event_types input').change(function(){
  var tp=$(this).closest('label').attr('tp');
  if($(this).prop('checked'))  $(".event."+tp).show();
  else $(".event."+tp).hide();
  localStorage[tp]=$(this).prop('checked');
});


/*
  $(function () {
    //Идентификатор элемента HTML (например: #datetimepicker1), для которого необходимо инициализировать виджет "Bootstrap datetimepicker"
    $('#datetimepicker').datetimepicker({
    language: 'ru'
  });
  });
*/

$(function(){
  for(i in localStorage){
    $('.event_types label[tp='i+'] input').prop('checked',localStorage[i]=='true').change();
  }
});

// добавить событие
$('.event-edit').click(function(){
    id = $(this).data("id");
    $.get( "update", {id:id}, function( data ) {
      $("#modalevent").find("#modalContent").html(data);
      $("#modalevent").modal();
    });
return false;
});



})(jQuery);
