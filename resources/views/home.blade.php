<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Creative - Start Bootstrap Theme</title>

    <!-- Bootstrap core CSS -->
    <link href="js/boot_vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="js/boot_vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>

    <!-- Plugin CSS -->
    <link href="js/boot_vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/boot_css/creative.min.css" rel="stylesheet">
    <style>
        #download_link{
            text-decoration: none;
        }
    </style>

</head>

<body id="page-top">

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="#page-top">Graduation project</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#services">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#portfolio">Upload</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#contact">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="masthead text-center text-white d-flex">
    <div class="container my-auto">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="">
                    A framework for developing secure android apps
                </h1>

            </div>
            <div class="col-lg-8 mx-auto">
                <p class="text-faded mb-5">This framework provides many services that makes your application more immune to vulnerabilities. </p>
                <a class="btn btn-primary btn-xl js-scroll-trigger" href="#about">Documentation</a>
            </div>
        </div>
    </div>
</header>

<section class="bg-primary" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-heading text-white">We've got what you need!</h2>
                <hr class="light my-4">
                <p class="text-faded mb-4">This framework provides several features to use, please read the documentation before using it.</p>
                <a class="btn btn-light btn-xl js-scroll-trigger" href="/getdocumentation">Read documentation</a>
            </div>
        </div>
    </div>
</section>

<section id="services">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-heading">Provided Services</h2>
                <hr class="my-4">
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-box mt-5 mx-auto">
                    <i class="fa fa-4x fa-hashtag text-success mb-3 sr-icons"></i>
                    <h3 class="mb-3">Root Detection</h3>
                    <p class="text-muted mb-0">Our framework detects if your app is running on a rooted device.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-box mt-5 mx-auto">
                    <i class="fa fa-4x fa-bug text-success mb-3 sr-icons"></i>
                    <h3 class="mb-3">Debug Detection</h3>
                    <p class="text-muted mb-0">Our framework detects if there is debugging on your app.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-box mt-5 mx-auto">
                    <i class="fa fa-4x fa-server text-success mb-3 sr-icons"></i>
                    <h3 class="mb-3">Function Execution on the server</h3>
                    <p class="text-muted mb-0">Our framework allows you to execute a function on the server.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-box mt-5 mx-auto">
                    <i class="fa fa-4x fa-user-secret text-success mb-3 sr-icons"></i>
                    <h3 class="mb-3">Obfuscation</h3>
                    <p class="text-muted mb-0">Our framework provides a name and control flow obfuscations and add junk code to your app.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section  style="background-color: rgb(50,149,97)" id="portfolio">
    <div class="container">
        <div class="col-lg-12 text-center">
            <h2 class="section-heading" style="color: white">Upload a project</h2>
            <hr class="my-4" style="border-color: #bdff00">
        </div>
        <input id="fileupload" type="file" name="file" data-url="upload"  style ="margin-top:5px;border-radius: 5px"><br>
        <br>

        <div class="progress" style="height: 1.5rem">
            <div class="progress-bar" role="progressbar" style="width: 0%;background-color:#9ed700"  aria-valuemin="0" aria-valuemax="100" id="prog"></div>
        </div>
        <br>
        <div class="alert alert-success" style="display: none" id="success" style="text-align: center">
            <strong>Success!</strong> You have uploaded the file successfully.
        </div>
        <div class="alert alert-danger" style="display: none" id="fail" style="text-align: center">
            <strong>Failed!</strong> Please make sure you have uploaded an Android project.
        </div>
        <br>
        <div style="text-align: center">
            <button type="button" id="upload" class="btn btn-default btn-xl sr-button"> upload</button>
        </div>


        <span id="spin-t"style="font-size: 12pt;color:white;display: none"><i class="fa fa-refresh fa-spin"></i>  Processing-please wait, it takes a while...</span>


        <a id="download_link"href="#" class="pull-right" style="display: none;color:white"><i class="icon-download-alt" > </i>  Download APK file </a>
    </div>
   {{-- <div class="container-fluid p-0">
        <div class="row no-gutters popup-gallery">
            <div class="col-lg-4 col-sm-6">
                <a class="portfolio-box" href="css/img/portfolio/fullsize/1.jpg">
                    <img class="img-fluid" src="css/img/portfolio/thumbnails/1.jpg" alt="">
                    <div class="portfolio-box-caption">
                        <div class="portfolio-box-caption-content">
                            <div class="project-category text-faded">
                                Category
                            </div>
                            <div class="project-name">
                                Project Name
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6">
                <a class="portfolio-box" href="css/img/portfolio/fullsize/2.jpg">
                    <img class="img-fluid" src="css/img/portfolio/thumbnails/2.jpg" alt="">
                    <div class="portfolio-box-caption">
                        <div class="portfolio-box-caption-content">
                            <div class="project-category text-faded">
                                Category
                            </div>
                            <div class="project-name">
                                Project Name
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6">
                <a class="portfolio-box" href="css/img/portfolio/fullsize/3.jpg">
                    <img class="img-fluid" src="css/img/portfolio/thumbnails/3.jpg" alt="">
                    <div class="portfolio-box-caption">
                        <div class="portfolio-box-caption-content">
                            <div class="project-category text-faded">
                                Category
                            </div>
                            <div class="project-name">
                                Project Name
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6">
                <a class="portfolio-box" href="css/img/portfolio/fullsize/4.jpg">
                    <img class="img-fluid" src="css/img/portfolio/thumbnails/4.jpg" alt="">
                    <div class="portfolio-box-caption">
                        <div class="portfolio-box-caption-content">
                            <div class="project-category text-faded">
                                Category
                            </div>
                            <div class="project-name">
                                Project Name
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6">
                <a class="portfolio-box" href="css/img/portfolio/fullsize/5.jpg">
                    <img class="img-fluid" src="css/img/portfolio/thumbnails/5.jpg" alt="">
                    <div class="portfolio-box-caption">
                        <div class="portfolio-box-caption-content">
                            <div class="project-category text-faded">
                                Category
                            </div>
                            <div class="project-name">
                                Project Name
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-sm-6">
                <a class="portfolio-box" href="css/img/portfolio/fullsize/6.jpg">
                    <img class="img-fluid" src="css/img/portfolio/thumbnails/6.jpg" alt="">
                    <div class="portfolio-box-caption">
                        <div class="portfolio-box-caption-content">
                            <div class="project-category text-faded">
                                Category
                            </div>
                            <div class="project-name">
                                Project Name
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>--}}
</section>



<section id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-heading">Let's Get In Touch!</h2>
                <hr class="my-4">
                <p class="mb-5">Any questions? Give us a call or send us an email and we will get back to you as soon as possible!</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 ml-auto text-center">
                <i class="fa fa-phone fa-3x mb-3 sr-contact"></i>
                <p>123-456-6789</p>
            </div>
            <div class="col-lg-4 mr-auto text-center">
                <i class="fa fa-envelope-o fa-3x mb-3 sr-contact"></i>
                <p>
                    <a href="mailto:secureyourapp@gmail.com">secureyourapp@gmail.com</a>
                </p>
            </div>
            <div class="col-lg-4 mr-auto text-center">
                <i class="fa fa-users fa-3x mb-3 sr-contact"></i>
                <p>
                    Khaled &nbsp;&nbsp; Muath &nbsp;&nbsp;   Ahmad
                </p>



            </div>
        </div>
    </div>
</section>

<!-- Bootstrap core JavaScript -->
<script src="js/boot_vendor/jquery/jquery.min.js"></script>
<script src="js/boot_vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Plugin JavaScript -->
<script src="js/boot_vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/boot_vendor/scrollreveal/scrollreveal.min.js"></script>
<script src="js/boot_vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

<!-- Custom scripts for this template -->
<script src="js/boot_js/creative.min.js"></script>
<script src="js/vendor/jquery.ui.widget.js"></script>
<script src="js/jquery.iframe-transport.js"></script>
<script src="js/jquery.fileupload.js"></script>
<script>

    $(function () {

        localStorage['csrf_token'] = $('meta[name="csrf-token"]').attr('content');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#fileupload').fileupload({
            maxChunkSize: 10000000,
            replaceFileInput:false,
            maxFileSize: 1000000 * 10000,
            formData: {token: localStorage['csrf_token']},
            add: function (e, data) {

                $('#upload').click(function () {
                    localStorage['file_name']=data.originalFiles[0].name;
                    data.submit();

                    $('#spin-t').css('display','inline');
                });

            },
            always: function (e, data) {

                $('#spin-t').css('display','none');

                // console.log("success");
                if(data.result.status == "success"){
                    $('#success').css('display','block');
                    $('#download_link').css('display','inline');
                    document.getElementById("download_link").setAttribute("href","/download/"+localStorage['csrf_token']+"/"+localStorage['file_name']);


                }
                else if(data.result.status == "fail"){
                    $('#fail').html('<strong>Failed!</strong> Compilation error.');
                    $('#fail').css('display','block');
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
