<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>jQuery File Upload Example</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <div class="container">
<input id="fileupload" type="file" name="file" data-url="upload" multiple style ="margin-top:5px"><br>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width: 0%;"  aria-valuemin="0" aria-valuemax="100" id="prog"></div>
</div>
<div id="spin" style="display:none">
<i class="fa fa-spinner fa-spin" style="font-size:11pt"></i><span style="font-size: 8pt;color: rgb(178,178,178)">     Processing-please wait, it takes a while...</span>
</div>
<div class="alert alert-success" style="display: none" id="success" style="text-align: center">
  <strong>Success!</strong> You have uploaded the file successfully.
</div>
<div class="alert alert-danger" style="display: none" id="fail" style="text-align: center">
  <strong>Failed!</strong> Please make sure you have uploaded an Android project.
</div>
    <a id="download_link"href="/download" class="btn btn-large pull-right" style="display: none"><i class="icon-download-alt"> </i> Download APK file </a>
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
         maxChunkSize: 10000000, 
        maxFileSize: 1000000 * 10000,
        always: function (e, data) {

          $('#spin').css('display','none');
          // console.log("success");
           if(data.result.status == "success"){
               $('#success').css('display','block');
               $('#download_link').css('display','inline');
           }
           else if(data.result.status == "Afalse")
            $('#fail').css('display','block');
           else if(data.result.status == "Zfalse"){
            $('#fail').html('<strong>Failed!</strong> The file you uploaded is not a Zip file.')
            $('#fail').css('display','block');
           }
            
           
  
        },
        progress:function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
          $('#prog').html(progress+"%");
          $('#prog').width(''+progress+'%');
          if(progress == 100){
              $("#prog").html('Completed');
              $('#spin').css('display','block');
          }

        }

    });
});
</script>
</body> 
</html>