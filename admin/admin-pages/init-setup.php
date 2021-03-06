<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>autoCMS</title>
    <!-- Bootstrap Core CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="/admin/css/autocms.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Create Your First Login</h3>
                        </div>
                        <div class="panel-body">
                            <?php if (isset($_GET['error'])) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <?=$_GET['error']?>
                                </div>
                            <? } ?>
                            <form action="/admin/login/" method="post" role="form" onsubmit="return validateCreateAuth()">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="User Name" name="user" type="text" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Confirm Password" name="password2" type="password" value="">
                                    </div>
                                    <!-- Change this to a button or input when using this as a form -->
                                    <button type="submit" class="btn btn-lg btn-success btn-block">Create</button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery -->
        <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body>
</html>
