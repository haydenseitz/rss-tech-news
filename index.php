<?php

// TODO: get local timezone by loading skel page, then doing ajax call with TZ arg to php script to load ticker content

//debugging
error_reporting(E_ALL);
ini_set('display_errors', true);

// requirements
require_once('functions.php');
require_once('simplepie/autoloader.php');

// variables
$max_item_len = 500;
$feed_max = 50;

// RSS feeds
$rss = [
    'Engadget' => "https://www.engadget.com/rss-full.xml" 
    , 'TechCrunch' => "https://feeds.feedburner.com/Techcrunch" 
    , 'WIRED' => "https://www.wired.com/feed" 
    , 'The Verge' => "https://www.theverge.com/rss/full.xml" 
    , 'Ars Technica' => "http://feeds.arstechnica.com/arstechnica/index" 
];

// set light or dark mode
$darkmode = 0;
// see if darkmode cookie exists
if ( isset($_COOKIE['darkmode']) && $_COOKIE['darkmode']=='yes' ) {
    $darkmode = 1;
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="refresh" content="300"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <title>Tech News</title>
    <style>
        :root {
            --bg-dark: #343a40;
        }
        body { transition: all 0.5s; }
        hr { transition: all 0.5s; }
        a.badge { transition: all 0.5s; }
        li img {
            max-height: 150px;
            box-shadow: 0 0 0.5em rgba(0,0,0,0.5);
        }
        .button-controls {
            position: fixed;
            top: 0;
            right: 0;
        }
        div#progressbar {
            position: fixed;
            top: 0;
            left 0;
            width: 2em;
            height: 2em;
        }
        div#information {
            position: fixed;
            bottom: 0;
            right: 0;
            z-index: 1;
        }
        div#fade-bottom {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 5em;
            background: linear-gradient(rgba(0,0,0,0) 0%, <?php echo (($darkmode)? 'var(--bg-dark)' : 'white'); ?> 100%);
        } 
    </style>
</head>
<body class="<?php echo (($darkmode)? 'text-light bg-dark' : 'text-body bg-white'); ?>">
    <div class="container-fluid vh-100 overflow-hidden">
        <div class="row">
            <div class="col-1"></div>
            <div class="col">
                <div class="text-center"><p class="display-4"> ~ Tech News ~ </p></div>
                <hr class="mb-0 <?php echo (($darkmode)?'bg-secondary':'bg-light'); ?>">
            </div>
            <div class="col-1"></div>
        </div>
        <div class="row">
            <div class="col-1"></div>
            <div id="rss-ticker" class="col">
<?php

    // simplepie
    $feed = new SimplePie();
    $feed->set_feed_url($rss);
    $feed->set_cache_duration(60);  // only cache for 1 minute
    $feed->init();
    $feed->handle_content_type();  // confirm utf8
    
    echo '<ul class="list-unstyled">';
    $feed_counter=0;
    foreach ( $feed->get_items() as $item) {
        if ($feed_counter < $feed_max) {
            // get first image if exists
            preg_match('/<img[^>]+>/',$item->get_description(),$img);
            //echo '<li class="mb-2 border-bottom '.(($darkmode)?'border-secondary':'').'">';
            echo '<li class="m-3">';
            echo '<div class="row">';
            echo '<div class="col-md-2 text-center images">';
            if (!empty($img[0])){
                echo $img[0];
            } else {
                echo '<i class="fa fa-newspaper-o fa-4x"></i>';
            }
            echo '</div>';
            echo '<div class="col">';
            echo '<a class="title h4 font-weight-bold text-reset" href="'.$item->get_permalink().'" target="_blank">'.$item->get_title().'</a>';
            echo '&nbsp;<a class="badge '.(($darkmode)?'badge-secondary':'badge-light').'" href="' .$item->get_feed()->get_permalink() . '" target="_blank">' . $item->get_feed()->get_title() . '</a>';
            //echo '<footer class="blockquote-footer">' . $item->get_date('l, M jS Y g:i a') . '</footer>';
            echo '<footer class="blockquote-footer">' . $item->get_local_date('%A, %b %e %Y %l:%M %P') . '</footer>';
            echo '<p class="content text">';
            $content = strip_tags($item->get_description(),'<a><p><b><strong><small>');  // strip html tags except for allowed
            if ( html_len($content) > $max_item_len ) {
                echo html_cut($content, $max_item_len, '<span class="text-muted">...</span>');
            } else {
                echo $content;
            }
            echo '</p>';
            echo '</div>'; // col
            echo '</div>'; // row
            echo '</li>';
            $feed_counter++;
        } else {
            break;
        }
    }
    echo '</ul>';

/*
        echo '<ul>';
        foreach ( range(1,10)  as $i ) {
                    echo '<li>';
                    echo '<div class="row">';
                    echo '<div class="col-lg-2 text-center">';
                    echo '<img class="img-fluid rounded align-middle" src="https://picsum.photos/'.rand(100,1000).'/'.rand(100,1000).'?random='.$i.'"/>';
                    echo '</div>';
                    echo '<div class="col">';
                    echo '<a class="title" href="#">Title</a>';
                    echo '<p class="content">';
                    foreach( range(1,rand(2,5)) as $i ) {
                        echo 'content of the article here is a much longer content descripton that should take up most of the page. ';
                    }
                    echo '</p>';
                    echo '<p class="source">' . date("F d, Y h:i:s") . ' Source Name </p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</li>';
        }
        echo '</ul>';
 */
?>
            </div>
            <div class="col-1"></div>
        </div>
    </div>

    <div class="button-controls mr-3">
        <p><div class="btn-group-vertical scroll">
            <button type="button" class="btn <?php echo (($darkmode)? 'btn-outline-light' : 'btn-outline-dark'); ?> up-arrow"><i class="fa fa-arrow-up fa-fw"></i></button>
            <button type="button" class="btn <?php echo (($darkmode)? 'btn-outline-light' : 'btn-outline-dark'); ?> play-pause"><i class="fa fa-pause fa-fw"></i></button>
            <button type="button" class="btn <?php echo (($darkmode)? 'btn-outline-light' : 'btn-outline-dark'); ?> down-arrow"><i class="fa fa-arrow-down fa-fw"></i></button>
        </div></p>
        <p><button type="button" data-darkmode="<?php echo (($darkmode)?'yes':'no'); ?>" class="btn <?php echo (($darkmode)? 'btn-light' : 'btn-dark'); ?> dark-light"><i class="fa <?php echo (($darkmode)?'fa-lightbulb-o':'fa-moon-o'); ?> fa-fw"></i></button></p>
    </div>

    <div id="progressbar" class="m-3"></div>

    <div id="information"><button class="btn btn-link mt-0 ml-3 mr-3 mb-3"><i class="fa fa-info-circle text-muted"></i></button></div>

    <div id="fade-bottom"></div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
<?php //<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script> ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>         
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <!-- other js -->
    <script src="jquery-easy-ticker/jquery.easy-ticker.custom.js"></script>
    <script src="FeedEk/js/FeedEk.min.js"></script>
    <script src="js/js.cookie.js"></script>
    <script src="progressbar.js/dist/progressbar.min.js"></script>

    <script>

    // variables
    var progress_counter = 0;
    var progressCircle = null;
    var progressTimer = null;

    // functions
    function updateMediaQuery() {
        if (window.matchMedia('(max-width: 992px)').matches) {  // mobile
            $('#fade-bottom').hide();
            $('.scroll').hide();
        } else {  // desktop
            $('#fade-bottom').show();
            $('.scroll').show();
        }
    }
    function startProgressTimer(progressObj){
        //console.debug(progress_counter);
        progress_counter += 0.11;
        progressObj.animate(progress_counter);
    }

    // immediately add some styling
    $('.images img').addClass('img-fluid rounded m-3');

    // hide some stuff on mobile
    updateMediaQuery();
    $(window).bind('resize', updateMediaQuery);


    $(window).ready(function(){

        $('#rss-ticker').easyTicker({
            visible: 10
            , mousePause: 0
            , speed: 1000
            , interval: 10000
            , controls: {
                up: '.down-arrow'
               , down: '.up-arrow'
               , toggle: '.play-pause'
               , playText: '<i class="fa fa-play fa-fw"></i>'
               , stopText: '<i class="fa fa-pause fa-fw"></i>'
            }
        });

        // mouse scroll triggers up and down ticker scroll
        $(window).on('DOMMouseScroll',function(e){
            if (e.originalEvent.detail < 0) {  // scroll up
                console.debug('scroll up');
                $('.up-arrow').click();
            } else {  // scroll down
                console.debug('scroll down');
                $('.down-arrow').click();
            }
        });

        // progress bar/circle
        progressCircle = new ProgressBar.Circle(progressbar,{
            trailWidth: 1
            , strokeWidth: 10
            , trailColor: '#888'
            , color: '#888'
            , svgStyle: null
            , duration: 1000
            , easing: 'linear'
        });

        //console.debug(progressCircle);
        
        // start progress timer
        progressCircle.animate(progress_counter);
        progressTimer = setInterval(function(){ startProgressTimer(progressCircle); },1000);

        $('#rss-ticker').bind('move', function(){
            progress_counter = 0;
            clearInterval(progressTimer);
            progressCircle.animate(progress_counter);
            progressTimer = setInterval(function(){ startProgressTimer(progressCircle); },1000);
            //progressCircle.animate(progress_counter);
        });

        // stop and reset timer when scrolling is paused
        $('.scroll button').click(function(){
            if ( $('.play-pause').hasClass('et-run') ) {
                //console.debug('was stopped');
                // start progress timer
                progress_counter = 0;
                progressTimer = setInterval(function(){ startProgressTimer(progressCircle); },1000);
            } else {
                //console.debug('was running');
                // stop progress timer
                clearInterval(progressTimer);
                progressCircle.animate(0.0);
            }
        });

        /// old code
        /*
        // load rss
        var feed_url = document.location.protocol + '//www.engadget.com/rss-full.xml';

        var feedEk = $('#rss-ticker').FeedEk({
            FeedUrl: feed_url
            , DescCharacterLimit: 1000
            , MaxCount: 10
        });

        $('#rss-ticker').on('DOMSubtreeModified', function() {
            //console.debug('content changed');
            //console.debug($(this).html());
            if ( $(this).html() != '' ) {
                // enable ticker
                $(this).easyTicker({
                    visible: 10
                    , mousePause: 0
                    , speed: 1000
                    , interval: 5000
                    , controls: {
                        up: '.down-arrow'
                       , down: '.up-arrow'
                       , toggle: '.play-pause'
                       , playText: '<i class="fa fa-play fa-fw"></i>'
                       , stopText: '<i class="fa fa-pause fa-fw"></i>'
                    }
                });
                // show ticker
                $(this).show();
            }
        });
         */

        /*
        // start ticker
        $('#rss-ticker').easyTicker({
            visible: 10
            , mousePause: 0
            , speed: 1000
            , interval: 5000
            , controls: {
                up: '.down-arrow'
               , down: '.up-arrow'
               , toggle: '.play-pause'
               , playText: '<i class="fa fa-play fa-fw"></i>'
               , stopText: '<i class="fa fa-pause fa-fw"></i>'
            }
        });
         */

        // dark/light mode
        $('.dark-light').click(function(){
            if ( $(this).data('darkmode')=='yes' ) {  // if currently dark
                // set light features
                $('body').removeClass('bg-dark text-light').addClass('bg-white text-body');
                if (!window.matchMedia('(max-width: 992px)').matches) {  // desktop
                    $('#fade-bottom').hide(0).css('background','linear-gradient(rgba(0,0,0,0) 0%, white 100%)').fadeIn(500);
                }
                $('hr').removeClass('bg-secondary').addClass('bg-light');
                //$('button').not('.dark-light').removeClass('btn-outline-light').addClass('btn-outline-dark');
                $('.scroll button').removeClass('btn-outline-light').addClass('btn-outline-dark');
                $(this).removeClass('btn-light').addClass('btn-dark').find('i').removeClass('fa-lightbulb-o').addClass('fa-moon-o');
                $('li').removeClass('border-secondary');
                $('.badge').removeClass('badge-secondary').addClass('badge-light');
                // update cookie and data tag
                Cookies.set('darkmode','no', {expires: 365, path: '/news/'});
                $(this).data('darkmode','no');
            } else {  // if currently light
                // set dark features
                $('body').removeClass('bg-white text-body').addClass('bg-dark text-light');
                if (!window.matchMedia('(max-width: 992px)').matches) {  // desktop
                    $('#fade-bottom').hide(0).css('background','linear-gradient(rgba(0,0,0,0) 0%, var(--dark) 100%)').fadeIn(500);
                }
                $('hr').removeClass('bg-light').addClass('bg-secondary');
                //$('button').not('.dark-light').removeClass('btn-outline-dark').addClass('btn-outline-light');
                $('.scroll button').removeClass('btn-outline-dark').addClass('btn-outline-light');
                $(this).removeClass('btn-dark').addClass('btn-light').find('i').removeClass('fa-moon-o').addClass('fa-lightbulb-o');
                $('li').addClass('border-secondary');
                $('.badge').removeClass('badge-light').addClass('badge-secondary');
                // update cookie and data tag
                Cookies.set('darkmode','yes', {expires: 365, path: '/news/'});
                $(this).data('darkmode','yes');
            };
        });

        // information tooltip
        var title = 'v0.1 2019-12-20 <br>';
        //title += 'Hayden Seitz <br>';
        title += 'sources: <br>';
        title += '<ul><?php
        foreach ( $rss as $source => $url ) {
            echo '<li><a href="'.$url.'" target="_blank">'.$source.'</a></li>';
        }
        ?></ul>';

        $('#information').popover({
            placement: 'top'
            , content: title
            , html: true
            , trigger: 'click'
        });

    });
    </script>
</body>
</html>
