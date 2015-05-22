<?php
include_once('header.php');
$blogList = getBlogList();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Blog</h2>
        </div>
    </div>
    <?php foreach($blogList as $key => $blog) { ?>
        <?php print_r($blog); ?>
        <hr>
    <?php } ?>
    <div class="row">
        <div class="form-group">
            <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                <a href="/admin/blog/new/" class="btn btn-primary btn-block pull-right dirtyOK">Create Post</a>
            </div>
        </div>
    </div>
    <br><br>
</div>
<?php
include_once('footer.php');
?>