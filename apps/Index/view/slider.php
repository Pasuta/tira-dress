<div id="home">
    <!-- Start cSlider -->
    <div id="da-slider" class="da-slider">
        <div class="triangle"></div>
        <!-- mask elemet use for masking background image -->
        <div class="mask"></div>
        <!-- All slides centred in container element -->
        <div class="container">
            <!-- Start first slide -->
            <div class="da-slide">
                <h2 class="fittext2"><?=$welcome->title?></h2>
                <h4><?=$welcome->subtitle?></h4>
                <p><?=$welcome->text?></p>
<!--                <a href="#" class="da-link button">Read more</a>-->
                <div class="da-img " >
                    <img src="/public/images/10.jpg" alt="image01" width="320" class="rounded">
                </div>
            </div>
            <!-- End first slide -->
            <!-- Start second slide -->
            <div class="da-slide">
                <h2 class="fittext2"><?=$uniq->title?></h2>
                <h4><?=$uniq->subtitle?></h4>
                <p><?=$uniq->text?></p>
<!--                <a href="#" class="da-link button">Read more</a>-->
                <div class="da-img">
                    <img src="/public/images/1.jpg" width="320" alt="image02" class="rounded">
                </div>
            </div>
            <!-- End second slide -->
            <!-- Start third slide -->
            <div class="da-slide">
                <h2 class="fittext2"><?=$ind->title?></h2>
                <h4><?=$ind->subtitle?></h4>
                <p><?=$ind->text?></p>
<!--                <a href="#" class="da-link button">Read more</a>-->
                <div class="da-img">
                    <img src="/public/images/8.jpg" width="320" alt="image03" class="rounded">
                </div>
            </div>
            <!-- Start third slide -->
            <!-- Start cSlide navigation arrows -->
            <div class="da-arrows">
                <span class="da-arrows-prev"></span>
                <span class="da-arrows-next"></span>
            </div>
            <!-- End cSlide navigation arrows -->
        </div>
    </div>
</div>