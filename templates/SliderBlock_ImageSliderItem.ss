<div data-item="image" class="slider-block__image-slider<% if $SliderImage %> slider-block__has-background<% end_if %>"<% if $SliderImage %> style="background-image: url('$SliderImage.SetWidth(1600).URL');"<% end_if %>>
    <div class="slider-block__content $HorizontalType $VerticalType $LowerStyle">
        <div class="slider-block__content--inner">
            <% if $Heading %><h2>$Heading</h2><% end_if %>

            $Content
        </div>
    </div>
</div>