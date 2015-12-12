var _open_horizontal_dropdown = false;
var _horizontal_layered_navigation_contents = false;
var _product_list_element = "#ajaxlist-reload-product_list";
//var _compare_sidebar_block = "#ajaxlist-reload-compare_sidebar";
var reload_nav = true;
var reload_compare = true;
var k = "";
var l = "";
function setReload_nav(a) {
    window.reload_nav = a;
}
function getReload_nav() {
    return window.reload_nav;
}
function setReload_compare(a) {
    window.reload_compare = a;
}
function getReload_compare() {
    return window.reload_compare;
}
function ajaxListURL(a) {
    if (a.indexOf(window._ajaxlist_parameter + "=1") < 0) {
        if (a.indexOf("?") < 0) {
            a = a + "?" + window._ajaxlist_parameter + "=1";
        } else {
            a = a + "&" + window._ajaxlist_parameter + "=1";
        }
    }
    return a;
}
function hashUrl(a) {
    r = a.match(/\?(.+)$/);
    if (!r) {
        return "#";
    }
    var b = RegExp.$1;
    if (b.indexOf(window._ajaxlist_parameter + "=1") >= 0) {
        b = b.replace(window._ajaxlist_parameter + "=1&", "");
        b = b.replace("&" + window._ajaxlist_parameter + "=1", "");
        b = b.replace(window._ajaxlist_parameter + "=1", "");
    }
    return b;
}
jQuery(document).ready(function() {
    /*function setCartDeleteText(a) {
     window.cartDeleteText = a;
     }*/
    setReload_nav(true);
    setReload_compare(true);
    var m = location.hash.slice(1);
    if (m !== "") {
        handleHashChange();
    }
    jQuery(window).hashchange(function(e) {
        handleHashChange();
    });
    /* remove pager selects click event */
    jQuery(".pager select, .toolbar select").prop("onchange", null).attr("onchange", null);
    window.StartSlider = function() {
        min_price = 0;
        max_price = parseInt(jQuery("input#price_maximum").val());
        step_val = jQuery("input#step_value").val();
        step_val = parseInt(step_val);
        jQuery("#slider-range-price").slider({
            range: true,
            min: 0,
            max: max_price,
            step: step_val,
            values: [jQuery("#init_price_minimum").val(), jQuery("#init_price_maximum").val()],
            slide: function(a, b) {
                jQuery("input#price_maximum").val(b.values[ 1 ]);
                jQuery("input#price_minimum").val(b.values[ 0 ]);
            },
            change: function(a, b) {
                jQuery("input#price_maximum").val(b.values[ 1 ]);
                jQuery("input#price_minimum").val(b.values[ 0 ]);
                new_url = ajaxListURL(jQuery("#price_slider_url").val()) + "&price=" + jQuery("#slider-range-price").slider("values", 0) + "-" + jQuery("#slider-range-price").slider("values", 1);
                setReload_nav(true);
                setReload_compare(false);
                window.location.hash = hashUrl(new_url);
            }
        });
        jQuery("input#price_maximum").val(jQuery("#slider-range-price").slider("values", 1));
        jQuery("input#price_minimum").val(jQuery("#slider-range-price").slider("values", 0));
    };
    window.StartSlider();
    jQuery(document).on('change', ".pager select, .toolbar select", function() {
        setReload_nav(false);
        setReload_compare(false);
        window.location.hash = hashUrl(jQuery(this).val());
        return false;
    });
    jQuery(document).on('click', ".pager a, .toolbar a", function() {
        setReload_nav(false);
        setReload_compare(false);
        window.location.hash = hashUrl(jQuery(this).attr("href"));
        return false;
    });
    jQuery(document).on("click", ".block-layered-nav #narrow-by-list ol.layered-links a, #horizontal-layered-navigation-container ol.layered-links a, .block-layered-nav .currently a, .block-layered-nav .actions a, ol.layered-links-category li.child > a, #horizontal-layered-navigation-status li a", function(e) {
        e.preventDefault();
        setReload_nav(true);
        setReload_compare(true);
        window.location.hash = hashUrl(jQuery(this).attr("href"));
        return false;
    });
    jQuery(document).on("click", "button#price-filter-button", function(e) {
        e.preventDefault();
        step_val = parseInt(jQuery("input#step_value").val());
        request_price_min = Math.floor(jQuery("#price_minimum").val() / step_val) * step_val;
        request_price_max = Math.ceil(jQuery("#price_maximum").val() / step_val) * step_val;
        new_url = ajaxListURL(jQuery("#price_slider_url").val()) + "&price=" + request_price_min + "-" + request_price_max;
        window.location.hash = hashUrl(new_url);
    });
    /* ajax compare feature... */
    if (window._ajax_compare) {
        jQuery(document).on('click', window._product_list_element + ' a[href*="catalog/product_compare/add"]', function(e) {
            e.preventDefault();
            loadAjaxCompare(jQuery(this).attr("href"), false, true);
            return false;
        });
        jQuery(document).on('click', 'a[href*="catalog/product_compare/remove"]', function(e) {
            e.preventDefault();
            loadAjaxCompare(jQuery(this).attr("href"), false, true);
            return false;
        });
        jQuery(document).on('click', 'a[href*="catalog/product_compare/clear"]', function(e) {
            e.preventDefault();
            loadAjaxCompare(jQuery(this).attr("href"), false, true);
            return false;
        });
    }
    function loadAjaxCompare(e, f, g, h) {
        e = ajaxListURL(e);
        jQuery("ul.messages").remove();
        jQuery(window._product_list_element).append("<div class=\"products-list-loader\"><div></div></div>");
        jQuery.ajax(e,
                {data: {},
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-Requested-With', {toString: function() {
                                return '';
                            }});
                    },
                    success: function(a, b, c) {
                        if (b === "error") {
                            jQuery(window._product_list_element).prepend("<p>There was an error making the AJAX request</p>");
                        } else {
                            jQuery(window._product_list_element + " div.products-list-loader").remove();
                            if (a.ajaxcompare_reload_block){
                                jQuery.each(a.ajaxcompare_reload_block, function(i,val){
                                    console.debug("#ajaxcompare-reload-block-" + i);
                                    jQuery("#ajaxcompare-reload-block-" + i).replaceWith(val);
                                });
                            }
                            if (a.message)
                                jQuery(window._product_list_element).prepend(a.message);
                            /*jQuery('html, body').animate({
                                scrollTop: jQuery(window._product_list_element).offset().top
                            }, 500);*/
                        }
                    }
                });
    }
    jQuery(document).on("keyup", "#price_minimum, #price_maximum", function(a) {
        if (a.keyCode === 13) {
            jQuery("button#price-filter-button").click();
        }
    });
    function handleHashChange() {
        var a = location.hash.slice(1);
        path = window.location.href;
        path = path.split("#")[0];
        tmp = path;
        path = path.split("?")[0];
        path = ajaxListURL(path + "?" + a);
        nv = getReload_nav();
        cm = getReload_compare();
        loadAjaxProductsList(path, nv, cm, false);
        setReload_nav(true);
        setReload_compare(false);
        if (a === "" && typeof (window.history.pushState) === 'function') {/* remove hash symbol when empty parameters */
            window.history.pushState({}, '', tmp);
        }
    }
    function loadAjaxProductsList(e, f, g, h) {
        e = ajaxListURL(e);
        jQuery(window._product_list_element).append("<div class=\"products-list-loader\"><div></div></div>");
        if (f) {
            jQuery("#layered-navigation-container .block-layered-nav").append("<div class=\"products-list-loader\"><div></div></div>");
        }
        jQuery(document).trigger("beforeAjaxProductsLoaded");
        jQuery.ajax(e,
                {data: {},
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-Requested-With', {toString: function() {
                                return '';
                            }});
                    },
                    success: function(a, b, c) {
                        if (b === "error") {
                            jQuery(window._product_list_element).html("<p>There was an error making the AJAX request</p>");
                        } else {
                            var d = jQuery("<div />").html(a);
                            if (d.find(window._product_list_element).length > 0) {
                                jQuery(window._product_list_element).html(d.find(window._product_list_element).html());
                            } else if (d.find(".note-msg").length > 0) {/* 0 results  */
                                jQuery(window._product_list_element).html(d.find('.note-msg'));
                            }
                            if (f) {
                                jQuery("#layered-navigation-container").html(d.find('#layered-navigation-container').html());
                                window.StartSlider();
                                expandCategoriesFilter();
                                window._horizontal_layered_navigation_contents = ""; /* will reload the horizontal layered navigation */
                            }
                            if (g) {
                                jQuery(".block-compare").html(d.find('.block-compare').html());
                            }
                            /*if (h) {
                             jQuery(".block-cart").html(d.find('.block-cart').html());
                             }*/
                            /* remove pager selects click event */
                            jQuery(".pager select, .toolbar select").prop("onchange", null).attr("onchange", null);
                            
                            if (window._horizontal_layered_navigation) {/* create horizontal layered navigation... */
                                createHorizontalLayeredNavigation();
                            }
                            /* create event triggers / observers .. */
                            /*  how to use: 
                             * ADD CODE TO ANOTHER FILE:
                             * jQuery( document ).on( "afterAjaxProductsLoaded", function(  ) {
                             *      CUSTOM CODE HERE....
                             * })
                             */
                            jQuery(document).trigger("afterAjaxProductsLoaded");
                            /* enable this file in the extension configuration and the edit the afterReload.js file */
                            if (typeof window.afterAjaxReload === 'function') {
                                window.afterAjaxReload();
                            }
                            /*jQuery('html, body').animate({
                                scrollTop: jQuery(window._product_list_element).offset().top
                            }, 500);*/
                            
                        }
                    }
                });
    }
    /* new function to open close parents */
    jQuery(document).on("click", "ol#category-filters li.parent > a", function(e) {
        e.preventDefault();
        _children = jQuery(this).closest("li").find(" > ul");
        if (jQuery(this).hasClass("category-collapse")) {
            _children.hide();
            jQuery(this).addClass("category-expand").removeClass("category-collapse");
        } else {
            _children.show();
            jQuery(this).removeClass("category-expand").addClass("category-collapse");
        }
        return false;
    });
    /* expand items when loading page */
    function expandCategoriesFilter() {
        jQuery("ol#category-filters li.active-filter-option").parentsUntil('#category-filters', "ul").show();
        jQuery("ol#category-filters li.active-filter-option").parentsUntil('#category-filters', "li").find("> a").removeClass("category-expand").addClass("category-collapse");
    }
    expandCategoriesFilter();
    /* expand collapse filters based on filters selection */
    if (window._collapse) {/* if setting is enabled.. */
        jQuery(document).on("click", "#narrow-by-list div.filter-title", function() {
            jQuery(this).next('div.filter-content').toggleClass('filter-content-expanded filter-content-collapsed');
            jQuery(this).toggleClass('filter-title-expanded filter-title-collapsed');
        });

    }
    /* horizontal layered navigation */
    if (window._horizontal_layered_navigation) {
        function createHorizontalLayeredNavigation( ) {
            /* first, create container div */
            jQuery("div.category-products div.toolbar").first().before("<div id='horizontal-layered-navigation-container'></div>");
            /* get list of filters to display horizontally */
            /* place them in the container... */
            if (window._horizontal_layered_navigation_contents) {
                jQuery("div#horizontal-layered-navigation-container").html(window._horizontal_layered_navigation_contents);
            } else {
                jQuery("#narrow-by-list div.horizontal-filter").appendTo("div#horizontal-layered-navigation-container");
            }
            /* add z-index for dropdowns... */
            _z_index = 10000;
            jQuery("div#horizontal-layered-navigation-container div.horizontal-filter").each(function(i) {
                jQuery(this).css("z-index", _z_index - i);
            });
            /* get the selected / active items */
            _active_links = jQuery("div#horizontal-layered-navigation-container ol.active-layer li.active-filter-option").not('.no-items').clone();
            if (jQuery(_active_links).length > 0) {
                jQuery("div#horizontal-layered-navigation-container").before("<ol id='horizontal-layered-navigation-status'></ol>");
                jQuery("ol#horizontal-layered-navigation-status").append(_active_links);
            }
            /* keep the html, when the pages are reloaded with ajax, replace the layered navigation... */
            window._horizontal_layered_navigation_contents = jQuery("div#horizontal-layered-navigation-container").html();
        }
        createHorizontalLayeredNavigation();
    }
    jQuery(document).on("click", "#horizontal-layered-navigation-container div.horizontal-filter .filter-title", function(e) {
        e.preventDefault();
        _is_expanded = jQuery(this).hasClass("horizontal-filter-title-expanded");
        jQuery("#horizontal-layered-navigation-container div.horizontal-filter .filter-title.horizontal-filter-title-expanded, #horizontal-layered-navigation-container div.horizontal-filter .filter-content.horizontal-filter-content-expanded").removeClass('horizontal-filter-title-expanded horizontal-filter-content-expanded');
        if (!_is_expanded) {
            jQuery(this).next('div.filter-content').addClass('horizontal-filter-content-expanded');
            jQuery(this).addClass('horizontal-filter-title-expanded');
        }
        window._open_horizontal_dropdown = true;
    });
    jQuery(document).on("click", ".horizontal-filter-content-expanded, .filter-price-content", function(e) {
        e.stopPropagation();/* do not close the dropdowns... */
    });
    /* close all dropdowns */
    jQuery(document).on("click", function(e) {
        if (!window._open_horizontal_dropdown) {
            jQuery("#horizontal-layered-navigation-container div.horizontal-filter .filter-title.horizontal-filter-title-expanded, #horizontal-layered-navigation-container div.horizontal-filter .filter-content.horizontal-filter-content-expanded").removeClass('horizontal-filter-title-expanded horizontal-filter-content-expanded');
        } else {
            window._open_horizontal_dropdown = false;
        }
    });
    /* added new function for left side navigation */
    /*jQuery("#sidebar-nav.Left li").not(".home").find("a").on("click",function(e){
     e.preventDefault();
     _url = jQuery(this).attr("href");
     // Assuming the path is retreived and stored in a variable 'path'
     if (_url ){
     
     if (typeof(window.history.pushState) === 'function') {
     window.history.pushState({}, '', _url);
     } else {
     //window.location.hash = '#!' + path;
     }
     ajaxListURL( _url );
     loadAjaxProductsList(_url);
     }
     return;
     });*/
});
