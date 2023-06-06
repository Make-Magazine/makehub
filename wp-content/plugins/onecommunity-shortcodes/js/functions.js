/******************************* Side nav with members *******************************************************/

if(document.getElementById("sidenav")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";
    document.getElementById('sidenav-button').addEventListener('click', function() {
        document.getElementById('sidenav').classList.toggle('sidenav-expand');
    });

    document.getElementById('sidenav-drop-down-menu').addEventListener('click', function() {
        let el = document.getElementById('sidenav-ul');
        el.classList.toggle('show');
        el.classList.toggle('fadeInFast');
    });
});


window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    var h = window.innerHeight;
    var el = document.querySelector('#sidenav .tab-content');

    if(h < 900) {
    el.classList.add('small-screen');
    }

    document.querySelectorAll('#sidenav-ul li').forEach(function(el){
    el.addEventListener('click', function() {

    var el = document.querySelector('#sidenav .tab-content');

    var members_loop_type = this.getAttribute('data-tab');
    document.querySelector('#sidenav-ul li.current').classList.remove('current');
    this.classList.add('current', 'dd-loading');

    var r = new XMLHttpRequest();
    r.open('POST', ajaxurl, true);
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    r.onload = function () {

    if (this.status >= 200 && this.status < 400) {

        var content = this.response;
        el.innerHTML = '';
        el.insertAdjacentHTML('afterbegin','<div class="tab-content-wrap fadein">' + content + '</div>');
        document.querySelector('#sidenav-ul li.dd-loading').classList.remove('dd-loading');

    } else {
        // Response error
        console.log('Response error');
    }
    };
    r.onerror = function() {
    // Connection error
        console.log('Connection error');
    };
    r.send('action=onecommunity_sidenav_load&members_loop_type=' + members_loop_type + '');

    });

    });
});

}


/******************************* Groups tabs and load more button *******************************************************/

if(document.querySelector(".shortcode-bp-groups-tabs-container")){
window.addEventListener("DOMContentLoaded", function(event) {

    document.querySelectorAll('.shortcode-bp-groups-tabs-container #object-nav ul li').forEach(function(el){
    el.addEventListener('click', function() {

    document.querySelector('.shortcode-bp-groups-tabs-container li.current').classList.remove('current');
    document.querySelector('.load-more-groups').classList.remove('no-more');
    this.classList.add('current', 'dd-loading');

    var groups_type = this.getAttribute('data-tab');
    var per_page = this.getAttribute('data-tab-per-page');
    var page = this.getAttribute('data-tab-page');

// Update attributes of load more button
    var more = document.querySelector('.load-more-groups');
    more.setAttribute("data-tab", groups_type);
    more.setAttribute("data-tab-per-page", per_page);
    more.setAttribute("data-tab-page", page);

    var r = new XMLHttpRequest();
    r.open('POST', ajaxurl, true);
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    r.onload = function () {
    if (this.status >= 200 && this.status < 400) {

        var el = document.querySelector('.shortcode-bp-groups-tabs-container .list-wrap ul');
        el.innerHTML = '';
        var content = this.response;
        var content = content.replace(/group-has-avatar/gi, 'group-has-avatar fadein');
        var count = content.match(/class="group-box"/g).length;

        if(count < per_page) {
            document.querySelector('.load-more-groups').classList.add('no-more');
        }

        el.insertAdjacentHTML('afterbegin', content);

        document.querySelector('.shortcode-bp-groups-tabs-container li.current').classList.remove('dd-loading');

    } else {
        // Response error
        console.log('Response error');
        document.querySelector('.shortcode-bp-groups-tabs-container li.current').classList.remove('dd-loading');
    }
    };
    r.onerror = function() {
    // Connection error
        console.log('Connection error');
        document.querySelector('.shortcode-bp-groups-tabs-container li.current').classList.remove('dd-loading');
    };
    r.send('action=onecommunity_bp_groups_listing_load&groups_type=' + groups_type + '&per_page=' + per_page + '&page=' + page + '');

    });

    });


    document.querySelector('.load-more-groups').addEventListener('click', function() {

    var more_btn = document.querySelectorAll('.shortcode-bp-groups-tabs-container .load-more-groups span');
    more_btn[0].classList.add('dd-loading');

    var groups_type = this.getAttribute('data-tab');
    var per_page = this.getAttribute('data-tab-per-page');
    var page = this.getAttribute('data-tab-page');
    var page_next = Number(page) + 1;

    document.querySelector('.load-more-groups').setAttribute('data-tab-page', page_next);

    var r = new XMLHttpRequest();
    r.open('POST', ajaxurl, true);
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    r.onload = function () {
    if (this.status >= 200 && this.status < 400) {

        var content = this.response;
        var content = content.replace(/group-has-avatar/gi, 'group-has-avatar fadein');
        var count = content.match(/class="group-box"/g).length;
        more_btn[0].innerHTML = '';
        more_btn[0].insertAdjacentHTML('afterbegin','Load More');

        if(count < per_page) {
            document.querySelector('.load-more-groups').classList.add('no-more');
        }

        var el = document.querySelector('.shortcode-bp-groups-tabs-container .list-wrap ul');
        el.insertAdjacentHTML('beforeend', content);
        more_btn[0].classList.remove('dd-loading');

    } else {
        // Response error
        console.log('Response error');
        more_btn[0].classList.remove('dd-loading');
    }
    };
    r.onerror = function() {
    // Connection error
        console.log('Connection error');
        more_btn[0].classList.remove('dd-loading');
    };
    r.send('action=onecommunity_bp_groups_listing_load&groups_type=' + groups_type + '&per_page=' + per_page + '&page=' + page_next + '');


    });


});

}



/******************************* Popular post *******************************************************/


if(document.querySelector(".shortcode-popular-posts")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    var sidebar_box = document.querySelectorAll('.sidebar-box');
    var popular_posts = document.querySelector('.shortcode-popular-posts');

    for (var i = 0; i < sidebar_box.length; i++) {
        if (sidebar_box[i].contains(popular_posts)) {
        sidebar_box[i].classList.add('sidebar-box-popular-posts');
        }
    }


    var menuDropDown = document.querySelector('.shortcode-popular-posts-menu-drop-down');

    document.querySelector('.shortcode-popular-posts-menu').addEventListener('click', function() {
        menuDropDown.classList.toggle('show');
        menuDropDown.classList.toggle('fadein');
    });

    document.querySelectorAll('.shortcode-popular-posts-menu-drop-down span').forEach(function(el){
    el.addEventListener('click', function() {

    menuDropDown.classList.remove('show');
    document.querySelector('.shortcode-popular-posts-menu-drop-down span.current').classList.remove('current');
    this.classList.add('current');

    var posts_list_type = this.getAttribute('data-tab');
    var tab_title = this.textContent;
    var per_page = this.getAttribute('data-per-page');

    var r = new XMLHttpRequest();
    r.open('POST', ajaxurl, true);
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    r.onload = function () {

    if (this.status >= 200 && this.status < 400) {


        var el = document.querySelector('.shortcode-popular-posts .tab-news-content');
        var title = document.querySelector('.sidebar-box-popular-posts .sidebar-title');
        title.innerHTML = '';
        var text = document.createTextNode(tab_title);
        title.appendChild(text);
        title.classList.add('dd-loading');
        var content = this.response;
        el.innerHTML = '';
        el.insertAdjacentHTML('afterbegin',content);
        title.classList.remove('dd-loading');

    } else {
        // Response error
        console.log('Response error');
    }
    };
    r.onerror = function() {
    // Connection error
        console.log('Connection error');
    };
    r.send('action=onecommunity_top_news_load&posts_list_type=' + posts_list_type + '&per_page=' + per_page + '');

    });
    });

});

}


/******************************* Frontpage activities *******************************************************/

if(document.getElementById("tabs-activity")){


window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    var trigger = document.createElement('a');
    var triggerContainer = document.getElementById('activity-menu-button');
    triggerContainer.appendChild(trigger);

    if(document.querySelector("#activity-menu-button h4.elementor-heading-title")){
        var triggerContainerElementor = document.querySelector('#activity-menu-button h4.elementor-heading-title');
        triggerContainerElementor.appendChild(trigger);
    }

    // Masonry load
    var contentList = document.querySelector('ul.tab-content-list');
    imagesLoaded( contentList, function( instance ) {
        var msnry = new Masonry( contentList, {
        // options
        itemSelector: 'li.activity-item',
            isAnimated: true,
            animationOptions: {
            duration: 1,
            easing: 'linear',
            queue: false
        }
        });
    });

    var nav = document.querySelector('.tabs-activity-nav');

    document.querySelector('#activity-menu-button a').addEventListener('click', function() {
        nav.classList.toggle('show');
        nav.classList.toggle('fadeInFast');
    });

    document.querySelectorAll('#tabs-activity .tabs-activity-nav li').forEach(function(el){
    el.addEventListener('click', function() {


        var tab_activity_type = this.getAttribute('data-tab-type');
        var tab_activity_page = this.getAttribute('data-tab-page');

        document.querySelector('#tabs-activity .tabs-activity-nav li.current').classList.remove('current');
        this.classList.add('current', 'dd-loading');


    var r = new XMLHttpRequest();
    r.open('POST', ajaxurl, true);
    r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    r.onload = function () {

    if (this.status >= 200 && this.status < 400) {

        var content = this.response;
        contentList.innerHTML = '';
        contentList.insertAdjacentHTML('afterbegin', content);
        document.querySelector('#tabs-activity .tabs-activity-nav li.dd-loading').classList.remove('dd-loading');
        nav.classList.remove('show');

            // Masonry load
            imagesLoaded( contentList, function( instance ) {
                var msnry = new Masonry( contentList, {
                // options
                itemSelector: 'li.activity-item',
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
    r.onerror = function() {
    // Connection error
        console.log('Connection error');
    };
    r.send('action=onecommunity_activity_load&tab_activity_type=' + tab_activity_type + '&tab_activity_page=' + tab_activity_page + '');


    });
    });
    });

}




/******************************* Category blog posts *******************************************************/


if(document.querySelector("ul.cat-posts-list")){

window.addEventListener("DOMContentLoaded", function(event) {

imagesLoaded( document.querySelector('ul.cat-posts-list'), function( instance ) {
var elem = document.querySelector('.cat-posts-list');
var msnry = new Masonry( elem, {
    // options
    itemSelector: 'li.recent-post',
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

if(document.getElementById("shortcode-category-posts")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

var trigger = document.createElement('a');
var triggerContainer = document.getElementById('shortcode-posts-menu-button');
triggerContainer.appendChild(trigger);

if(document.querySelector(".elementor-heading-title")){
var triggerContainerElementor = document.querySelector('#shortcode-posts-menu-button .elementor-heading-title');
triggerContainerElementor.appendChild(trigger);
}


    var buttons = document.querySelectorAll('#shortcode-category-posts .cat-item a');
    for (let i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', e => e.preventDefault());
    }

    var nav = document.querySelector('#shortcode-category-posts .shortcode-category-posts-menu');

    document.querySelector('#shortcode-posts-menu-button a').addEventListener('click', function() {
        nav.classList.toggle('show');
        nav.classList.toggle('fadeInFast');
    });

    var cat_class_first = document.querySelectorAll('#shortcode-category-posts .shortcode-category-posts-menu li');
    var cat_class_first = cat_class_first[0].getAttribute('class');
    var cat_class_first_id = cat_class_first.replace( /^\D+/g, '');

    document.querySelectorAll('#shortcode-category-posts li.cat-item').forEach(function(el){
    el.addEventListener('click', function() {

        var cat_classes = this.getAttribute('class');
        var catid = cat_classes.replace( /^\D+/g, '');
        document.querySelector('#shortcode-category-posts .shortcode-category-posts-menu li.current').classList.remove('current');
        this.classList.add('current', 'dd-loading');

        /** Request for big post **/

        var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {

        if (this.status >= 200 && this.status < 400) {

            var contentContainer = document.querySelector('#shortcode-category-posts ul.big-post');
            var content = this.response;
            contentContainer.innerHTML = '';
            contentContainer.insertAdjacentHTML('afterbegin', content);
            nav.classList.remove('show');
            document.querySelector('#shortcode-category-posts .shortcode-category-posts-menu li.dd-loading').classList.remove('dd-loading');

         } else {
            // Response error
            console.log('Response error');
        }
        };
        r.onerror = function() {
        // Connection error
            console.log('Connection error');
        };
        r.send('action=onecommunity_cat_big_post_load&cat_id=' + catid + '');

        /** Request for small posts view **/

        var r = new XMLHttpRequest();
        r.open('POST', ajaxurl, true);
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        r.onload = function () {

        if (this.status >= 200 && this.status < 400) {

            var contentContainer = document.querySelector('#shortcode-category-posts .cat-posts-list');
            var content = this.response;
            contentContainer.innerHTML = '';
            contentContainer.insertAdjacentHTML('afterbegin', content);


            // Masonry load
            imagesLoaded( document.querySelector('ul.cat-posts-list'), function( instance ) {
            var elem = document.querySelector('ul.cat-posts-list');
            var msnry = new Masonry( elem, {
            // options
            itemSelector: 'li.recent-post',
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
        r.onerror = function() {
        // Connection error
            console.log('Connection error');
        };
        r.send('action=onecommunity_cat_posts_load&cat_id=' + catid + '');


    });
    });

});

}



/**************************************** Dark Mode ***********************************************/

if(document.getElementById('dark-mode')){
    
document.getElementById('dark-mode').addEventListener('click', function() {
"use strict";

if(document.querySelector("body.dark-mode")){
document.cookie = "dark_mode=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
console.log("Dark mode cookie deleted");
location.reload(); 
} else {

var date = new Date();
var daysToExpire = 30;
date.setTime(date.getTime()+(daysToExpire*24*60*60*1000));
document.cookie = "dark_mode=true; path=/; expires=" + date;
location.reload(); 
}

});

}


/**************************************** Featured Courses Masonry ***********************************************/

if(document.querySelector(".learn-press-courses")){

window.addEventListener("DOMContentLoaded", function(event) {

imagesLoaded( document.querySelector('.learn-press-courses'), function( instance ) {
var elem = document.querySelector('.learn-press-courses');
var msnry = new Masonry( elem, {
    // options
    itemSelector: 'li.lp_course',
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


/************************************* More Featured Courses Loading *****************************************************/

if(document.getElementById("load-more-courses")){

window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    document.getElementById('load-more-courses').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var page = this.getAttribute('data-page');
        var duration = this.getAttribute('duration');
        var excerpt = this.getAttribute('excerpt');
        var page_next = Number(page) + 1;

        var button = document.getElementById('load-more-courses');
        button.setAttribute('data-page', page_next);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.getElementById('load-more-courses');
            var el = document.querySelector('ul.learnpress-featured');
            var content = this.response;
            console.log(content);
            var content = content.replace(/class="course-entry/gi, 'class="course-entry fadein');
            var count = content.match(/class="course-entry fadein/g).length;

            if(count < 3) {
                button.classList.add('no-more');
            }

            el.insertAdjacentHTML('beforeend', content);

            // Masonry load
            imagesLoaded( document.querySelector('.learnpress-featured'), function( instance ) {
            var elem = document.querySelector('.learnpress-featured');
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
        request.send('action=onecommunity_featured_courses_more&page=' + page_next + '&duration=' + duration + '&excerpt=' + excerpt + '');


    });

});

}

/*************************************************************************************************************/


/************************************* AJAX BLOG POSTS SHORTCODE LOADING *****************************************************/

if(document.querySelector(".shortcode-posts-container")){
window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    document.querySelectorAll('.shortcode-posts-container #object-nav ul li').forEach(function(el){
    el.addEventListener('click', function() {

        var button = document.querySelector('.shortcode-posts-container #object-nav ul li.current');
        var more = document.getElementById('shortcode-load-more-posts');
        button.classList.remove('current');
        this.classList.add('current', 'dd-loading');

        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');
        var per_page = this.getAttribute('data-per-page');

        // Update attributes of load more button
        more.setAttribute("data-posts-type", blog_posts_type);
        more.setAttribute("data-tab-page", page);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.querySelector('.shortcode-posts-container #object-nav ul li.current');
            var el = document.querySelector('ul.blog-1');
            el.innerHTML = '';
            var content = this.response;
            var content = content.replace(/class="box-blog-entry/gi, 'class="box-blog-entry fadein');
            var count = content.match(/class="box-blog-entry fadein/g).length;

            if(count < 3) {
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
        request.send('action=onecommunity_shortcode_blog_posts_load&blog_posts_type=' + blog_posts_type + '&page=' + page + '&per_page=' + per_page + '');

    });
    });


    document.getElementById('shortcode-load-more-posts').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var blog_posts_type = this.getAttribute('data-posts-type');
        var page = this.getAttribute('data-tab-page');
        var per_page = this.getAttribute('data-per-page');
        var page_next = Number(page) + 1;

        var button = document.getElementById('shortcode-load-more-posts');
        button.setAttribute('data-tab-page', page_next);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.getElementById('shortcode-load-more-posts');
            var el = document.querySelector('ul.blog-1');
            var content = this.response;
            var content = content.replace(/class="box-blog-entry/gi, 'class="box-blog-entry fadein');
            var count = content.match(/class="box-blog-entry fadein/g).length;

            if(count < per_page) {
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
        request.send('action=onecommunity_shortcode_blog_posts_more&blog_posts_type=' + blog_posts_type + '&page=' + page_next + '&per_page=' + per_page + '');


    });

});

}

/*************************************************************************************************************/



/************************************* BP GROUP BLOG MORE POSTS LOADING *****************************************************/

if(document.getElementById("bp-group-blog")){
window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    document.getElementById('bp-group-blog-load-more').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var page = this.getAttribute('data-tab-page');
        var group_id = this.getAttribute('data-group-id');
        var page_next = Number(page) + 1;

        var button = document.getElementById('bp-group-blog-load-more');
        button.setAttribute('data-tab-page', page_next);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.getElementById('bp-group-blog-load-more');
            var el = document.querySelector('#bp-group-blog ul.shortcode-small-recent-posts');
            var content = this.response;
            var content = content.replace(/class="item/gi, 'class="item fadein');
            var count = content.match(/class="item fadein/g).length;

            if(count < 10) {
                button.classList.add('no-more');
            }

            el.insertAdjacentHTML('beforeend', content);
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
        request.send('action=onecommunity_bp_group_blog_more&page=' + page_next + '&group_id=' + group_id + '');


    });

});

}

/*************************************************************************************************************/




/************************************* BP USER BLOG MORE POSTS LOADING *****************************************************/

if(document.getElementById("bp-user-blog")){
window.addEventListener("DOMContentLoaded", function(event) {
"use strict";

    document.getElementById('bp-user-blog-load-more').addEventListener('click', function() {

        this.classList.add('dd-loading');
        var page = this.getAttribute('data-tab-page');
        var user_id = this.getAttribute('data-user-id');
        var page_next = Number(page) + 1;

        var button = document.getElementById('bp-user-blog-load-more');
        button.setAttribute('data-tab-page', page_next);

        var request = new XMLHttpRequest();
        request.open('POST', ajaxurl, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function () {
        if (this.status >= 200 && this.status < 400) {

            var button = document.getElementById('bp-user-blog-load-more');
            var el = document.querySelector('#bp-user-blog ul.shortcode-small-recent-posts');
            var content = this.response;
            var content = content.replace(/class="item/gi, 'class="item fadein');
            var count = content.match(/class="item fadein/g).length;

            if(count < 10) {
                button.classList.add('no-more');
            }

            el.insertAdjacentHTML('beforeend', content);
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
        request.send('action=onecommunity_bp_user_blog_more&page=' + page_next + '&user_id=' + user_id + '');


    });

});

}

/*************************************************************************************************************/