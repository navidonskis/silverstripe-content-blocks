<div
    id="$ID"
    data-module="editor"
    data-update-marker-link="$Link('updateMarkers')"
    data-delete-marker-link="$Link('deleteMarkers')"
    data-zoom-changed-link="$Link('zoomChanged')"
    data-coordinates-changed-link="$Link('coordinatesChanged')"
    <% if $Markers %>data-markers="$Markers"<% end_if %>
    <% if $Options %>data-options="$Options"<% end_if %>
    <% if $Api %>data-key="$Api"<% end_if %>
></div>