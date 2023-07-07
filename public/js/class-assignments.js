$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
});

$("#all-assigns").click(function(){
    $("#all-assigns").addClass('active');
    $("#assigned").removeClass('active');
    $("#overdue-assigns").removeClass('active');
    $("#task-assigns").removeClass('active');
    $("#assess-assigns").removeClass('active');

    $("#all-assignments-content").addClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#assigned").click(function(){
    $("#all-assigns").removeClass('active');
    $("#assigned").addClass('active');
    $("#overdue-assigns").removeClass('active');
    $("#task-assigns").removeClass('active');
    $("#assess-assigns").removeClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").addClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#overdue-assigns").click(function(){
    $("#all-assigns").removeClass('active');
    $("#assigned").removeClass('active');
    $("#overdue-assigns").addClass('active');
    $("#task-assigns").removeClass('active');
    $("#assess-assigns").removeClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").addClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#task-assigns").click(function(){
    $("#all-assigns").removeClass('active');
    $("#assigned").removeClass('active');
    $("#overdue-assigns").removeClass('active');
    $("#task-assigns").addClass('active');
    $("#assess-assigns").removeClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").addClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#assess-assigns").click(function(){
    $("#all-assigns").removeClass('active');
    $("#assigned").removeClass('active');
    $("#overdue-assigns").removeClass('active');
    $("#task-assigns").removeClass('active');
    $("#assess-assigns").addClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").addClass('active');
});

// For Mobile

$("#all-assigns-mob").click(function(){
    $("#all-assigns-mob").addClass('active');
    $("#assigned-mob").removeClass('active');
    $("#overdue-assigns-mob").removeClass('active');
    $("#task-assign-mobs").removeClass('active');
    $("#assess-assigns-mob").removeClass('active');

    $("#all-assignments-content").addClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#assigned-mob").click(function(){
    $("#all-assigns-mob").removeClass('active');
    $("#assigned-mob").addClass('active');
    $("#overdue-assigns-mob").removeClass('active');
    $("#task-assigns-mob").removeClass('active');
    $("#assess-assigns-mob").removeClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").addClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#overdue-assigns-mob").click(function(){
    $("#all-assigns-mob").removeClass('active');
    $("#assigned-mob").removeClass('active');
    $("#overdue-assigns-mob").addClass('active');
    $("#task-assigns-mob").removeClass('active');
    $("#assess-assigns-mob").removeClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").addClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#task-assigns-mob").click(function(){
    $("#all-assigns-mob").removeClass('active');
    $("#assigned-mob").removeClass('active');
    $("#overdue-assigns-mob").removeClass('active');
    $("#task-assigns-mob").addClass('active');
    $("#assess-assigns-mob").removeClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").addClass('active');
    $("#all-assess-content").removeClass('active');
});

$("#assess-assigns-mob").click(function(){
    $("#all-assigns-mob").removeClass('active');
    $("#assigned-mob").removeClass('active');
    $("#overdue-assigns-mob").removeClass('active');
    $("#task-assigns-mob").removeClass('active');
    $("#assess-assigns-mob").addClass('active');

    $("#all-assignments-content").removeClass('active');
    $("#all-assigned-content").removeClass('active');
    $("#all-overdue-content").removeClass('active');
    $("#all-task-content").removeClass('active');
    $("#all-assess-content").addClass('active');
});