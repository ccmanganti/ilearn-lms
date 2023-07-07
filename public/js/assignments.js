$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
});

$("#all-assigns").click(function(){
    $("#all-assignments").addClass('active');
    $("#all-assignments").css('flex-basis', '60%');
    $("#assignments-overdue").removeClass('active');
    $("#assignments-overdue").css('flex-basis', '0');
    $("#assignments-submitted").removeClass('active');
    $("#assignments-submitted").css('flex-basis', '0');

    $("#all-assigns").addClass('active');
    $("#overdue-assigns").removeClass('active');
    $("#submitted-assigns").removeClass('active');
})

$("#overdue-assigns").click(function(){
    $("#all-assignments").removeClass('active');
    $("#all-assignments").css('flex-basis', '0');
    $("#assignments-overdue").addClass('active');
    $("#assignments-overdue").css('flex-basis', '60%');
    $("#assignments-submitted").removeClass('active');
    $("#assignments-submitted").css('flex-basis', '0');

    $("#all-assigns").removeClass('active');
    $("#overdue-assigns").addClass('active');
    $("#submitted-assigns").removeClass('active');
})

$("#submitted-assigns").click(function(){
    $("#all-assignments").removeClass('active');
    $("#all-assignments").css('flex-basis', '0');
    $("#assignments-overdue").removeClass('active');
    $("#assignments-overdue").css('flex-basis', '0');
    $("#assignments-submitted").addClass('active');
    $("#assignments-submitted").css('flex-basis', '60%');

    $("#all-assigns").removeClass('active');
    $("#overdue-assigns").removeClass('active');
    $("#submitted-assigns").addClass('active');
})




$("#all-assigns-mob").click(function(){
    $("#all-assignments").addClass('active');
    $("#all-assignments").css('flex-basis', '60%');
    $("#assignments-overdue").removeClass('active');
    $("#assignments-overdue").css('flex-basis', '0');
    $("#assignments-submitted").removeClass('active');
    $("#assignments-submitted").css('flex-basis', '0');

    $("#all-assigns-mob").addClass('active');
    $("#overdue-assigns-mob").removeClass('active');
    $("#submitted-assigns-mob").removeClass('active');
})

$("#overdue-assigns-mob").click(function(){
    $("#all-assignments").removeClass('active');
    $("#all-assignments").css('flex-basis', '0');
    $("#assignments-overdue").addClass('active');
    $("#assignments-overdue").css('flex-basis', '60%');
    $("#assignments-submitted").removeClass('active');
    $("#assignments-submitted").css('flex-basis', '0');

    $("#all-assigns-mob").removeClass('active');
    $("#overdue-assigns-mob").addClass('active');
    $("#submitted-assigns-mob").removeClass('active');
})

$("#submitted-assigns-mob").click(function(){
    $("#all-assignments").removeClass('active');
    $("#all-assignments").css('flex-basis', '0');
    $("#assignments-overdue").removeClass('active');
    $("#assignments-overdue").css('flex-basis', '0');
    $("#assignments-submitted").addClass('active');
    $("#assignments-submitted").css('flex-basis', '60%');

    $("#all-assigns-mob").removeClass('active');
    $("#overdue-assigns-mob").removeClass('active');
    $("#submitted-assigns-mob").addClass('active');
})