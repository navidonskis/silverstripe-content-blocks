<section id="map-block-{$ID}" data-cb="map-block">
    <div
            data-module="google-map"
        <% if $MarkersAsJson %> data-markers="$MarkersAsJson"<% end_if %>
        <% if $OptionsAsJson %> data-options="$OptionsAsJson"<% end_if %>
            style="width:100%;height:500px;"></div>
</section>