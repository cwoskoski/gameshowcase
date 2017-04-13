$(document).ready(function() {

    $(".deny-submission").click(function() {
        var comment = prompt('What is the reason for denying this submission?');
        if (!comment || comment.length < 1) {
            alert('You must provide a reason for denying the submission.');
            return false;
        }

        $.post($(this).attr('href'), { comment: comment }, function(resp) {
            location.reload();
        });

        return false;
    });
	
});