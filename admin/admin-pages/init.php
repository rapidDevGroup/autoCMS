<?php
$pagesData = new PagesData();
$pages = $pagesData->getData();
?>
<!DOCTYPE html>
<html lang="en" style="overflow: hidden;">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>autoCMS</title>
        <!-- Bootstrap Core CSS -->
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <!-- MetisMenu CSS -->
        <link href="//cdnjs.cloudflare.com/ajax/libs/metisMenu/2.0.0/metisMenu.min.css" rel="stylesheet" type="text/css">
        <!-- Custom Fonts -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Custom CSS -->
        <link href="/admin/css/autocms.css" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body style="background-color: #f8f8f8; overflow: hidden;">
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0; width: 100%;">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/admin/">autoCMS Version <?=VERSION?></a>
                </div>

                <ul class="nav navbar-top-links navbar-right" style="text-align: right;">
                    <li class="dropdown">
                        <a class="dropdown-toggle dirtyOK" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <?=$_SESSION["user"]?> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#" data-toggle="modal" data-target="#change-pass" class="dirtyOK"><i class="fa fa-lock fa-fw"></i> Change Password</a></li>
                            <li><a href="/admin/logs/?user-logs=<?=urlencode($_SESSION["user"])?>" target="iframe"><i class="fa fa-server fa-fw"></i> View My Logs</a></li>
                            <!--li><a href="#"><i class="fa fa-gear fa-fw"></i> User Settings</a></li-->
                            <li class="divider"></li>
                            <li><a href="/admin/logout/"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li><a href="/admin/dash/" target="iframe"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                            <?php if (file_exists('data/autocms-nav.json')) {?><li><a href="/admin/nav/" target="iframe"><i class="fa fa-bars fa-fw"></i> Navigation</a></li><?php } ?>
                            <?php if (count($pages) > 0) {?>
                                <li>
                                    <a href="#" class="open-close"><i class="fa fa-files-o fa-fw"></i> Pages<span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level">
                                        <?php foreach($pages as $pageName) { ?>
                                            <li>
                                                <a href="/admin/page/<?=$pageName?>/" target="iframe"><i class="fa fa-file-text-o"></i> <?=$pageName?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if (file_exists('data/autocms-footer.json')) {?><li><a href="/admin/footer/" target="iframe"><i class="fa fa-file-code-o fa-fw"></i> Site Footer</a></li><?php } ?>
                            <?php if (file_exists('data/autocms-blog.json')) {?><li><a href="/admin/blog/" target="iframe"><i class="fa fa-pencil-square-o fa-fw"></i> Blog or News Feed</a></li><?php } ?>
                            <?php if (file_exists('data/autocms-analytics.json')) {?><li><a href="/admin/analytics/" target="iframe"><i class="fa fa-bar-chart fa-fw"></i> Analytics</a></li><?php } ?>
                            <?php if (file_exists('data/autocms-media.json')) {?><li><a href="/admin/media/" target="iframe"><i class="fa fa-file-image-o fa-fw"></i> Media Library</a></li><?php } ?>
                            <?php if (file_exists('data/autocms-settings.json')) {?><li><a href="/admin/settings/" target="iframe"><i class="fa fa-cog fa-fw"></i> Settings</a></li><?php } ?>
                            <!---<li><a href="/admin/invoicing/" target="iframe"><i class="fa fa-file fa-fw"></i> Invoicing</a></li>--->
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="modal fade" id="change-pass" tabindex="-1" role="dialog" aria-labelledby="changePassLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close dirtyOK" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Change Password</h4>
                        </div>
                        <form id="change-pass-form" class="form-horizontal">
                            <div class="modal-body">
                                <div id="change-pass-error" class="alert alert-danger" role="alert" style="display:none;">An error occurred, please try again!</div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Current Password</label>
                                    <div class="col-sm-8">
                                        <input class="form-control dirtyOK" placeholder="Current Password" name="current" type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">New Password</label>
                                    <div class="col-sm-8">
                                        <input class="form-control dirtyOK" placeholder="New Password" name="password" type="password" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Confirm New Password</label>
                                    <div class="col-sm-8">
                                        <input class="form-control dirtyOK" placeholder="Confirm New Password" name="password2" type="password" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default dirtyOK" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary dirtyOK">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <iframe id="page-wrapper" name="iframe" src="/admin/dash/" border="0"></iframe>
        </div>
        <!-- jQuery -->
        <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/metisMenu/2.0.0/metisMenu.min.js"></script>
        <script src="/admin/js/autocms.js"></script>
    </body>
</html>
