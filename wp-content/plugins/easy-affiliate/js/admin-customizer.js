(function ($) {
  var $brand_color_style,
    $accent_color_style,
    $menu_text_color_style,
    $menu_text_highlight_color_style;

  wp.customize('esaf_pro_dashboard_brand_color', function (value) {
    value.bind(function (newval) {
      if(!$brand_color_style) {
        $brand_color_style = $('<style>').appendTo('head');
      }

      $brand_color_style.text(EsafAdminCustomizerL10n.brand_color_css.replace(/%1\$s/g, newval));
    });
  });

  wp.customize('esaf_pro_dashboard_accent_color', function (value) {
    value.bind(function (newval) {
      if(!$accent_color_style) {
        $accent_color_style = $('<style>').appendTo('head');
      }

      $accent_color_style.text(EsafAdminCustomizerL10n.accent_color_css.replace(/%1\$s/g, newval));
    });
  });

  wp.customize('esaf_pro_dashboard_menu_text_color', function (value) {
    value.bind(function (newval) {
      if(!$menu_text_color_style) {
        $menu_text_color_style = $('<style>').appendTo('head');
      }

      $menu_text_color_style.text(EsafAdminCustomizerL10n.menu_text_color_css.replace(/%1\$s/g, newval));

      var menu_text_highlight_setting = wp.customize('esaf_pro_dashboard_menu_text_highlight_color');

      if(menu_text_highlight_setting) {
        var menu_text_highlight_color = menu_text_highlight_setting.get();

        if(menu_text_highlight_color) {
          set_menu_text_highlight_color(menu_text_highlight_color);
        }
      }
    });
  });

  wp.customize('esaf_pro_dashboard_menu_text_highlight_color', function (value) {
    value.bind(set_menu_text_highlight_color);
  });

  function set_menu_text_highlight_color(value) {
    if(!$menu_text_highlight_color_style) {
      $menu_text_highlight_color_style = $('<style>').appendTo('head');
    }

    $menu_text_highlight_color_style.text(EsafAdminCustomizerL10n.menu_text_highlight_color_css.replace(/%1\$s/g, value));
  }
})(jQuery);
