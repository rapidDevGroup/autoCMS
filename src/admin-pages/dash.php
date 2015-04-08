<?php

include_once('header.php');

$files = scanFiles('.html');

?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-red">
                <div class="panel-heading">
                    Unprocessed Files
                </div>
                <div class="panel-body">
                    <ul>
                        <?php foreach($files as $file) { ?>
                            <li><?=$file?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="panel-footer">
                    <?=count($files)?> Listed Files
                </div>
            </div>
            <!-- /.col-lg-4 -->
        </div>
        <div class="col-lg-4">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    Files Missing Content Management
                </div>
                <div class="panel-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>
                </div>
                <div class="panel-footer">
                    Panel Footer
                </div>
            </div>
            <!-- /.col-lg-4 -->
        </div>
        <div class="col-lg-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    Completed Files
                </div>
                <div class="panel-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>
                </div>
                <div class="panel-footer">
                    Panel Footer
                </div>
            </div>
            <!-- /.col-lg-4 -->
        </div>
    </div>
    <!-- /.row -->
<?php

include_once('footer.php');

?>