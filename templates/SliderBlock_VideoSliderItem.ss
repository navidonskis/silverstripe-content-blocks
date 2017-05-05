<div data-item="video" class="slider-block__video-slider<% if $SliderImage %> slider-block__has-background<% end_if %>"<% if $SliderImage %> style="background-image: url('$SliderImage.SetWidth(1600).URL');"<% end_if %>>
    <div class="slider-block__content $HorizontalType $VerticalType $LowerStyle">
        <div class="slider-block__content--inner">
            <h2>$Title</h2>

            $Content
        </div>
    </div>
</div>