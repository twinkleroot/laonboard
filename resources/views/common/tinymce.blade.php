
@if(isMobile())
<script src="https://cloud.tinymce.com/dev/tinymce.min.js"></script>
@else
<script src="{{ ver_asset('tinymce/tinymce.min.js') }}"></script>
@endif

<script>
tinymce.init({
    selector: '.editorArea',
    language: 'ko_KR',
    branding: false,
    theme: "modern",
    mobile: {
        theme: 'beta-mobile',
        plugins: ['autosave', 'lists'],
        toolbar: [ 'undo', 'redo', 'fontsizeselect', 'forecolor', 'bold', 'italic', 'underline', 'bullist', 'numlist', 'removeformat' ]
    },
    skin: "lightgray",
    height: 400,
    min_height: 400,
    min_width: 400,
    plugins: [
        'advlist autolink lists link charmap print preview anchor textcolor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime table contextmenu paste code help'
    ],
    menubar: false,
    toolbar1: "insert | formatselect fontselect fontsizeselect | forecolor backcolor bold italic underline removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
    toolbar2: "undo redo | customImage table code | preview fullscreen print | help",
    fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
    font_formats : "굴림=굴림;굴림체=굴림체;궁서=궁서;궁서체=궁서체;돋움=돋움;돋움체=돋움체;바탕=바탕;바탕체=바탕체;나눔고딕=나눔고딕;맑은고딕='맑은 고딕';"
    +"Arial=Arial;Comic Sans MS='Comic Sans MS';Courier New='Courier New';Tahoma=Tahoma;Times New Roman='Times New Roman';Verdana=Verdana",
    relative_urls: false,
    setup: function(editor) {
        editor.on('init', function() {
            this.getDoc().body.style.fontSize = '10pt';
            this.getDoc().body.style.fontFamily = '굴림체';
        });
        @if(!isMobile())
        editor.addButton('customImage', {
            text: '사진',
            icon: 'image',
            onclick: function () {
                window.open('{{ route('image.form') }}','tinymcePop','width=640, height=480');
            }
        });
        @endif
    }
});
</script>
