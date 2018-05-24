{{--<form action="\execute" method="post">--}}
    {{--<input type="text" name="name" placeholder="enter your name..">--}}
    {{--<input type="text" name="age">--}}
    {{--{{csrf_field()}}--}}
    {{--<input type="submit" value="enter">--}}

{{--</form>--}}
<button id="btn">click me</button>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
$('#btn').click(function () {
    $data = {
       "arguments":{
           "a0":["khaled","tareq"],
           "a1":[1,2,3,4,9,10]
       },
       "fileName":"f_1_UYqvHQaIHCVWXiTiW6MHnmXLvBd52ai4Mk6qkQ5P.java"

    };
    $.ajax({
        url:"/execute",
        type:"POST",
        contentType: 'application/json',
        data:JSON.stringify($data),
        success:function (data) {
            console.log(data);
        }
    });


});
</script>