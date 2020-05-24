<?php
/**
 * Шаблон отдельной записи (single.php)
 * Template Name Posts: single-post(NOT BOOK)
 * @package WordPress
 * @subpackage your-clean-template
 */
// подключаем header.php
$getcat = get_the_category();
$myID = $getcat[0]->cat_ID;

get_header(); 
get_sidebar();?>
    <aside class="right-section">
    <!-- Main content - Includes Featured Listings + Latest Listings -->
        <section class="content">



        <?php while (have_posts()): the_post(); ?>
            <!-- Start Page Heading -->
            <div class="section bg-brown-lighten ">
                <div class="container-fluid">
                    <div class=" col-md-8">
                        <h1 class="post-title entry-title " <?php post_class(); ?> id="" ><?php the_title(); ?> </h1>              
                    </div>
                    <div class=" col-md-4">
                         <?php 
                            $i = mt_rand(1,2);
                            get_template_part('reclama/reclama-'.$i);
                        ?>
                    </div>
                </div>
            </div>
            <!-- ./ Page Header -->
        <!-- Start Main Section -->
        <div class="bg-brown-lighten bdr-b container-fluid">
            <!-- Start Nav Tabs -->
            <ul class="nav nav-tabs" role="tablist" id="myTab">
                <li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab">Описания</a></li>
                <li role="presentation"><a href="#comments1" aria-controls="comments1" role="tab" data-toggle="tab">Описание/Скачать</a></li>            
            </ul>
        <!-- ./  Nav Tabs -->
        </div>
       <!-- Start Tab Panels -->
        <div class="main-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="description">
                            <div class="panel panel-default bdr-t-none">
                                <!-- Default panel contents -->
                                <div class="panel-body">
                                    <p>                                   
                                        <?php the_content(); ?>
                                        <h3>Понравилась статья или книга? Поделись с друзями:</h3>
                                         <?php echo function_exists('evc_buttons_code') ?  evc_buttons_code(): ''; ?>
                                    </p>                                                          
                                </div>                                
                                <div class="panel-heading bdr-t">
                                     Комментарии <button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="panel-body">
                                  <?php comments_template( ); ?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="comments1">
                            <div class="panel panel-default bdr-t-none">
                                <!-- Default panel contents -->
                                <div class="panel-body">
                                    <!-- Table -->
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Жанр:</td>
                                                <td><?php 
                                                     if ( function_exists( 'yoast_breadcrumb' ) ) {
                                                        yoast_breadcrumb();
                                                     }?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Статус:</td>
                                                <td><?php 
                                                    $complexity = trim(get_field( "complexity"));
                                                   echo get_complexity($complexity);  
                                                   ?>                                         
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Ссылка на оригинал статьи (Если указана или эта статья не авторская)</td>
                                                <td>
                                                    <?php 
                                                        $linkToDownload = get_post_meta($post->ID, 'download', true);
                                                        $linkToDownload =  parse_url($linkToDownload, PHP_URL_PATH);
                                                        $linkToDownloadarray= explode("/",$linkToDownload);
                                                        count($linkToDownloadarray);

                                                       $linkToDownloadarray = $linkToDownloadarray[count($linkToDownloadarray)-1];
                                                     ?>
                                                    <a href="<?php echo get_bloginfo('url') ?>/download/?key=<?php echo $linkToDownloadarray; ?>&count=<?php echo $post->ID;?>&cat=<?php echo $myID  ?>" class="btn btn-primary btn-sm" target="_blank">Скачать</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>На сайт предоставил</td>
                                                <td>
                                                    <!-- Для решения ошибки с entry-title -->
                                                    
                                                    <!-- Для решения ошибки с updated -->
                                                    <time datetime="<?php the_time('o-m-d') ?>" class="post-date updated" pubdate><?php the_time(apply_filters('themify_loop_date', 'M j, Y H:i')) ?></time>
                                                    <!-- Для решения ошибки с author -->
                                                    <span class="vcard author">
                                                      <span class="fn"><?php the_author_posts_link(); ?></span>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Поделись статьей</td>
                                                <td>
                                                    <?php echo function_exists('evc_buttons_code') ?  evc_buttons_code(): '';; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <h2>
                                        <i class="fa fa-exclamation-circle"></i> Статьи опубликованые на сайте <a href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name'); ?></a> указаны с ссылками на источник. Администрация сайта не несет ответственность за их использование Вами
                                    </h2>
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
                            </div>
                        </div>                 
                    </div>
                </div>   <!-- ./ col-md-6 -->

               <!-- ./ Tab Panels -->

               <!-- Start Photo Gallery -->

                <div class="col-md-3">

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
                                   <?php the_post_thumbnail( 'big-thumb-main' ); ?>
                                </div>
                                
                            </div>

                            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="fa fa-angle-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="fa fa-angle-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                    <div class="info-block">
                        <h3>Смотри также:</h3>  
                        <?php 

                            $argsToQuery = array(
                                'posts_per_page' => 5,
                                'post_status' => 'publish',
                                'orderby' => 'rand',
                                'category__in' => $myID,
                            );


                            $queryInSingle = new WP_Query( $argsToQuery );

                            // Цикл
                            if ( $queryInSingle->have_posts() ) {
                                while ( $queryInSingle->have_posts() ) {
                                    $queryInSingle->the_post();
                                     
                                    ?>
                                    <div class="list-group">
                                      <a href="<?php echo get_the_permalink(); ?>" class="list-group-item">
                                        <h4 class="list-group-item-heading"><i class="fa fa-arrow-circle-right"></i><?php echo get_the_title()  ?></h4>
                                        <p class="list-group-item-text"><?php 
                                            $ShortNews = get_the_content();
                                            $ShortNews = strip_tags($ShortNews);
                                            $ShortNews = substr($ShortNews, 0, 100);
                                            $ShortNews = rtrim($ShortNews, "!,.-");
                                            $ShortNews = substr($ShortNews, 0, strrpos($ShortNews, ' '));                                            
                                            echo $ShortNews."… ";






                                         ?></p>
                                      </a>
                                    </div>
                                    <?php                                   
                                }
                            } else {
                                // Постов не найдено
                            }


                         ?>
                    </div>


                    
                </div>
            </div>
        </div>
    </div>
    <?php endwhile;?>
       </section>
    <!-- right col -->
   </aside>
<?php
get_footer();
?>