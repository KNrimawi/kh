
<html>
   <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>upload</title>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="/js/xhr2.js"></script>

      
      <script type="text/javascript">
         $(function(){
               $('#upload').on("click",function(){
                     $.ajaxSetup({
                         headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         }
                     });
                     $("#file").upload("https://cec33601.ngrok.io/upload",function(data){
                      console.log(data);
                     },$("#prog"));
               });
         });
         
      </script>
   </head>
   <body>
      
    
     <input type="file" id="file" name="file">

    <input type="button" id="upload" value="upload">
    <br>
    <progress value="100" max="100" min="0" id="prog">

   </body>
 
</html>