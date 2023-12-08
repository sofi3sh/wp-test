jQuery(document).ready(function($) {
    $('.article-card').on('click', function(e) {
        e.stopPropagation();

        var $popupContent = $(this).next('.popup-content');

        if ($popupContent.length > 0) {
            $popupContent.css('display', 'block');
            $('body').addClass('body-lock');

            if ($(window).width() <= 768) {
                $('html').addClass('mobile-scroll');
            }


            $(document).on('click.popup', function(event) {
                if (!$popupContent.is(event.target) && $popupContent.has(event.target).length === 0) {
                    $popupContent.css('display', 'none');
                    $('body').removeClass('body-lock');
                    $('html').removeClass('mobile-scroll');
                    $(document).off('click.popup');
                }
            });
        }
    });

    $('.close-popup').on('click', function(e) {
        e.stopPropagation();

        var $popupContent = $(this).closest('.popup-content');
        $popupContent.css('display', 'none');
        $('body').removeClass('body-lock');
        $('html').removeClass('mobile-scroll');
        $(document).off('click.popup');
    });
});
