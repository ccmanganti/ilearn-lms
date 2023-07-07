$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
});

// JOIN CLASS FUNCTIONS
$("#assessment-form").on('submit', function(e){
    e.preventDefault();     
    
    $.ajax({
        url:$(this).attr('action'),
        method:$(this).attr('method'),
        data: $(this).serialize(),
        success: function(data){
            if(data.success){
                window.location.href = "/lms/assignments";
            }
        },
    });
});
