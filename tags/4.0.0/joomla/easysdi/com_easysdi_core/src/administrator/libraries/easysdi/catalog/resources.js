js = jQuery.noConflict();
js(document).ready(function() {

    // Change publish date field to Calendar field
    Calendar.setup({
        // Id of the input field
        inputField: "published",
        // Format of the input field
        ifFormat: "%Y-%m-%d",
        // Trigger for the calendar (button ID)
        button: "published_img",
        // Alignment (defaults to "Bl")
        align: "Bl",
        singleClick: true,
        firstDay: 1
    });

});

function showModal(id) {
    js('html, body').animate({ scrollTop: 0 }, 'slow');
    js('input[name^="id"]').val(id);
    js('#publishModal').modal('show');
}

function onVersionChange(resourceid) {
    js('.' + resourceid + '_linker').each(function() {
        var href = js(this).attr("href");
        var i = href.lastIndexOf("/");
        var newhref = href.substring(0, i + 1);
        js(this).attr("href", newhref + js("select#" + resourceid + "_select").val());
    });
}

