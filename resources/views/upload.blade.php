
<html>
   <head>
      <title>upload</title>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="/js/xhr2.js"></script>
      
      <script type="text/javascript">
         // $(function(){
         //       $('#upload').on("click",function(){
         //             $("#file").upload("https://ca2edd50.ngrok.io/upload",function(success){

         //             },$("#prog"));
         //       });
         // });
         $(function(){
            
            $('#btn').click(function(){
              var data={
                text:$('#txt').val(),
                token:$('input[name=_token]').val()
              }
              data = JSON.stringify(data);
              $.ajax({
                  
                  url:"https://cec33601.ngrok.io/upload",
                  type:"POST",
                  
                  dataType:'json',
                  success:function(data){
                  console.log(data);


              }
            });
           //  $.ajax(
           //  {
           //  url : 'https://jsonplaceholder.typicode.com/todos',
           //  dataType:'json',
           //  success:function(data){
           //    console.log(data);
           //  }
           // });
    
         
             });
            
         });
      </script>
   </head>
   <body>
      
    <input type="text" name="username" id="txt">
    <input type="button" name="btn" id="btn" value="send">
    

   </body>
  <!-- <input type="file" id="file" name="file">

    <input type="button" id="upload" value="upload">
    <br>
    <progress value="100" max="100" min="0" id="prog"> -->
</html>