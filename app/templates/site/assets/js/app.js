/**
 * Variables
 */

var owl = $('.owl-carousel');

/**
 * Owl Carousel
 */
if(owl.length) {
    owl.owlCarousel({
        margin: 10,
        responsiveClass: true,
        responsive:{
            0:{
                items: 1,
                nav:true,
                dots: true,
            },
            600:{
                items: 2,
                nav: false,
                dots: true
            },
            1000:{
                items: 4,
                nav: true,
                loop: false,
                dots: true
            }
        }
    });
}

$('[data-toggle="tooltip"]').tooltip();