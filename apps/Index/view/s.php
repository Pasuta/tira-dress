<?extract($this->context);?>
<div>
    <div class="sliderArr" data-move="left">&larr;</div>

    <div class="sliderFrame">
        <div class="sliderContent animated">
            <?
            foreach($last6 as $o) {
                $src = $o->mp->image->uri;
                echo "<img src='{$src}' style='width: 200px'>";
            }?>
            <br style="clear: both">
        </div>
    </div>

    <div class="sliderArr" data-move="right">&rarr;</div>
    <br class="clear">
</div>

<script>
    sliderHorizontal();
</script>