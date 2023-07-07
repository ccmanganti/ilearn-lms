$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
});

// JOIN CLASS FUNCTIONS
$("#join-form").on('submit', function(e){
    e.preventDefault();     
    
    $.ajax({
        url:$(this).attr('action'),
        method:$(this).attr('method'),
        data: $(this).serialize(),
        success: function(data){
            if(data.success == "0"){
                $("#no-class").css('display', 'block');
                $("#exist-class").css('display', 'none');
                $("#with-class").css('display', 'none');
            } else if(data.success == "1"){
                $("#no-class").css('display', 'none');
                $("#exist-class").css('display', 'block');
                $("#with-class").css('display', 'none');
            } else if(data.success == "2"){
                $("#no-class").css('display', 'none');
                $("#exist-class").css('display', 'none');
                $("#with-class").css('display', 'block');
                location.reload();
            }
        },
    });
});
$("#joinclass-btn").click(function(){
    $("#join-form").css('display', 'flex')
})
$("#join-class-box").click(function(){
    $("#join-form").css('display', 'flex')
})
$("#remove-join").click(function(){
    $("#join-form").css('display', 'none')
})


$("#unenroll-classes").click(function(){
    $('.unenroll').toggleClass('active');
    $('#unenroll-classes').toggleClass('active');
    $('#warning-unenroll').toggleClass('active');
    $('#warning-unenroll-mob').toggleClass('active');
})

function submitUnenroll(event, form) {
    event.preventDefault(); // Prevent default form submission

    $.ajax({
        url:$(form).attr('action'),
        method:$(form).attr('method'),
        data: $(form).serialize(),
        success: function(data){
            $('#warning-unenroll').text('Unenrolled successfully!');
            $('#warning-unenroll').css('background-color', 'lightgreen');
            $('#warning-unenroll').css('color', 'darkgreen');
            $('#warning-unenroll').css('border', '1px solid darkgreen');
            location.reload();
        },
        error: function (xhr, status, error) {
            console.log("error");
        }
    });
};


// CLASS FUNCTIONS 
$("#all-posts").click(function(){
    $("#posts").css('display', 'flex');
    $("#posts-only").css('display', 'none');
    $("#tasks-only").css('display', 'none');
    $("#assess-only").css('display', 'none');

    $("#all-posts").addClass('active');
    $("#class-posts").removeClass('active');
    $("#tasks-posts").removeClass('active');
    $("#assess-posts").removeClass('active');
})

$("#class-posts").click(function(){
    $("#posts").css('display', 'none');
    $("#posts-only").css('display', 'flex');
    $("#tasks-only").css('display', 'none');
    $("#assess-only").css('display', 'none');

    $("#all-posts").removeClass('active');
    $("#class-posts").addClass('active');
    $("#tasks-posts").removeClass('active');
    $("#assess-posts").removeClass('active');
})

$("#tasks-posts").click(function(){
    $("#posts").css('display', 'none');
    $("#posts-only").css('display', 'none');
    $("#tasks-only").css('display', 'flex');
    $("#assess-only").css('display', 'none');

    $("#all-posts").removeClass('active');
    $("#class-posts").removeClass('active');
    $("#tasks-posts").addClass('active');
    $("#assess-posts").removeClass('active');
})

$("#assess-posts").click(function(){
    $("#posts").css('display', 'none');
    $("#posts-only").css('display', 'none');
    $("#tasks-only").css('display', 'none');
    $("#assess-only").css('display', 'flex');

    $("#all-posts").removeClass('active');
    $("#class-posts").removeClass('active');
    $("#tasks-posts").removeClass('active');
    $("#assess-posts").addClass('active');
})

// MOBILE CLASS FUNCTIONS 
$("#all-nav").click(function(){
    $("#posts").css('display', 'flex');
    $("#posts-only").css('display', 'none');
    $("#tasks-only").css('display', 'none');
    $("#assess-only").css('display', 'none');

    $("#all-nav").addClass('active');
    $("#class-nav").removeClass('active');
    $("#tasks-nav").removeClass('active');
    $("#assess-nav").removeClass('active');
})

$("#class-nav").click(function(){
    $("#posts").css('display', 'none');
    $("#posts-only").css('display', 'flex');
    $("#tasks-only").css('display', 'none');
    $("#assess-only").css('display', 'none');

    $("#all-nav").removeClass('active');
    $("#class-nav").addClass('active');
    $("#tasks-nav").removeClass('active');
    $("#assess-nav").removeClass('active');
})

$("#tasks-nav").click(function(){
    $("#posts").css('display', 'none');
    $("#posts-only").css('display', 'none');
    $("#tasks-only").css('display', 'flex');
    $("#assess-only").css('display', 'none');

    $("#all-nav").removeClass('active');
    $("#class-nav").removeClass('active');
    $("#tasks-nav").addClass('active');
    $("#assess-nav").removeClass('active');
})

$("#assess-nav").click(function(){
    $("#posts").css('display', 'none');
    $("#posts-only").css('display', 'none');
    $("#tasks-only").css('display', 'none');
    $("#assess-only").css('display', 'flex');

    $("#all-nav").removeClass('active');
    $("#class-nav").removeClass('active');
    $("#tasks-nav").removeClass('active');
    $("#assess-nav").addClass('active');
})
