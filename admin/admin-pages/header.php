<?php
$pages = getPageList();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>autoCMS</title>
        <!-- Bootstrap Core CSS -->
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <!-- MetisMenu CSS -->
        <link href="//cdnjs.cloudflare.com/ajax/libs/metisMenu/2.0.0/metisMenu.min.css" rel="stylesheet" type="text/css">
        <!-- Custom CSS -->
        <link href="/admin/css/autocms.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Other -->
        <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/admin/dash/">autoCMS Version <?=VERSION?></a>
                </div>

                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <?=$_SESSION["user"]?> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <!--li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                            <li><a href="#"><i class="fa fa-gear fa-fw"></i> User Settings</a></li>
                            <li class="divider"></li-->
                            <li><a href="/admin/logout/"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li><a href="/admin/dash/"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                            <?php if (hasNav()) {?><li><a href="/admin/nav/"><i class="fa fa-bars fa-fw"></i> Navigation</a></li><?php } ?>
                            <?php if (count($pages) > 0) {?>
                                <li>
                                    <a href="#"><i class="fa fa-files-o fa-fw"></i> Pages<span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level">
                                        <?php foreach($pages as $pageName) { ?>
                                            <li>
                                                <a href="/admin/page/<?=$pageName?>/"><i class="fa fa-file-text-o"></i> <?=$pageName?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if (false) {?><li><a href="/admin/blog/"><i class="fa fa-bars fa-fw"></i> Blog / News / RSS Feed</a></li><?php } ?>
                            <?php if (false) {?><li><a href="/admin/languages/"><i class="fa fa-bars fa-fw"></i> Multi-Language</a></li><?php } ?>
                            <?php if (false) {?><li><a href="/admin/settings/"><i class="fa fa-bars fa-fw"></i> Settings</a></li><?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>

            <div id="page-wrapper">