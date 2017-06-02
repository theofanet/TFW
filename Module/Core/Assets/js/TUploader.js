function TUploader(elem_id, route){
    this.elem_id = elem_id;

    this.dropbox = $(this.elem_id);
    this.message = $('.message', this.dropbox);
    this.data    = {};
    this.route   = route;

    this.callback = function(data){};

    this.template = '<div class="preview">'+
    '<span class="imageHolder">'+
    '<img />'+
    '<span class="uploaded"></span>'+
    '</span>'+
    '<div class="file_name"></div>'+
    '<div class="progressHolder">'+
    '<div class="progress"></div>'+
    '</div>'+
    '</div>';
}

TUploader.prototype.addData = function(data){
    this.data = $.extend({}, data, this.data);
};

TUploader.prototype.createImage = function(file){
    var preview   = $(this.template),
        image     = $('img', preview),
        file_name = $('div.file_name', preview);

    var reader = new FileReader();

    image.width  = 100;
    image.height = 100;

    reader.onload = function(e){
        if(file.type.split("/")[0] == 'image')
            image.attr('src', e.target.result);
        else
            image.attr('src', 'module/Assets/images/no_pic.jpg');

        file_name.html(file.name);
    };

    reader.readAsDataURL(file);

    this.message.hide();
    preview.appendTo(this.dropbox);

    $.data(file, preview);
};

TUploader.prototype.showMessage = function(msg){
    this.message.html(msg);
};

TUploader.prototype.setCallback = function(func){
    this.callback = func;
};

TUploader.prototype.make = function(){
    var uploader = this;

    this.dropbox.filedrop({
        // The name of the $_FILES entry:

        uploader: uploader,
        paramname: 'file',

        queuefiles: 5,
        maxfilesize: 200, // in mb
        url: uploader.route,

        data: uploader.data,

        dragOver: function(){
            this.uploader.dropbox.addClass('hover');
        },

        dragLeave: function() {
            this.uploader.dropbox.removeClass('hover');
        },

        drop: function(){
            this.uploader.dropbox.removeClass('hover');
        },

        uploadFinished: function(i, file, response){
            $.data(file).addClass('done');

            if($(this.uploader.elem_id + " .preview").length == $(this.uploader.elem_id + " .preview.done").length){
                $(this.uploader.elem_id + " .preview").remove();
                this.uploader.message.show();
            }
            if(response.error)
                toastr.error(response.error);
            else
                this.uploader.callback(response.data);
        },

        error: function(err, file) {
            switch(err) {
                case 'BrowserNotSupported':
                    this.uploader.showMessage('Your browser does not support HTML5 file uploads!');
                    break;
                case 'TooManyFiles':
                    alert('Too many files! Please select 5 at most!');
                    break;
                case 'FileTooLarge':
                    alert(file.name+' is too large! Please upload files up to 2mb.');
                    break;
                default:
                    break;
            }
        },

        uploadStarted:function(i, file, len){
            this.uploader.createImage(file);
        },

        progressUpdated: function(i, file, progress) {
            $.data(file).find('.progress').width(progress);
        }

    });
};