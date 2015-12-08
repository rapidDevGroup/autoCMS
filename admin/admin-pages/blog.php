<?php
include_once('header.php');
$blogData = new BlogData();
$blogList = $blogData->getBlogList();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Blog</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-4"><h4>Title</h4></div>
        <div class="col-md-2 col-sm-2 col-xs-4" style="text-align: center"><h4>Creator</h4></div>
        <div class="col-md-2 hidden-sm hidden-xs" style="text-align: center"><h4>Created</h4></div>
        <div class="col-md-2 col-sm-3 col-xs-4" style="text-align: center"><h4>Published</h4></div>
        <div class="col-lg-2 col-md-3 col-sm-4 hidden-xs" style="text-align: center"><h4>Options</h4></div>
    </div>
    <hr>
    <?php foreach($blogList as $key => $blog) { ?>
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-4"><?=$blog['title']?></div>
            <div class="col-md-2 col-sm-2 col-xs-4" style="text-align: center"><?=$blog['creator']?></div>
            <div class="col-md-2 hidden-sm hidden-xs" style="text-align: center"><?=date("M jS, Y", $blog['created'])?></div>
            <div class="col-md-2 col-sm-3 col-xs-4" style="text-align: center"><?php if(isset($blog['published'])) { print date("M jS, Y", $blog['published']); } else { print "Not Published"; } ?></div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                <div class="visible-xs" style="padding-top: 10px;"></div>
                <div class="btn-group btn-group-justified">
                    <a href="/admin/blog/<?=$key?>/trash/" class="btn btn-danger" title="Delete Post"><i class="fa fa-trash"></i></a>
                    <?php if(isset($blog['published'])) { ?>
                        <a href="/admin/blog/<?=$key?>/unpublish/" class="btn btn-warning" title="Unpublish Post"><i class="fa fa-pause"></i></a>
                    <?php } else { ?>
                        <a href="/admin/blog/<?=$key?>/publish/" class="btn btn-warning" title="Publish Post"><i class="fa fa-play"></i></a>
                    <?php } ?>
                    <!--a href="/<?=$blog['external']?>/" class="btn btn-success" title="View Post" target="_blank"><i class="fa fa-eye"></i></a-->
                    <a href="/admin/blog/<?=$key?>/" class="btn btn-primary" title="Edit Post"><i class="fa fa-pencil-square-o"></i></a>
                </div>
            </div>
        </div>
        <hr>
    <?php } ?>
    <div class="row">
        <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
            <a href="/admin/blog/new/" class="btn btn-primary btn-block pull-right dirtyOK">Create New Post</a>
        </div>
    </div>
    <br><br>
</div>
<?php

include_once('footer.php');
?>