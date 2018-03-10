<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>jQuery File Upload Example</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<input id="fileupload" type="file" name="file" data-url="upload" multiple><br>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width: 0%;"  aria-valuemin="0" aria-valuemax="100" id="prog"></div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="js/vendor/jquery.ui.widget.js"></script>
<script src="js/jquery.iframe-transport.js"></script>
<script src="js/jquery.fileupload.js"></script>
<script>
$(function () {
  var i = 1;
  var j = 1;
    $.ajaxSetup({
         headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });
    $('#fileupload').fileupload({
        dataType: 'json',
         maxChunkSize: 10000000, 
        maxFileSize: 1000000 * 10000,
        done: function (e, data) {
          console.log('done');
  
        },
        progress:function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
          $('#prog').html(progress+"%");
          $('#prog').width(''+progress+'%');
        }

    });
});
</script>
</body> 
</html>