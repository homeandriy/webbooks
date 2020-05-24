<?php
/**
 * Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
// подключаем header.php

?>



    <!-- Start Page Heading -->
    <div class="section bg-brown-lighten ">
        <div class="container-fluid">
            <div class=" col-md-12">
                <h3><?php the_title();?></h3>                
            </div>
        </div>
    </div>
    <!-- ./ Page Header -->


    <!-- Start Main Section -->


    <div class="bg-brown-lighten bdr-b container-fluid">

        <!-- Start Nav Tabs -->

        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab">Описания</a></li>
            <li role="presentation"><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab">Комментарии</a></li>
            <!-- <li role="presentation"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">Contact Publisher</a></li> -->
            <li role="presentation" class="dropdown pull-right">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
            Report <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#">Report as Spam</a></li>
                <li><a href="#">Report as Expired</a></li>
                <li><a href="#">Report as Duplicate ad</a></li>
                <li><a href="#">Report as Offensive</a></li>
            </ul>
            </li>
            <li class="pull-right" role="presentation"><a href="#"><i class="mdi-action-visibility "></i> Watch</a></li>
            <li class="pull-right" role="presentation"><a href="#"><i class="mdi-social-share"></i> Share</a></li>
            <li class="pull-right" role="presentation"><a href="#"><i class="mdi-maps-local-print-shop"></i> Print Listing</a></li>
        </ul>

    <!-- ./  Nav Tabs -->

    </div>

   <!-- Start Tab Panels -->

    <div class="main-section">

        <div class="container-fluid">

            <div class="row">

                <div class="col-md-6">

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="description">
                            <div class="panel panel-default bdr-t-none">
                                <!-- Default panel contents -->
                                <div class="panel-body">
                                    <p>
                                        <?php the_content(); ?>
                                    </p>
                                    <p>
                                        
                                    </p>
                                </div>
                                <div class="panel-heading bdr-t">
                                     Sponsored Ad <button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="panel-body">
                                    <img alt="Sponsored Ad" src="http://dummyimage.com/600x100/38A6A6/fff.png">
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="comments">
                            <div class="panel panel-default bdr-t-none">
                                <!-- Default panel contents -->
                                <div class="panel-body">
                                    <div class="pull-left">
                                        <form>
                                            <input type="text" class="form-control input-sm" placeholder="Search comments...">
                                        </form>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-primary btn-sm hidden-lg hidden-md" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="mdi-content-add-circle-outline"></i></a>
                                        <a data-toggle="collapse" href="" aria-expanded="false" aria-controls="collapseExample" class="btn btn-primary btn-sm hidden-sm hidden-xs">+ Post a Comment</a>
                                    </div>
                                </div>
                                <div class="collapse" id="collapseExample">
                                    <div class="panel-body">
                                        <form>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Comment</label>
                                                <textarea class="form-control" rows="4" placeholder="Enter comment"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                        </form>
                                    </div>
                                </div>
                                <!-- List group -->
                                <?php comments_template( ); ?>
                            </div>
                        </div>
                      <!--   <div role="tabpanel" class="tab-pane" id="contact">
                            <div class="panel panel-default bdr-t-none">
                                
                                <div class="list-group">
                                    <a id="example-one" data-text-swap="519-123-XXXX (Click to show)" data-text-original="519-123-4567" class="list-group-item "><i class="mdi-hardware-phone-iphone"></i> 519-123-XXXX (Click to show)</a>
                                    <a href="#" class="list-group-item"><i class="mdi-action-view-module"></i>View publisher's other listings</a>
                                </div>
                                <div class="panel-heading">
                                    Email Publisher
                                </div>
                                <div class="alert alert-success alert-block alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <strong>Message sent succesfully!</strong> You should get a reply soon.
                                </div>
                                <div class="panel-body">
                                    <form>
                                        <div class="form-group">
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" id="exampleInputName1" placeholder="Enter name">
                                        </div>
                                        <div class="form-group">
                                            <textarea rows="4" class="form-control" placeholder="Message"></textarea>
                                        </div>
                                        <a href="#" class="btn btn-default pushme">Send</a>
                                    </form>
                                </div>
                                <div class="panel-heading bdr-t">
                                     Sponsored Ad <button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="panel-body">
                                    <img alt="Sponsored Ad" src="http://dummyimage.com/600x100/38A6A6/fff.png">
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>   <!-- ./ col-md-6 -->

               <!-- ./ Tab Panels -->

               <!-- Start Photo Gallery -->

                <div class="col-md-6">

                    <div class="panel panel-default mrg-t">

                        <!-- Default panel contents -->

                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-example-generic" data-slide-to="0" class=""></li>
                                <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
                                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="3" class="active"></li>
                            </ol>

                            <div class="carousel-inner" role="listbox">
                              
                                <div class="item active">
                                   <?php the_post_thumbnail( 'big-thumb' ); ?>
                                </div>
                                
                            </div>

                            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                            </a>

                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-6 col-md-2">
                                    <a href="img/thumbs/thumb1.jpg" class="boxer" title="Caption One" data-gallery="gallery">
                                    <img alt="Thumbnail" src="img/thumbs/thumb1.jpg" style=" width: 100%; display: block;">
                                    </a>
                                </div>
                                <div class="col-xs-6 col-md-2">
                                    <a href="img/thumbs/thumb2.jpg" class="boxer" title="Caption Two" data-gallery="gallery">
                                    <img alt="Thumbnail" src="img/thumbs/thumb2.jpg"  style=" width: 100%; display: block;">
                                    </a>
                                </div>
                                <div class="col-xs-6 col-md-2">
                                    <a href="img/thumbs/thumb3.jpg" class="boxer" title="Caption Three" data-gallery="gallery">
                                    <img alt="Thumbnail" src="img/thumbs/thumb3.jpg"  style=" width: 100%; display: block;">
                                    </a>
                                </div>
                                <div class="col-xs-6 col-md-2">
                                    <a href="img/thumbs/thumb4.jpg" class="boxer" title="Caption Four" data-gallery="gallery">
                                    <img alt="Thumbnail" src="img/thumbs/thumb4.jpg"  style=" width: 100%; display: block;">
                                    </a>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
