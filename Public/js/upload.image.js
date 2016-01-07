//基于UEditor的单独图片上传
(function($){
    uploadImage = {
        editor:null,
        init:function(editorId,uploadId){
            var _editor = this.getEditor(editorId);
            _editor.ready(function(){
                //_editor.setDisabled();
                _editor.hide();
                _editor.addListener('beforeinsertimage',function(t,args){
                    $('#' + uploadId).val(args[0].src);
                });
            });
        },
        getEditor:function(editorId){
            this.editor = UE.getEditor(editorId);
            return this.editor;
        },
        show:function(btnId){
            var _editor = this.editor;
            $('#' + btnId).click(function(){
                var img = _editor.getDialog('insertimage');
                img.render();
                img.open();
            });
        },
        render:function(editorId){
            var _editor = this.getEditor(editorId);
            _editor.render();
        }
    };
})(jQuery);