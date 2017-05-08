<section id="slider-block-{$ID}" data-cb="$BlockName" class="slider-block" data-module="slider" data-options="$SliderOptions">
    <div class="slider-block__frame">
        <div class="slider-block__slides">
            <% loop $Sliders %>
                $forTemplate
            <% end_loop %>
        </div>
    </div>
    <% if $Sliders.Count > 1 %>
        <span class="slider-block__navigation-button prev">
            <svg version="1.1" width="50" height="50" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 477.175 477.175" xml:space="preserve">
                <path fill="#FFFFFF" d="M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225
                    c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z"/>
            </svg>
        </span>
        <span class="slider-block__navigation-button right">
            <svg version="1.1" width="50" height="50" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 477.175 477.175" xml:space="preserve">
                <path fill="#FFFFFF" d="M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5
                    c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z
                    "/>
            </svg>
        </span>
    <% end_if %>
</section>