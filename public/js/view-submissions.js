$.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
});

var previousButtonId = null;
var previousFormId = null;

function validateInput(input) {
    var parsedValue = parseInt(input.value);
    if (isNaN(parsedValue)) {
      input.value = input.min; // Set to minimum value if the entered value cannot be parsed
    } else if (parsedValue > input.max) {
      input.value = input.max;
    } else if (parsedValue < input.min) {
      input.value = input.min;
    }
  }
  

function submitForm(event, form) {
    event.preventDefault(); // Prevent default form submission

    $.ajax({
        url:$(form).attr('action'),
        method:$(form).attr('method'),
        data: $(form).serialize(),
        success: function(data){
            if (previousButtonId) {
                $('#' + previousButtonId).find('button').removeClass('active');
            }
            if (previousFormId) {
                $(previousFormId).removeClass('active');
            }
            var siblingForm = $('#' + form.id).siblings("form").first();
            $('#' + form.id).addClass('active');
            siblingForm.addClass('active');
            $('#'.concat(form.id)).find('button').addClass('active');
            $('#resultContainer').css('justify-content', 'start');

            if(data.type == "t1"){
                var fileLinks = generateFileLinksFromResponse(data.success);
                $('#resultContainer').html(fileLinks);
            }
            
            previousButtonId = form.id;
            previousFormId = $('#' + form.id).siblings("form").first();

            if(data.success == "0" || data.type == "a1"){
                $('#resultContainer').css('justify-content', 'center');
            }

        },
        error: function (xhr, status, error) {
            // Handle the error here
            console.log("error");
        }
    });
};

function submitScoreForm(event, form) {
    event.preventDefault(); // Prevent default form submission

    $.ajax({
        url:$(form).attr('action'),
        method:$(form).attr('method'),
        data: $(form).serialize(),
        success: function(data){
            var scoreInputValue = $(form).find('input#scoreInput').val();
            $('#changedScore').text(scoreInputValue);
            $(form).find('button').css('background-color', 'green');
            $(form).find('button').text('Submitted');
            setTimeout(function() {
                $(form).find('button').css('background-color', 'var(--class-color)');
                $(form).find('button').text('Submit');
        }, 1000);
        },
        error: function (xhr, status, error) {
            // Handle the error here
            console.log("error");
        }
    });
};

function generateFileLinksFromResponse(response) {
    // Extract the files array from the response object

    // Generate the HTML links for each file
    var linksHTML = '';
    for (var i = 0; i < response.length; i++) {
        var fileLink = '/storage/' + response[i];
        var modifiedUrl = response[i].substring(response[i].indexOf('/') + 1);
        var linkHTML = '<a class="submitted-files" href="' + fileLink + '" target="_blank">'+ modifiedUrl + '</a>';
        linksHTML += linkHTML;
    }
    if(response == "0"){
        var linksHTML = '<h2 class="instruct-view" id="instruct">No files submitted yet.</h2>';
    }

    // Return the HTML links
    return linksHTML;
}