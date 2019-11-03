$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

    $('.add-item').click(function (event) {
        event.preventDefault();

        var list = $($(this).attr('data-list'));
        var counter = list.children().length;

        var newWidget = list.attr('data-prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data('widget-counter', counter);

        var newElem = $(newWidget);
        newElem.appendTo(list);
    });

    $(document).on('click', '.remove-item', function (event) {
        event.preventDefault();

        $(this).closest('.list-item').remove();
    });
});
