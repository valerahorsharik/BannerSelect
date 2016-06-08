<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Test Work</title>

        <!-- Bootstrap -->
        <link href="/tmp/css/bootstrap.css" rel="stylesheet">
        <link href="/tmp/css/bootstrap-theme.css" rel="stylesheet">
        <link href="/tmp/css/style.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Test work</a>
                </div>
                <div class="collapse navbar-collapse ">
                    <ul class="nav navbar-nav pull-right">


             
                        <li><a href="#">Hello</a></li>

                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
        <div class="container">

            <?php include_once($currentPage); ?>

        </div>
        <div id="footer">
            <div class="container ">
                <p class="text-muted text-center  ">Code example by Valera Horsharik &#169; 2016</p>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="/mvc/tmp/js/bootstrap.js"></script>
    </body>
</html>