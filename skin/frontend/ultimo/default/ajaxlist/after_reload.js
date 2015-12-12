/* USE THIS FUNCTION TO EXECUTE SCRIPTS AFTER THE AJAX LOAD
 * IN THIS CASE, WE RECREATE THE EXPAND /  COLLAPSE PATTERN USED IN THE RWD THEME
 * BECAUSE IT IS IMPLEMENTED WITHOUT USING A LIVE FUNCTION SO IT NEEDS TO BE RECREATED EACH TIME THE AJAX IS LOADED
 */



function afterAjaxReload() {
    /* do something... */
    var loaded = 0;
    jQuery('#ajaxlist-reload-product_list li.item a.product-image img').on('load', function() {
        loaded++;
        //console.debug(loaded);
        if (loaded === jQuery('#ajaxlist-reload-product_list li.item a.product-image img').length) {
            //console.debug("will resize...")
            /* after all images are loaded.... */
            window.setInterval(setGridItemsEqualHeight(jQuery), 2000);
        }
    });
    
    // ==============================================
    // Basic variables
    // ==============================================

    var breakpointMedium = 768;
    var isResponsive = jQuery('body').hasClass('responsive');

    
    
    // ==============================================
    // Layered Navigation Block
    // ==============================================

    // On product list pages, we want to show the layered nav/category menu immediately above the product list
    if (isResponsive)
    {
        if (jQuery('.block-layered-nav').length && jQuery('.category-products').length)
        {
            enquire.register('screen and (max-width: ' + (breakpointMedium - 1) + 'px)', {
                match: function() {
                    jQuery('.block-layered-nav').insertBefore(jQuery('.category-products'))
                },
                unmatch: function() {
                    // Move layered nav back to left column
                    jQuery('.block-layered-nav').insertAfter(jQuery('#layered-navigation-container'))
                }
            });
        }
    }
    
}


/*portion taken from skin/frontend/ultimo/default/js/app.js*/

function collapsibleLayeredNavigation() {


    // ==============================================
    // Basic variables
    // ==============================================

    var breakpointMedium = 768;
    var isResponsive = jQuery('body').hasClass('responsive');

    // ==============================================
    // UI Pattern - ToggleSingle
    // ==============================================

    // Use this plugin to toggle the visibility of content based on a toggle link/element.
    // This pattern differs from the accordion functionality in the Toggle pattern in that each toggle group acts
    // independently of the others. It is named so as not to be confused with the Toggle pattern below
    //
    // This plugin requires a specific markup structure. The plugin expects a set of elements that it
    // will use as the toggle link. It then hides all immediately following siblings and toggles the sibling's
    // visibility when the toggle link is clicked.
    //
    // Example markup:
    // <div class="block">
    //     <div class="block-title">Trigger</div>
    //     <div class="block-content">Content that should show when </div>
    // </div>
    //
    // JS: jQuery('.block-title').toggleSingle();
    //
    // Options:
    //     destruct: defaults to false, but if true, the plugin will remove itself, display content, and remove event handlers

    jQuery.fn.toggleSingle = function(options) {

        // passing destruct: true allows
        var settings = jQuery.extend({
            destruct: false
        }, options);

        return this.each(function() {
            if (!settings.destruct) {
                jQuery(this).on('click', function() {
                    jQuery(this)
                            .toggleClass('active')
                            .next()
                            .toggleClass('no-display');
                });
                // Hide the content
                $this = jQuery(this);
                if (!$this.hasClass('active'))
                {
                    $this.next().addClass('no-display');
                }
                //jQuery(this).next().addClass('no-display');
            } else {
                // Remove event handler so that the toggle link can no longer be used
                jQuery(this).off('click');
                // Remove all classes that were added by this plugin
                jQuery(this)
                        .removeClass('active')
                        .next()
                        .removeClass('no-display');
            }

        });
    }

    // ==============================================
    // UI Pattern - Toggle Content (tabs and accordions in one setup)
    // ==============================================

    jQuery('.toggle-content').each(function() {
        var wrapper = jQuery(this);

        var hasTabs = wrapper.hasClass('tabs');
        var hasAccordion = wrapper.hasClass('accordion');
        var startOpen = wrapper.hasClass('open');

        var dl = wrapper.children('dl:first');
        var dts = dl.children('dt');
        var panes = dl.children('dd');
        var groups = new Array(dts, panes);

        //Create a ul for tabs if necessary.
        if (hasTabs) {
            var ul = jQuery('<ul class="toggle-tabs"></ul>');
            dts.each(function() {
                var dt = jQuery(this);
                var li = jQuery('<li></li>');
                li.html(dt.html());
                ul.append(li);
            });
            ul.insertBefore(dl);
            var lis = ul.children();
            groups.push(lis);
        }

        //Add "last" classes.
        var i;
        for (i = 0; i < groups.length; i++) {
            groups[i].filter(':last').addClass('last');
        }

        function toggleClasses(clickedItem, group) {
            var index = group.index(clickedItem);
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].removeClass('current');
                groups[i].eq(index).addClass('current');
            }
        }

        //Toggle on tab (dt) click.
        dts.on('click', function(e) {
            //They clicked the current dt to close it. Restore the wrapper to unclicked state.
            if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                wrapper.removeClass('accordion-open');
            } else {
                //They're clicking something new. Reflect the explicit user interaction.
                wrapper.addClass('accordion-open');
            }
            toggleClasses(jQuery(this), dts);
        });

        //Toggle on tab (li) click.
        if (hasTabs) {
            lis.on('click', function(e) {
                toggleClasses(jQuery(this), lis);
            });
            //Open the first tab.
            lis.eq(0).trigger('click');
        }

        //Open the first accordion if desired.
        if (startOpen) {
            dts.eq(0).trigger('click');
        }

    });


    // ==============================================
    // Layered Navigation Block
    // ==============================================

    // On product list pages, we want to show the layered nav/category menu immediately above the product list
    if (isResponsive)
    {
        if (jQuery('.block-layered-nav').length && jQuery('.category-products').length)
        {
            enquire.register('screen and (max-width: ' + (breakpointMedium - 1) + 'px)', {
                match: function() {
                    jQuery('.block-layered-nav').insertBefore(jQuery('.category-products'))
                },
                unmatch: function() {
                    // Move layered nav back to left column
                    jQuery('.block-layered-nav').insertAfter(jQuery('#layered-nav-marker'))
                }
            });
        }
    }

    // ==============================================
    // Blocks collapsing (on smaller viewports)
    // ==============================================

    if (isResponsive)
    {
        enquire.register('(max-width: ' + (breakpointMedium - 1) + 'px)', {
            setup: function() {
                this.toggleElements = jQuery(
                        '.sidebar .block:not(.block-layered-nav) .block-title, ' +
                        '.block-layered-nav .block-subtitle--filter, ' +
                        //'.block-layered-nav .block-title, ' + //Currently this element is hidden in mobile view
                        '.mobile-collapsible .block-title'
                        );
            },
            match: function() {
                this.toggleElements.toggleSingle();
            },
            unmatch: function() {
                this.toggleElements.toggleSingle({destruct: true});
            }
        });
    }

    // ==============================================
    // Blocks collapsing on all viewports
    // ==============================================

    //Exclude elements with ".mobile-collapsible" for backward compatibility
    jQuery('.collapsible:not(.mobile-collapsible) .block-title').toggleSingle();


}