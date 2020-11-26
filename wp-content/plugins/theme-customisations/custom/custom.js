jQuery(document).ready(function ($) {

  /* global customJs */

  const href = window.location.href;

  if ((href.indexOf("filter_") >= 0) || href.indexOf("shuffle") >= 0) {

    $([document.documentElement, document.body]).animate({
      scrollTop: $("#primary").offset().top
    }, "fast", "linear");
  }

  //
  // HACK: [-2-] Close keyboard when starting to scroll search results

  $(".swpparentel").on("touchstart", function () {
    $('.site-search .search-field').blur();
  });

  //
  // HACK: [-1-] Toggle keyboard on search toggle

  if ($(window).width() < 768) {
    const sUsrAg = navigator.userAgent;

    if (/android/i.test(sUsrAg)) {
      $("#mobile-search-toggle").change(function () {
        if (this.checked) {
          $(".site-search .search-field").focus();
        } else {
          $(".site-search .search-field").blur().val('');
        }
      });
    }
  }

  $("#desktop-search-toggle").change(function () {
    if (this.checked) {
      $(".site-search .search-field").focus();
    } else {
      $(".site-search .search-field").blur().val('');
    }
  });

  //
  // HACK: [-2-] Scroll to top of tabs when tab is clicked

  $(".woocommerce-Tabs-panel--useful_links").addClass('active');

  function scrollToTabs() {
    //if ($(window).width() < 768) {
    $(".woocommerce-tabs ul.tabs > li > a").click(function () {
      const activeTabIdSelector = $(this).attr('href');
      const activeTab = $(activeTabIdSelector);

      $(".wc-tab, .panel").not(".panel .panel").hide().removeClass('active');
      activeTab.show().addClass('active');

      const activeTabOffsetTop = activeTab.offset().top;

      $("html, body").stop().animate({ scrollTop: activeTabOffsetTop }, 500, function () {
        window.location.hash = '#tab-all_links';
        $("#tab-all_links").focus();
      });
    });
    //}
  }

  setTimeout(scrollToTabs, 2000);

  //
  // HACK: [-2-] Open external links in new tab

  $("a[href*='http']").not("[href*='simply-hobbies']").attr('target', '_blank').attr('rel', 'noopener');
  $("form[action*='http']").not("[action*='simply-hobbies']").attr('target', '_blank');

  //
  // HACK: [-1-] Whitelist search fields for Hotjar

  $(".search-field").addClass("data-hj-whitelist");

  // HACK: [-2-] Display unloader when filter is clicked

  $(document.body).on(
    "click",
    ".woocommerce-widget-layered-nav-list__item a",
    function () {
      $('body').addClass("unloading");
      $('.site').removeClass("animated fadeIn");
    }
  );

  // HACK: [-2-] Change sticky add to cart to bookmark-link, if there is more then one 'Get It' link

  if ($('#get-essential-links').length) {
    $('.storefront-sticky-add-to-cart__content-button')
      .attr("href", "#get-essential-links")
      .removeAttr('target')
      .html('Get It');
  }

  // HACK: [-1-] Toggle sub-menu on parent menu item click 

  $(document.body).on(
    "click",
    ".handheld-navigation ul.menu li.menu-item-has-children > a",
    function () {
      $(this).siblings('.dropdown-toggle').click();
    }
  );

  // HACK: [-2-] Enable Web Share

  function webShare() {
    navigator.share({
      url: window.location.href,
    })
      .then(() => console.log('Successful share'))
      .catch((error) => console.log('Error sharing', error));
  }

  if (navigator.share) {
    $(document.body).on(
      "click",
      ".web-share",
      webShare
    );

    $(".a2a_dd").replaceWith("<a class='web-share'><span class='material-icons'>share</span>Share</a>");

    $(document.body).on('post-load', function () { // NOTE: For infinite scroll
      $(".scroll-end-cta .a2a_dd").replaceWith("<a class='web-share'><span class='material-icons'>share</span>Share</a>");
    });
  }

  // HACK: [-2-] Add tabindex to tabs list to enable bookmark link

  $('ul.tabs').attr('id', "tabs").attr('tabindex', '-1');

  // HACK: [-2-] Product expert notification subscription

  $(document.body).on(
    "click",
    ".subscribe-product-expert a",
    function (e) {
      e.preventDefault();

      $(this).closest('.woocommerce-info').addClass('sh_ajax-loading');

      var pathname = window.location.pathname;
      pathname = pathname.replace(/\/$/, ''); // remove trailing slash
      productSlug = pathname.split('/').pop();

      $.ajax({
        type: "post",
        dataType: "json",
        url: customJs.ajaxurl,
        data: { action: "subscribe_product_expert", product_slug: productSlug },
      })
        .done(function () {
          $(".subscribe-product-expert").replaceWith("<span class='unsubscribe-product-expert'>Successfully subscribed. <a href>Cancel</a></span>");

          if (typeof dataLayer !== 'undefined') {
            dataLayer.push({ 'event': 'questions_subscribe' });
          }
        })
        .fail(function () {
          $(".unsubscribe-product-expert").replaceWith("<span>Something went wrong. Please contact us or try again later.</span>");
        })
        .always(function () {
          $('.woocommerce-info.sh_ajax-loading').removeClass('sh_ajax-loading');
        });
    }
  );

  $(document.body).on(
    "click",
    ".unsubscribe-product-expert a",
    function (e) {
      e.preventDefault();

      $(this).closest('.woocommerce-info').addClass('sh_ajax-loading');

      const pathname = window.location.pathname;
      pathname = pathname.replace(/\/$/, ''); // remove trailing slash
      const productSlug = pathname.split('/').pop();

      $.ajax({
        type: "post",
        dataType: "json",
        url: customJs.ajaxurl,
        data: { action: "unsubscribe_product_expert", product_slug: productSlug },
      })
        .done(function () {
          $(".unsubscribe-product-expert").replaceWith("<span class='subscribe-product-expert'>Successfully unsubscribed. <a href>Cancel</a></span>");
        })
        .fail(function () {
          $(".unsubscribe-product-expert").replaceWith("<span>Something went wrong. Please contact us or try again later.</span>");
        })
        .always(function () {
          $('.woocommerce-info.sh_ajax-loading').removeClass('sh_ajax-loading');
        });
    }
  );

  // HACK: [-2-] Helpful vote

  $(document).on('click', '.helpful_count_badge', function (e) {
    e.preventDefault();
  });

  $(document).on('click contextmenu', '.content_type_list_item > a, .product_tag-essentials > a, .hover-links > a, .app-stores-badges-container > a', function () {
    initializeLinkRating($(this));
  });

  $(document.body).on(
    "click",
    ".helpful-vote",
    function (e) {
      e.preventDefault();

      if ($(this).hasClass('vote_selected')) {
        return;
      }

      $(this).addClass('vote_selected');
      $(this).siblings().removeClass('vote_selected');

      $(this).closest('.link-rating').addClass('voted');

      var listItem = $(this).closest('li');

      if (listItem.hasClass('product_tag-essentials')) {
        var classes = listItem.attr("class");
        var regex = /post-([0-9]*)/;
        var match = classes.match(regex);

        var productId = match[1];
      } else {
        var productId = listItem.data('product_id');
      }

      var helpfulVoteMetaKey = listItem.data('helpful_vote_meta_key');

      var voteType = $(this).html();

      var isEssential = false;

      if (listItem.hasClass('product_tag-essentials') || listItem.hasClass('essentials')) {
        isEssential = true;
      }

      $.ajax({
        type: "post",
        dataType: "json",
        url: customJs.ajaxurl,
        data: { action: "helpful_vote", vote_type: voteType, product_id: productId, helpful_vote_meta_key: helpfulVoteMetaKey, is_essential: isEssential },
      })
        .done(function (response) {
          var updated = response.updated;

          if (updated) {
            var cookieName = response.cookieName;
            var cookieValue = response.cookieValue;

            document.cookie = cookieName + "=" + cookieValue + "; expires=Fri, 31 Dec 2100 23:59:59 GMT; path=/";

            if (typeof dataLayer !== 'undefined') {
              dataLayer.push({ 'event': 'helpful_vote' });
            }
          }
        })
        .fail(function () {
        })
        .always(function () {
        });
    }
  );

  function initializeLinkRating(clickedElement) {
    var listItem = clickedElement.closest('li');

    if (listItem.find('.link-rating').length) {
      return;
    }

    var linkRatingHtml = '<div class="link-rating close-target"><span class="text">Was the link helpful?</span><div class="thumbs-container"><span class="material-icons-outlined helpful-vote yes">thumb_up</span><span class="material-icons-outlined helpful-vote no">thumb_down</span></div><div class="wrong-link-wrapper">Report wrong link <span class="material-icons">link_off</span></div><span class="material-icons close">close</span></div>';

    listItem.addClass('visited');

    listItem.find('a').prop('title', '');

    listItem.append(linkRatingHtml);

    if (listItem.hasClass('product_tag-essentials')) {
      var classes = listItem.attr("class");
      var regex = /post-([0-9]*)/;
      var match = classes.match(regex);

      var productId = match[1];

      var helpfulVoteMetaKey = 'helpful';
    } else {
      var productId = listItem.data('product_id');

      var helpfulVoteMetaKey = listItem.data('helpful_vote_meta_key');
    }

    var cookieName = "sh_" + productId + "_" + helpfulVoteMetaKey;

    var voted = getCookie(cookieName);

    if (voted) {
      var voteSelected = listItem.find('.helpful-vote.' + voted);

      voteSelected.addClass('vote_selected');
      voteSelected.siblings().removeClass('vote_selected');

      voteSelected.closest('.link-rating').addClass('voted');
    }
  }

  //
  // HACK: [-2-] Change hobby's essentials bookmark on mobile

  if ($(window).width() > 768) {
    $('a[href="#bottom-hobbys-essentials"').attr("href", "#hobbys-essentials")
  }

  //
  // HACK: [-0-]

  $(document.body).on(
    "click",
    ".wrong-link-wrapper",
    function (e) {
      e.preventDefault();

      $(this).html('Thanks!');

      var listItem = $(this).closest('li');

      if (listItem.find('.woocommerce-loop-product__link').length) {
        var linkHref = listItem.find('.woocommerce-loop-product__link').attr('href');
      } else {
        var linkHref = listItem.find('.link').attr('href');
      }

      if (listItem.hasClass('product_tag-essentials')) {
        var classes = listItem.attr("class");
        var regex = /post-([0-9]*)/;
        var match = classes.match(regex);

        var productId = match[1];
      } else {
        var productId = listItem.data('product_id');
      }

      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: customJs.ajaxurl,
        data: { action: "wrong_link", link_href: linkHref, product_id: productId },
      })
        .done(function (response) {
        })
        .fail(function () {
        })
        .always(function () {
        });
    }
  );


  //
  // HACK: [-2-] Add live search start event

  $(document).on('searchwp_live_search_start', function () {
    if (typeof dataLayer !== 'undefined') {
      dataLayer.push({ 'event': 'live_search_start' });
    }
  });

  //
  // HACK: [-2-] Show and scroll to useful links tab on handheld bar click

  $(document).on('click', '.storefront-handheld-footer-bar a[href="#tab-useful_links"]', function () {
    $('.useful_links_tab a').click();
  });

  //
  // HACK: [-2-] Remove irrelevant apps store link

  if ($(window).width() < 768) {
    const sUsrAg = navigator.userAgent;

    if (/android/i.test(sUsrAg)) {
      $('.hover-links > a[href*="https://apps.apple.com/"]').remove();
      $('.app-stores-badges-container a[href*="https://apps.apple.com/"]').remove();
    }

    if (/iPad|iPhone|iPod/.test(sUsrAg) && !window.MSStream) {
      $('.hover-links > a[href*="https://play.google.com/store/apps"]').remove();
      $('.app-stores-badges-container a[href*="https://play.google.com/store/apps"]').remove();
    }
  }

  //
  // HACK: [-2-] Description inline-podcast-toggle

  $(document.body).on(
    "click",
    ".site_description .inline-podcast-toggle",
    function (e) {
      e.preventDefault();

      $(this).closest('li').find('.podcast-toggle-label').click();
      $(this).closest('li').find('a').blur();
    }
  );

  //
  // HACK: [-2-] Description hide button

  // NOTE: Only show site description hide button below description is the toggle is available
  if ($(".site_description_toggle-input").length) {
    $(".site_description .site_description-hide").css("display", "inline-flex");
  }

  $(document.body).on(
    "click",
    ".site_description .site_description-hide",
    function (e) {
      e.preventDefault();

      $(this).closest('li').find('.site_description_toggle-input').prop('checked', false);
      $(this).closest('li').find('a').blur();

      initializeLinkRating($(this));
    }
  );

  //
  // HACK: [-2-] Hide other opened description

  $(document.body).on(
    "change",
    ".site_description_toggle-input",
    function () {
      $(".site_description_toggle-input").not(this).prop("checked", false);
    }
  );

  //
  // HACK: [-1-] Toggle audio on toggle-podcast click

  $(document.body).on(
    "change",
    ".podcast-toggle-input",
    function (e) {
      var audioDomElement = $(this).closest('li').find('audio')[0];

      if (this.checked) {
        audioDomElement.play();

        initializeLinkRating($(this));
      } else {
        audioDomElement.pause();
      }

      // NOTE: Close description on podcast toggle
      $(".site_description_toggle-input").prop("checked", false);
    }
  );

  //
  // HACK: [-2-] Toggle 'playing' class when on audio toggle

  $("li.sh_podcasts audio").on("play", function () {
    $(this).closest('li').addClass("playing");
    $(this).closest('li').find('.podcast-toggle-input').prop('checked', true);

    // NOTE: Track podcast_start event
    if (typeof dataLayer !== 'undefined') {
      dataLayer.push({ 'event': 'podcast_start' });
    }
  });

  $("li.sh_podcasts audio").on("ended pause", function () {
    $(this).closest('li').removeClass("playing");
  });

  //
  // HACK: [-2-] Description glow

  $(document.body).one(
    "click mouseenter",
    ".site_description_toggle-label",
    function (e) {
      $(".single-product").removeClass("description-glow");

      document.cookie = "sh_description-glow=off;path=/";
    }
  );

  var descriptionGlowCookie = getCookie("sh_description-glow");

  if (descriptionGlowCookie !== "off") {
    $(".single-product").addClass("description-glow");
  }

  //
  // HACK: [-1-] Track first- and last-batch scroll

  $(document.body).on('post-load', function (event, response) {
    if (response['lastbatch']) {
      if (typeof dataLayer !== 'undefined') {
        dataLayer.push({ 'event': 'lastbatch_scroll' });
      }
    }
  });

  $(document.body).one('post-load', function (event, response) {
    if (typeof dataLayer !== 'undefined') {
      dataLayer.push({ 'event': 'firstbatch_scroll' });
    }
  });

  //
  // HACK: [-2-] Load deferred content

  function loadDeferredContent() {
    var deferredFeaturedVideo = $("iframe[id*='ywcfav_video'][data-src]").first();

    deferredFeaturedVideo.attr('src', deferredFeaturedVideo.attr('data-src'));
  }

  window.onload = loadDeferredContent;

  $(document.body).one(
    "click touchstart",
    ".yith_featured_thumbnail",
    function () {
      $(".woocommerce-product-gallery").find("iframe[id*='ywcfav_video'][src='']").each(function () {
        $(this).attr('src', $(this).attr('data-src'));
      });
    }
  );

  $(document.body).one(
    "click",
    ".videos_tab",
    function () {
      $(".woocommerce-Tabs-panel--videos").find("iframe[src='']").each(function () {
        $(this).attr('src', $(this).attr('data-src'));
      });
    }
  );

  //
  // HACK: [-2-] Get cookie

  function getCookie(cookieName) {
    var name = cookieName + "=";
    var decodedCookies = decodeURIComponent(document.cookie);
    var cookiesArray = decodedCookies.split(';');

    for (var i = 0; i < cookiesArray.length; i++) {
      var cookie = cookiesArray[i];

      while (cookie.charAt(0) == ' ') {
        cookie = cookie.substring(1);
      }

      if (cookie.indexOf(name) == 0) {
        return cookie.substring(name.length, cookie.length);
      }
    }

    return;
  }

  //
  // HACK: [-2-] Close close-targets

  $(document).on('click', '.close', function () {
    $(this).closest('.close-target').remove();
  });
});
