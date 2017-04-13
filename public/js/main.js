tinymce.init({
    selector: ".editor",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern"
    ],
    toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons | pagebreak"
});

$(document).ready(function(){
    $('.fancybox').fancybox({
        type: 'image'
    });

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    $(document).on('click', '.nav-tabs a', function(event){
        $(this).tab('show');
    });

    $(".delete-submission").click(function() {
        return confirm('Are you sure you want to delete this submission? This process is irreversible!');
    });

    if($(".submission-edit").length) {
        var frm = $(".submission-edit"),
            submissionId = frm.data('id');
        var myDropzone = $(".drop-resource").dropzone({ url: "/submission/edit/"+ submissionId, init: function(){
            this.on("complete", function(file) {
                var list = $(".resource-list");
                if(file.status == 'success') {
                    $(file.previewElement).remove();
                    var resource = $.parseJSON(file.xhr.response),
                        type = resource.type,
                        li = $("<li>").addClass("list-group-item "+ resource.type).attr('data-id', resource.id);

                    if(type == 'image') {
                        li.append('<div class="row"><div class="col-sm-3"><img class="img-responsive" src="/submissions/'+ submissionId +'/media/'+ resource.id +'/media" /></div></div>');
                    } else {
                        li.append('<div class="row"><div class="col-sm-3"><a href="/submissions/'+ submissionId +'/media/'+ resource.id +'/media">Download ' + resource.file_path + '</a></div></div>');
                    }

                    list.find("li.empty").remove();
                    list.append(li)
                } else {
                    $(file.previewElement).remove();
                    bootbox.alert("An error occurred during the file upload: "+ file.xhr.statusText);
                }
            });
        } });

    }

});