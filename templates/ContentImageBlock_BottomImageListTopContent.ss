<div class="content-image-block__bottom-image-list-top-content">
    <div class="content-image-block__bottom-image-list-top-content--content">
        <h2>$Title</h2>

        $Content
    </div>

    <div class="content-image-block__bottom-image-list-top-content--picture">
        <% loop $Images %>
            <div class="content-image-block__bottom-image-list-top-content--image">
                $Fill(250, 250)
            </div>
        <% end_loop %>
    </div>
</div>

