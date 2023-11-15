/**************************************** PRELOADER ********************************************************/

if(document.getElementById("loader-wrapper")){
window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
    document.getElementById("loader-wrapper").classList.add("fadeout");
});
}

/******************************* Put animation classes after full page load *******************************************************/
if(document.getElementById("loader-wrapper")){
 if(document.querySelector(".frontpage-style-1")){

    window.addEventListener("load", function(event) {
    "use strict";
        var h1 = document.querySelector('.frontpage-style-1 h1');
        var desc = document.querySelector('.frontpage-row-1-desc');
        var btn1 = document.querySelector('.hero-button.join');
        var btn2 = document.querySelector('.hero-button.alternative');
        h1.classList.add('bounceInDown');
        desc.classList.add('bounceInLeft');
        btn1.classList.add('fadeInUp');
        btn2.classList.add('fadeInUp', 'delayed');
    });

 }
}

/******************************* Truncate menu elements *******************************************************/
if(document.querySelector("nav.main-navs")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
    var viewport = window.innerWidth;
    var tabsCount = document.querySelectorAll('nav.main-navs ul li').length;
    if (tabsCount > 8 && viewport > 1299) {

        var newEl = document.createElement('li');
        newEl.className = "object-nav-menu-button";
        var newEl2 = document.createElement('a');
        newEl2.className = "object-nav-menu-button-a";
        var newEl3 = document.createElement('div');
        newEl3.className = "object-nav-menu";
        let el = document.querySelectorAll('nav.main-navs ul li')[7];
        var parentEl = el.parentNode;
        parentEl.insertBefore(newEl, el);
        newEl.appendChild(newEl2);
        newEl.appendChild(newEl3);

        for (var i = 8; i < tabsCount +1; i++) {
        document.querySelectorAll('.object-nav-menu')[0].appendChild(document.querySelectorAll('.main-navs ul li')[i]);
        }

    } else if (tabsCount > 5 && viewport < 1300 && viewport > 767 ) {

        var newEl = document.createElement('li');
        newEl.className = "object-nav-menu-button";
        var newEl2 = document.createElement('a');
        newEl2.className = "object-nav-menu-button-a";
        var newEl3 = document.createElement('div');
        newEl3.className = "object-nav-menu";
        let el = document.querySelectorAll('nav.main-navs ul li')[6];
        var parentEl = el.parentNode;
        parentEl.insertBefore(newEl, el);
        newEl.appendChild(newEl2);
        newEl.appendChild(newEl3);

        for (var i = 7; i < tabsCount +1; i++) {
        document.querySelectorAll('.object-nav-menu')[0].appendChild(document.querySelectorAll('.main-navs ul li')[i]);
        }

    } else if (tabsCount > 3 && viewport < 767) {

        var newEl = document.createElement('li');
        newEl.className = "object-nav-menu-button";
        var newEl2 = document.createElement('a');
        newEl2.className = "object-nav-menu-button-a";
        var newEl3 = document.createElement('div');
        newEl3.className = "object-nav-menu";
        let el = document.querySelectorAll('nav.main-navs ul li')[3];
        var parentEl = el.parentNode;
        parentEl.insertBefore(newEl, el);
        newEl.appendChild(newEl2);
        newEl.appendChild(newEl3);

        for (var i = 4; i < tabsCount +1; i++) {
        document.querySelectorAll('.object-nav-menu')[0].appendChild(document.querySelectorAll('.main-navs ul li')[i]);
        }

    }

    if(document.querySelector(".object-nav-menu-button")){
        document.querySelector('.object-nav-menu-button').addEventListener('click', function() {
            let el = document.querySelector('.object-nav-menu');
            el.classList.toggle('show');
            el.classList.toggle('fadeInFast');
        });
    }

});

}
/*********************************************************************************************************************/


/******************************* Truncate WooCommerce tabs *******************************************************/
if(document.querySelector(".woocommerce-tabs")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
    var viewport = window.innerWidth;
    var tabsCount = document.querySelectorAll('.woocommerce-tabs ul.tabs li').length;
    if (tabsCount > 2 && viewport < 767) {

        var newEl = document.createElement('li');
        newEl.className = "woo-object-nav-menu-button";
        //var newEl2 = document.createElement('a');
        //newEl2.className = "woo-object-nav-menu-button-a";
        var newEl3 = document.createElement('div');
        newEl3.className = "woo-object-nav-menu";
        let el = document.querySelectorAll('.woocommerce-tabs ul.tabs li')[2];
        var parentEl = el.parentNode;
        parentEl.insertBefore(newEl, el);
        //newEl.appendChild(newEl2);
        newEl.appendChild(newEl3);

        for (var i = 3; i < tabsCount +1; i++) {
        document.querySelectorAll('.woo-object-nav-menu')[0].appendChild(document.querySelectorAll('.woocommerce-tabs ul.tabs li')[i]);
        }

    }

    if(document.querySelector(".woo-object-nav-menu-button")){
        document.querySelector('.woo-object-nav-menu-button').addEventListener('click', function() {
            let el = document.querySelector('.woo-object-nav-menu');
            el.classList.toggle('show');
            el.classList.toggle('fadeInFast');
        });
    }

});

}
/*********************************************************************************************************************/



/******************************* Drop down menu for BP widgets *******************************************************/
if(document.querySelector(".widget_bp_groups_widget")){

function bpWidgetButton() {
let el = document.querySelectorAll('.widget_bp_groups_widget .sidebar-title');
let htmlString = "<span class='menu-button'></span>";
el[0].insertAdjacentHTML('beforeend', htmlString);
}


function bpWidgetMenu() {
"use strict";
    let el = document.querySelectorAll('.widget .item-options');
    el[0].classList.toggle('show');
    el[0].classList.toggle('fadeInFast');
}

function bpWidgetMenuAction() {
    document.querySelectorAll('.menu-button')[0].addEventListener("click", bpWidgetMenu, false);
}


window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
    bpWidgetButton();
    bpWidgetMenuAction();
});

}

/*************************************************************************************************************/


/********************** Header messages and notifications drop down menus ***********************************/

if(document.querySelector(".top-bar-messages-menu-container")){
 window.addEventListener("DOMContentLoaded", function(event) {
 "use strict";
  document.querySelectorAll('.top-bar-messages')[0].addEventListener('click', function() {
    let el = document.querySelectorAll('.top-bar-messages-menu-container');
    el[0].classList.toggle('show');
    el[0].classList.toggle('fadeInFast');
  });
 });
}

if(document.querySelector(".notifications-list-container")){
 window.addEventListener("DOMContentLoaded", function(event) {
 "use strict";
  document.querySelectorAll('.top-bar-notifications')[0].addEventListener('click', function() {
    let el = document.querySelectorAll('.notifications-list-container');
    el[0].classList.toggle('show');
    el[0].classList.toggle('fadeInFast');
  });
 });
}

if(document.querySelector(".user-top-menu-container")){
 window.addEventListener("DOMContentLoaded", function(event) {
 "use strict";
  document.getElementById("user-top-menu-expander").addEventListener('click', function() {
    let el = document.querySelectorAll('.user-top-menu-container');
    el[0].classList.toggle('show');
    el[0].classList.toggle('fadeInFast');
  });
 });
}

/*************************************************************************************************************/


/*************************************** Bbpress breadcrumbs improve *****************************************/

if(document.querySelector(".bbp-breadcrumb")){
window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
    var el = document.querySelectorAll('.bbp-breadcrumb-sep');
    for (var i = 0; i < el.length; i++) {
    el[i].textContent = " / ";
    }
 });
}

/*************************************************************************************************************/

/*************************************************************************************************************/

if(document.querySelector(".wp-caption")){
window.addEventListener("DOMContentLoaded", function(event) {
    var el = document.querySelectorAll(".wp-caption");
    for (i = 0; i < el.length; i++) {
        el[i].removeAttribute("style");
    }

    var el2 = document.querySelectorAll(".wp-caption img");
    for (i = 0; i < el2.length; i++) {
        el2[i].removeAttribute("width");
    }

    var el3 = document.querySelectorAll(".wp-caption img");
    for (i = 0; i < el3.length; i++) {
        el3[i].removeAttribute("height");
    }
});
}

/*************************************************************************************************************/

/*************************************** MOBILE MENU *******************************************************/

if(document.getElementById('mobile-nav')){
document.getElementById('mobile-nav').addEventListener('click', function() {
    let el = document.querySelector('#mobile-nav div.header-menu-mobile-container');
    el.classList.toggle('show');
    el.classList.toggle('fadein');
});
}

/*************************************************************************************************************/


/*************************************** MOBILE SEARCH *******************************************************/

if(document.getElementById('header-search-mobile')){
document.getElementById('header-search-mobile-icon').addEventListener('click', function() {
    let el = document.querySelector('#header-search-mobile .header-search-mobile-field');
    el.classList.toggle('show');
    el.classList.toggle('fadein');
});
}

/*************************************************************************************************************/

/********************************************* MASONRY *****************************************************************/

if(document.querySelector(".blog-1")){

window.addEventListener("DOMContentLoaded", function(event) {

imagesLoaded( document.querySelector('.blog-1'), function( instance ) {
var elem = document.querySelector('.blog-1');
var msnry = new Masonry( elem, {
    // options
    itemSelector: 'li.box-blog-entry',
        isAnimated: true,
        animationOptions: {
        duration: 1,
        easing: 'linear',
        queue: false
        }
    });
});

});

}



if(document.querySelector("body.woocommerce ul.products")){

window.addEventListener("DOMContentLoaded", function(event) {

imagesLoaded( document.querySelector('.products'), function( instance ) {
var elem = document.querySelector('ul.products');
var msnry = new Masonry( elem, {
    // options
    itemSelector: 'li.product',
        isAnimated: true,
        animationOptions: {
        duration: 1,
        easing: 'linear',
        queue: false
        }
    });
});

});

}



if(document.querySelector("ul.learnpress-featured")){

window.addEventListener("DOMContentLoaded", function(event) {

imagesLoaded( document.querySelector('ul.learnpress-featured'), function( instance ) {
var elem = document.querySelector('ul.learnpress-featured');
var msnry = new Masonry( elem, {
    // options
    itemSelector: 'li.course-entry',
    isAnimated: true,
    animationOptions: {
    duration: 1,
    easing: 'linear',
    queue: false
    }
    });
});

});

}

/*************************************************************************************************************/

/************************************************ CLASSIC BLOG POSTS LOADING *************************************************************/

if(document.querySelector(".page-template-blog-classic")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    document.querySelectorAll('body.page-template-blog-classic #object-nav ul li, body.index-posts #object-nav ul li').forEach(function(el){
    el.addEventListener('click', function() {

        var button = document.querySelector('body.page-template-blog-classic #object-nav ul li.current, body.index-posts #object-nav ul li.current');
        button.classList.remove('current');
        var more = document.getElementById('load-more-posts-classic');
        more.classList.remove('show');
        this.classList.add('current', 'dd-loading');
        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');

        // Update attributes of load more button
        more.setAttribute("data-posts-type", blog_posts_type);
        more.setAttribute("data-tab-page", page);

        var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.querySelector('body.page-template-blog-classic #object-nav ul li.current, body.index-posts #object-nav ul li.current');
            var el = document.querySelector('.blog-classic');
            el.innerHTML = '';
            var content = this.response;
            var content = content.replace(/class="blog-post"/gi, 'class="blog-post fadein"');
            var count = content.match(/class="blog-post fadein"/g).length;

            if(count < 3) {
                button.classList.add('no-more');
                button.classList.remove('dd-loading');
            }

            el.insertAdjacentHTML('afterbegin', content);
            button.classList.remove('dd-loading');

            } else {
            // Response error
            console.log('Response error');
            button.classList.remove('dd-loading');
        }
        };
        r.onerror = function() {
        // Connection error
        console.log('Connection error');
        button.classList.remove('dd-loading');
        };
        r.send('action=onecommunity_blog_classic&blog_posts_type=' + blog_posts_type + '&page=' + page + '');

    });
    });

});


    document.getElementById('load-more-posts-classic').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');
        var page_next = Number(page) + 1;

        var button = document.getElementById('load-more-posts-classic');
        button.setAttribute('data-tab-page', page_next);

        var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var el = document.querySelector('.blog-classic');
            var content = this.response;
            var content = content.replace(/class="blog-post"/gi, 'class="blog-post fadein"');
            var count = content.match(/class="blog-post fadein"/g).length;

            if(count < 3) {
                button.classList.remove('dd-loading');
                button.classList.remove('show');
                button.classList.add('no-more');
            }

            el.insertAdjacentHTML('beforeend', content);
            button.classList.remove('dd-loading');

        } else {
        // Response error
            console.log('Response error');
            button.classList.remove('dd-loading');
        }
        };
        r.onerror = function() {
        // Connection error
        console.log('Connection error');
        button.classList.remove('dd-loading');
        };
        r.send('action=onecommunity_blog_classic&blog_posts_type=' + blog_posts_type + '&page=' + page_next + '');

    });

}

/*************************************************************************************************************/

/************************************* AJAX BLOG POSTS LOADING *****************************************************/

if(document.getElementById("load-more-posts-1")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    document.querySelectorAll('body.page-template-blog-1 #object-nav ul li, body.page-template-blog-2 #object-nav ul li, body.home.blog #object-nav ul li').forEach(function(el){
    el.addEventListener('click', function() {

        var button = document.querySelector('body.page-template-blog-1 #object-nav ul li.current, body.page-template-blog-2 #object-nav ul li.current, body.home.blog #object-nav ul li.current');
        var more = document.getElementById('load-more-posts-1');
        button.classList.remove('current');
        this.classList.add('current', 'dd-loading');

        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');

        // Update attributes of load more button
        more.setAttribute("data-posts-type", blog_posts_type);
        more.setAttribute("data-tab-page", page);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.querySelector('body.page-template-blog-1 #object-nav ul li.current, body.page-template-blog-2 #object-nav ul li.current, body.home.blog #object-nav ul li.current');
            var el = document.querySelector('ul.blog-1');
            el.innerHTML = '';
            var content = this.response;
            var content = content.replace(/class="box-blog-entry/gi, 'class="box-blog-entry fadein');
            var count = content.match(/class="box-blog-entry fadein/g).length;

            if(count < 6) {
            more.classList.add('no-more');
            }

            el.insertAdjacentHTML('afterbegin', content);
            button.classList.remove('dd-loading');

            // Masonry load
            imagesLoaded( document.querySelector('.blog-1'), function( instance ) {
            var elem = document.querySelector('.blog-1');
            var msnry = new Masonry( elem, {
            // options
            itemSelector: 'li.box-blog-entry',
                isAnimated: true,
                animationOptions: {
                duration: 1,
                easing: 'linear',
                queue: false
            }
            });
            });



        } else {
            // Response error
            console.log('Response error');
        }
        };
        request.onerror = function() {
        // Connection error
            console.log('Connection error');
        };
        request.send('action=onecommunity_blog_1&blog_posts_type=' + blog_posts_type + '&page=' + page + '');

    });
    });


    document.getElementById('load-more-posts-1').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');
        var page_next = Number(page) + 1;

        var button = document.getElementById('load-more-posts-1');
        button.setAttribute('data-tab-page', page_next);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.getElementById('load-more-posts-1');
            var el = document.querySelector('ul.blog-1');
            var content = this.response;
            var content = content.replace(/class="box-blog-entry/gi, 'class="box-blog-entry fadein');
            var count = content.match(/class="box-blog-entry fadein/g).length;

            if(count < 6) {
                button.classList.add('no-more');
            }

            el.insertAdjacentHTML('beforeend', content);

            // Masonry load
            imagesLoaded( document.querySelector('.blog-1'), function( instance ) {
            var elem = document.querySelector('.blog-1');
            var msnry = new Masonry( elem, {
            // options
            itemSelector: 'li.box-blog-entry',
                isAnimated: true,
                animationOptions: {
                duration: 1,
                easing: 'linear',
                queue: false
            }
            });
            });

            button.classList.remove('dd-loading');
        } else {
           // Response error
            console.log('Response error');
        }
        };
        request.onerror = function() {
        // Connection error
            console.log('Connection error');
        };
        request.send('action=onecommunity_blog_1&blog_posts_type=' + blog_posts_type + '&page=' + page_next + '');


    });

});

}

/*************************************************************************************************************/

/******************************** ARCHIVE CATEGORY POSTS LOADING *********************************************/

if(document.getElementById("load-more-archive")){

    window.addEventListener("DOMContentLoaded", function(event) {
    "use strict";

        document.querySelectorAll('#object-nav.archive-tabs ul li').forEach(function(el){
        el.addEventListener('click', function() {

            var button = document.querySelector('#object-nav.archive-tabs ul li.current');
            button.classList.remove('current');
            var more = document.getElementById('load-more-archive');
            more.classList.remove('show');
            this.classList.add('current', 'dd-loading');
            var blog_posts_type = this.getAttribute('data-posts-type');
            var page = this.getAttribute('data-tab-page');
            var taxonomy = this.getAttribute('data-taxonomy');
            var term_id = this.getAttribute('data-term-id');

            // Update attributes of load more button
            more.setAttribute("data-posts-type", blog_posts_type);
            more.setAttribute("data-tab-page", page);

            var r = new XMLHttpRequest();
            r.open('POST', ajaxurl, true);
            r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            r.onload = function () {
            if (this.status >= 200 && this.status < 400) {

                var button = document.querySelector('#object-nav.archive-tabs ul li.current');
                var el = document.querySelector('.blog-1');
                el.innerHTML = '';
                var content = this.response;
                var content = content.replace(/class="box-blog-entry/gi, 'class="box-blog-entry fadein');
                var count = content.match(/class="box-blog-entry fadein/g).length;

                el.insertAdjacentHTML('afterbegin', content);

                    // Masonry load
                    imagesLoaded( document.querySelector('.blog-1'), function( instance ) {
                    var elem = document.querySelector('.blog-1');
                    var msnry = new Masonry( elem, {
                    // options
                    itemSelector: 'li.box-blog-entry',
                        isAnimated: true,
                        animationOptions: {
                        duration: 1,
                        easing: 'linear',
                        queue: false
                    }
                    });
                    });


                button.classList.remove('dd-loading');

                } else {
                // Response error
                console.log('Response error');
                button.classList.remove('dd-loading');
            }
            };
            r.onerror = function() {
            // Connection error
            console.log('Connection error');
            button.classList.remove('dd-loading');
            };
            r.send('action=onecommunity_blog_archive_tabs&blog_posts_type=' + blog_posts_type + '&page=' + page + '&taxonomy=' + taxonomy + '&term_id=' + term_id + '');

        });
        });

    });


    /******************************** ARCHIVE CATEGORY POSTS LOADING *********************************************/

 

    document.getElementById('load-more-archive').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');
        var page_next = Number(page) + 1;
        var taxonomy = this.getAttribute('data-taxonomy');
        var term_id = this.getAttribute('data-term-id');

        var button = document.getElementById('load-more-archive');
        button.setAttribute('data-tab-page', page_next);

        var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var el = document.querySelector('.blog-1');
            var content = this.response;
            console.log(content.length);

            if(content.length > 50){
                var content = content.replace(/class="box-blog-entry/gi, 'class="box-blog-entry fadein');
                var count = content.match(/class="box-blog-entry fadein/g).length;
            } else {
                button.classList.remove('show');
                button.classList.add('no-more');
            }

            if(count < 6) {
                button.classList.remove('show');
                button.classList.add('no-more');
            }

            el.insertAdjacentHTML('beforeend', content);

                // Masonry load
                imagesLoaded( document.querySelector('.blog-1'), function( instance ) {
                var elem = document.querySelector('.blog-1');
                var msnry = new Masonry( elem, {
                // options
                itemSelector: 'li.box-blog-entry',
                    isAnimated: true,
                    animationOptions: {
                    duration: 1,
                    easing: 'linear',
                    queue: false
                }
                });
                });

            button.classList.remove('dd-loading');

        } else {
        // Response error
            console.log('Response error');
            button.classList.remove('dd-loading');
        }
        };
        r.onerror = function() {
        // Connection error
        console.log('Connection error');
        button.classList.remove('dd-loading');
        };
        r.send('action=onecommunity_blog_archive_more&blog_posts_type=' + blog_posts_type + '&page=' + page_next + '&taxonomy=' + taxonomy + '&term_id=' + term_id + '');

    });

}

/******************************** ANIMATED COUNTER *********************************************/

if( document.querySelector('.counter') ){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

setTimeout(function(){

var counters = document.querySelectorAll('.counter');
var speed = 200;

counters.forEach(counter => {
    var updateCount = () => {
        var target = +counter.getAttribute('data-target');
        var count = +counter.innerText;

        // Lower inc to slow and higher to slow
        var inc = parseInt(target / speed) +1;

        // Check if target is reached
        if (count < target) {
            // Add inc to count and output in counter
            counter.innerText = count + inc;
            // Call function every ms
            setTimeout(updateCount, 1);
        } else {
            counter.innerText = target;
        }
    };

    updateCount();
});

}, 1600);

});

}

/**************************************** MOBILE LEFT SIDEBAR ********************************************************/

if(document.getElementById('left-sidebar')){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

var panel = document.getElementById('left-sidebar');
var btn = document.getElementById('left-sidebar-trigger');
var close = document.getElementById('left-sidebar-close');

    btn.addEventListener('click', function() {
    "use strict";

        if(panel.classList.contains('fadeOutLeft')) {
            panel.classList.remove('fadeOutLeft');
        }

        panel.classList.add('fadeInLeft');
        btn.classList.add('hide');
        var yOffset = window.pageYOffset+30;
        panel.style.top = yOffset + 'px';

    });

    close.addEventListener('click', function() {
    "use strict";
        panel.classList.remove('fadeInLeft');
        panel.classList.add('fadeOutLeft');
        btn.classList.remove('hide');
        btn.classList.add('fadeInLeft');
    });   

}); 

}

