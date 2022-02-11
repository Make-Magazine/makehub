/**
 * application.js
 * -----------------------------------------------------
 * all the js magic we need!
 */

// on dom ready
jQuery(document).ready(function ($) {
  var mm_seo_tests = {};
  var keyword_set = false;
  var mm_seo_test_running = false;
  var seo_gage = false;

  // tabs
  var $state = $.cookie("csmm_menu");

  if ($state) {
    if ($state.indexOf("-") != -1) {
      state = $state.split("-");
      $(".csmm-main-menu li").removeClass("active");
      $(".csmm-main-menu li a").removeClass("active-secondary");
      $('a[href="' + state[0] + '"]')
        .parent("li")
        .addClass("active");
      $('a[href="' + $state + '"]').addClass("active-secondary");
      $($state).show();
    } else {
      $(".csmm-main-menu li").removeClass("active");
      $('a[href="' + $state + '"]')
        .parent("li")
        .addClass("active");
      $($state).show();
    }
  } else {
    $(".csmm-main-menu li:first").addClass("active");
    $(".csmm-tile:first").show();
  }

  // backup
  if ($(".csmm-main-menu li.active").length == 0) {
    $(".csmm-main-menu li:first").addClass("active");
    $(".csmm-tile:first").show();
  }
  $(".csmm-tile-first").hide();

  $(".csmm-main-menu li a").click(function (e) {
    e.preventDefault();

    var $selector = $(this);
    var $tab = $selector.attr("href");

    if ($selector.hasClass("parent-menu")) {
      $(this)
        .siblings(".csmm-submenu")
        .children("a:first-child")
        .trigger("click");
      return false;
    }

    $.removeCookie("csmm_menu", { path: "/" });

    $(".csmm-main-menu li").removeClass("active");
    $(".csmm-main-menu li a").removeClass("active-secondary");
    $selector.parents("li").addClass("active");
    $selector.addClass("active-secondary");

    $(".csmm-tile").hide();
    $($tab).show();
    $state = $tab;
    if ($tab == "#seo") {
      run_seo_tests();
    }

    if ($tab == "#dashboard") {
      create_csmm_chart();
    }
    $.cookie("csmm_menu", $tab, { path: "/" });
    //window.scrollTo(0, 0);
  });

  $(".csmm-mobile-menu a").click(function () {
    $(".csmm-main-menu").slideToggle();
  });
  // tabs

  keyboardJS.on(
    "ctrl + shift + s",
    function () {
      save_ajax();
    },
    function () {}
  );

  // auto remove notices
  window.setTimeout(function () {
    $(".csmm-alert").fadeOut();
  }, 1000 * 15);

  // overlay options
  $("#signals_csmm_overlay").change(function () {
    if ($(this).is(':checked')) {
      $(".overlay_parameters").fadeIn();
    } else {
      $(".overlay_parameters").fadeOut();
    }
  });
  $("#signals_csmm_overlay").change();

  var icm_icons = {
    "Social and Networking": [
      57694,
      57700,
      57701,
      57702,
      57703,
      57704,
      57705,
      57706,
      57707,
      57709,
      57710,
      57711,
      57717,
      57718,
      57719,
      57736,
      57737,
      57738,
      57739,
      57740,
      57741,
      57742,
      57746,
      57747,
      57748,
      57755,
      57756,
      57758,
      57759,
      57760,
      57761,
      57763,
      57764,
      57765,
      57766,
      57767,
      57776,
    ],
    "Web Applications": [
      57436,
      57437,
      57438,
      57439,
      57524,
      57525,
      57526,
      57527,
      57528,
      57531,
      57532,
      57533,
      57534,
      57535,
      57536,
      57537,
      57541,
      57545,
      57691,
      57692,
    ],
    "Business Icons": [
      57347,
      57348,
      57375,
      57376,
      57377,
      57379,
      57403,
      57406,
      57432,
      57433,
      57434,
      57435,
      57450,
      57453,
      57456,
      57458,
      57460,
      57461,
      57463,
    ],
    eCommerce: [57392, 57397, 57398, 57399, 57402],
    "Currency Icons": [],
    "Form Control Icons": [
      57383,
      57384,
      57385,
      57386,
      57387,
      57388,
      57484,
      57594,
      57595,
      57600,
      57603,
      57604,
      57659,
      57660,
      57693,
    ],
    "User Action & Text Editor": [
      57442,
      57443,
      57444,
      57445,
      57446,
      57447,
      57472,
      57473,
      57474,
      57475,
      57476,
      57477,
      57539,
      57662,
      57668,
      57669,
      57670,
      57671,
      57674,
      57675,
      57688,
      57689,
    ],
    "Charts and Codes": [57493],
    Attentive: [57543, 57588, 57590, 57591, 57592, 57593, 57596],
    "Multimedia Icons": [
      57356,
      57357,
      57362,
      57363,
      57448,
      57485,
      57547,
      57548,
      57549,
      57605,
      57606,
      57609,
      57610,
      57611,
      57614,
      57617,
      57618,
      57620,
      57621,
      57622,
      57623,
      57624,
      57625,
      57626,
    ],
    "Location and Contact": [
      57344,
      57345,
      57346,
      57404,
      57405,
      57408,
      57410,
      57411,
      57413,
      57414,
      57540,
    ],
    "Date and Time": [57415, 57416, 57417, 57421, 57422, 57423],
    Devices: [57359, 57361, 57364, 57425, 57426, 57430],
    Tools: [
      57349,
      57350,
      57352,
      57355,
      57365,
      57478,
      57479,
      57480,
      57481,
      57482,
      57483,
      57486,
      57487,
      57488,
      57663,
      57664,
    ],
    Brands: [
      57743,
      57750,
      57751,
      57752,
      57753,
      57754,
      57757,
      57773,
      57774,
      57775,
      57789,
      57790,
      57792,
      57793,
    ],
    "Files & Documents": [
      57378,
      57380,
      57381,
      57382,
      57390,
      57391,
      57778,
      57779,
      57780,
      57781,
      57782,
      57783,
      57784,
      57785,
      57786,
      57787,
    ],
    "Like & Dislike Icons": [
      57542,
      57544,
      57550,
      57551,
      57552,
      57553,
      57554,
      57555,
      57556,
      57557,
    ],
    Emoticons: [
      57558,
      57559,
      57560,
      57561,
      57562,
      57563,
      57564,
      57565,
      57566,
      57567,
      57568,
      57569,
      57570,
      57571,
      57572,
      57573,
      57574,
      57575,
      57576,
      57577,
      57578,
      57579,
      57580,
      57581,
      57582,
      57583,
    ],
    "Directional Icons": [
      57584,
      57585,
      57586,
      57587,
      57631,
      57632,
      57633,
      57634,
      57635,
      57636,
      57637,
      57638,
      57639,
      57640,
      57641,
      57642,
      57643,
      57644,
      57645,
      57646,
      57647,
      57648,
      57649,
      57650,
      57651,
      57652,
      57653,
      57654,
    ],
    "Other Icons": [
      57351,
      57353,
      57354,
      57358,
      57360,
      57366,
      57367,
      57368,
      57369,
      57370,
      57371,
      57372,
      57373,
      57374,
      57389,
      57393,
      57394,
      57395,
      57396,
      57400,
      57401,
      57407,
      57409,
      57412,
      57418,
      57419,
      57420,
      57424,
      57427,
      57428,
      57429,
      57431,
      57440,
      57441,
      57449,
      57451,
      57452,
      57454,
      57455,
      57457,
      57459,
      57462,
      57464,
      57465,
      57466,
      57467,
      57468,
      57469,
      57470,
      57471,
      57489,
      57490,
      57491,
      57492,
      57494,
      57495,
      57496,
      57497,
      57498,
      57499,
      57500,
      57501,
      57502,
      57503,
      57504,
      57505,
      57506,
      57507,
      57508,
      57509,
      57510,
      57511,
      57512,
      57513,
      57514,
      57515,
      57516,
      57517,
      57518,
      57519,
      57520,
      57521,
      57522,
      57523,
      57529,
      57530,
      57538,
      57546,
      57589,
      57597,
      57598,
      57599,
      57601,
      57602,
      57607,
      57608,
      57612,
      57613,
      57615,
      57616,
      57619,
      57627,
      57628,
      57629,
      57630,
      57655,
      57656,
      57657,
      57658,
      57661,
      57665,
      57666,
      57667,
      57672,
      57673,
      57676,
      57677,
      57678,
      57679,
      57680,
      57681,
      57682,
      57683,
      57684,
      57685,
      57686,
      57687,
      57690,
      57695,
      57696,
      57697,
      57698,
      57699,
      57708,
      57712,
      57713,
      57714,
      57715,
      57716,
      57720,
      57721,
      57722,
      57723,
      57724,
      57725,
      57726,
      57727,
      57728,
      57729,
      57730,
      57731,
      57732,
      57733,
      57734,
      57735,
      57744,
      57745,
      57749,
      57762,
      57768,
      57769,
      57770,
      57771,
      57772,
      57777,
      57788,
      57791,
      57794,
    ],
  };
  var icm_icon_search = {
    "Web Applications": [
      "Box add",
      "Box remove",
      "Download",
      "Upload",
      "List",
      "List 2",
      "Numbered list",
      "Menu",
      "Menu 2",
      "Cloud download",
      "Cloud upload",
      "Download 2",
      "Upload 2",
      "Download 3",
      "Upload 3",
      "Globe",
      "Attachment",
      "Bookmark",
      "Embed",
      "Code",
    ],
    "Business Icons": [
      "Office",
      "Newspaper",
      "Book",
      "Books",
      "Library",
      "Profile",
      "Support",
      "Address book",
      "Cabinet",
      "Drawer",
      "Drawer 2",
      "Drawer 3",
      "Bubble",
      "Bubble 2",
      "User",
      "User 2",
      "User 3",
      "User 4",
      "Busy",
    ],
    eCommerce: ["Tag", "Cart", "Cart 2", "Cart 3", "Calculate"],
    "Currency Icons": [],
    "Form Control Icons": [
      "Copy",
      "Copy 2",
      "Copy 3",
      "Paste",
      "Paste 2",
      "Paste 3",
      "Settings",
      "Cancel circle",
      "Checkmark circle",
      "Spell check",
      "Enter",
      "Exit",
      "Radio checked",
      "Radio unchecked",
      "Console",
    ],
    "User Action & Text Editor": [
      "Undo",
      "Redo",
      "Flip",
      "Flip 2",
      "Undo 2",
      "Redo 2",
      "Zoomin",
      "Zoomout",
      "Expand",
      "Contract",
      "Expand 2",
      "Contract 2",
      "Link",
      "Scissors",
      "Bold",
      "Underline",
      "Italic",
      "Strikethrough",
      "Table",
      "Table 2",
      "Indent increase",
      "Indent decrease",
    ],
    "Charts and Codes": ["Pie"],
    Attentive: [
      "Eye blocked",
      "Warning",
      "Question",
      "Info",
      "Info 2",
      "Blocked",
      "Spam",
    ],
    "Multimedia Icons": [
      "Image",
      "Image 2",
      "Play",
      "Film",
      "Forward",
      "Equalizer",
      "Brightness medium",
      "Brightness contrast",
      "Contrast",
      "Play 2",
      "Pause",
      "Forward 2",
      "Play 3",
      "Pause 2",
      "Forward 3",
      "Previous",
      "Next",
      "Volume high",
      "Volume medium",
      "Volume low",
      "Volume mute",
      "Volume mute 2",
      "Volume increase",
      "Volume decrease",
    ],
    "Location and Contact": [
      "Home",
      "Home 2",
      "Home 3",
      "Phone",
      "Phone hang up",
      "Envelope",
      "Location",
      "Location 2",
      "Map",
      "Map 2",
      "Flag",
    ],
    "Date and Time": [
      "History",
      "Clock",
      "Clock 2",
      "Stopwatch",
      "Calendar",
      "Calendar 2",
    ],
    Devices: [
      "Camera",
      "Headphones",
      "Camera 2",
      "Keyboard",
      "Screen",
      "Tablet",
    ],
    Tools: [
      "Pencil",
      "Pencil 2",
      "Pen",
      "Paint format",
      "Dice",
      "Key",
      "Key 2",
      "Lock",
      "Lock 2",
      "Unlocked",
      "Wrench",
      "Cog",
      "Cogs",
      "Cog 2",
      "Filter",
      "Filter 2",
    ],
    "Social and Networking": [
      "Share",
      "Googleplus",
      "Googleplus 2",
      "Googleplus 3",
      "Googleplus 4",
      "Google drive",
      "Facebook",
      "Facebook 2",
      "Facebook 3",
      "Twitter",
      "Twitter 2",
      "Twitter 3",
      "Vimeo",
      "Vimeo 2",
      "Vimeo 3",
      "Github",
      "Github 2",
      "Github 3",
      "Github 4",
      "Github 5",
      "Wordpress",
      "Wordpress 2",
      "Tumblr",
      "Tumblr 2",
      "Yahoo",
      "Soundcloud",
      "Soundcloud 2",
      "Reddit",
      "Linkedin",
      "Lastfm",
      "Lastfm 2",
      "Stumbleupon",
      "Stumbleupon 2",
      "Stackoverflow",
      "Pinterest",
      "Pinterest 2",
      "Yelp",
    ],
    Brands: [
      "Joomla",
      "Apple",
      "Finder",
      "Android",
      "Windows",
      "Windows 8",
      "Skype",
      "Paypal",
      "Paypal 2",
      "Paypal 3",
      "Chrome",
      "Firefox",
      "Opera",
      "Safari",
    ],
    "Files & Documents": [
      "File",
      "File 2",
      "File 3",
      "File 4",
      "Folder",
      "Folder open",
      "File pdf",
      "File openoffice",
      "File word",
      "File excel",
      "File zip",
      "File powerpoint",
      "File xml",
      "File css",
      "Html 5",
      "Html 52",
    ],
    "Like & Dislike Icons": [
      "Eye",
      "Eye 2",
      "Star",
      "Star 2",
      "Star 3",
      "Heart",
      "Heart 2",
      "Heart broken",
      "Thumbs up",
      "Thumbs up 2",
    ],
    Emoticons: [
      "Happy",
      "Happy 2",
      "Smiley",
      "Smiley 2",
      "Tongue",
      "Tongue 2",
      "Sad",
      "Sad 2",
      "Wink",
      "Wink 2",
      "Grin",
      "Grin 2",
      "Cool",
      "Cool 2",
      "Angry",
      "Angry 2",
      "Evil",
      "Evil 2",
      "Shocked",
      "Shocked 2",
      "Confused",
      "Confused 2",
      "Neutral",
      "Neutral 2",
      "Wondering",
      "Wondering 2",
    ],
    "Directional Icons": [
      "Point up",
      "Point right",
      "Point down",
      "Point left",
      "Arrow up left",
      "Arrow up",
      "Arrow up right",
      "Arrow right",
      "Arrow down right",
      "Arrow down",
      "Arrow down left",
      "Arrow left",
      "Arrow up left 2",
      "Arrow up 2",
      "Arrow up right 2",
      "Arrow right 2",
      "Arrow down right 2",
      "Arrow down 2",
      "Arrow down left 2",
      "Arrow left 2",
      "Arrow up left 3",
      "Arrow up 3",
      "Arrow up right 3",
      "Arrow right 3",
      "Arrow down right 3",
      "Arrow down 3",
      "Arrow down left 3",
      "Arrow left 3",
    ],
    "Other Icons": [
      "Quill",
      "Blog",
      "Droplet",
      "Images",
      "Music",
      "Pacman",
      "Spades",
      "Clubs",
      "Diamonds",
      "Pawn",
      "Bullhorn",
      "Connection",
      "Podcast",
      "Feed",
      "Stack",
      "Tags",
      "Barcode",
      "Qrcode",
      "Ticket",
      "Coin",
      "Credit",
      "Notebook",
      "Pushpin",
      "Compass",
      "Alarm",
      "Alarm 2",
      "Bell",
      "Print",
      "Laptop",
      "Mobile",
      "Mobile 2",
      "Tv",
      "Disk",
      "Storage",
      "Reply",
      "Bubbles",
      "Bubbles 2",
      "Bubbles 3",
      "Bubbles 4",
      "Users",
      "Users 2",
      "Quotes left",
      "Spinner",
      "Spinner 2",
      "Spinner 3",
      "Spinner 4",
      "Spinner 5",
      "Spinner 6",
      "Binoculars",
      "Search",
      "Hammer",
      "Wand",
      "Aid",
      "Bug",
      "Stats",
      "Bars",
      "Bars 2",
      "Gift",
      "Trophy",
      "Glass",
      "Mug",
      "Food",
      "Leaf",
      "Rocket",
      "Meter",
      "Meter 2",
      "Dashboard",
      "Hammer 2",
      "Fire",
      "Lab",
      "Magnet",
      "Remove",
      "Remove 2",
      "Briefcase",
      "Airplane",
      "Truck",
      "Road",
      "Accessibility",
      "Target",
      "Shield",
      "Lightning",
      "Switch",
      "Powercord",
      "Signup",
      "Tree",
      "Cloud",
      "Earth",
      "Bookmarks",
      "Notification",
      "Close",
      "Checkmark",
      "Checkmark 2",
      "Minus",
      "Plus",
      "Stop",
      "Backward",
      "Stop 2",
      "Backward 2",
      "First",
      "Last",
      "Eject",
      "Loop",
      "Loop 2",
      "Loop 3",
      "Shuffle",
      "Tab",
      "Checkbox checked",
      "Checkbox unchecked",
      "Checkbox partial",
      "Crop",
      "Font",
      "Text height",
      "Text width",
      "Omega",
      "Sigma",
      "Insert template",
      "Pilcrow",
      "Lefttoright",
      "Righttoleft",
      "Paragraph left",
      "Paragraph center",
      "Paragraph right",
      "Paragraph justify",
      "Paragraph left 2",
      "Paragraph center 2",
      "Paragraph right 2",
      "Paragraph justify 2",
      "Newtab",
      "Mail",
      "Mail 2",
      "Mail 3",
      "Mail 4",
      "Google",
      "Instagram",
      "Feed 2",
      "Feed 3",
      "Feed 4",
      "Youtube",
      "Youtube 2",
      "Lanyrd",
      "Flickr",
      "Flickr 2",
      "Flickr 3",
      "Flickr 4",
      "Picassa",
      "Picassa 2",
      "Dribbble",
      "Dribbble 2",
      "Dribbble 3",
      "Forrst",
      "Forrst 2",
      "Deviantart",
      "Deviantart 2",
      "Steam",
      "Steam 2",
      "Blogger",
      "Blogger 2",
      "Tux",
      "Delicious",
      "Xing",
      "Xing 2",
      "Flattr",
      "Foursquare",
      "Foursquare 2",
      "Libreoffice",
      "Css 3",
      "IE",
      "IcoMoon",
    ],
  };

  var pntr;

  function init_icon_picker() {
    if (pntr) {
      pntr.destroyPicker();
    }

    pntr = $(".icon_picker_select").fontIconPicker({
      source: icm_icons,
      searchSource: icm_icon_search,
      useAttribute: true,
      attributeName: "data-icomoon",
      theme: "fip-bootstrap",
    });
    $(".sort_rows").sortable({ forcePlaceholderSize: true });
  }
  init_icon_picker();

  $("#add_new_row").click(function () {
    var cloned = $(".sort_rows tr").first().clone();
    $("input", cloned).val("");
    $(".icons-selector", cloned).replaceWith("");
    //$('.csmm-form-control', cloned).removeClass('csmm-form-control');
    $(".sort_rows").append(cloned);

    $(".sort_rows").sortable("refresh");
    init_icon_picker();
  });
  $(".social_sytems_table").on("click", ".remove_row_button", function () {
    $(this).parents("tr").remove();
  });

  $("#signals_show_name").change(function () {
    if ($(this).attr("checked") == "checked") {
      $("#signals_csmm_message_noname").parents(".csmm-form-group").fadeIn();
    } else {
      $("#signals_csmm_message_noname").parents(".csmm-form-group").fadeOut();
    }
    //
  });
  $("#signals_show_name").change();

  $("#background_type")
    .change(function (e) {
      $("#design-background .background-type").hide();
      $("#design-background .background-type-" + $(this).val()).show();
    })
    .trigger("change");

  $("#prepopulate_fields").click(function () {
    data = parse_form_html($("#signals_autoconfigure").val());

    if (data.action_url) {
      $("#signal_ua_action_url").val(data.action_url);
    }
    if (data.method) {
      $("#signal_ua_method").val(data.method);
    }
    if (data.extra_data) {
      $("#signal_ua_additional_data").val(data.extra_data);
    }
    if (data.name_field) {
      $("#signal_ua_name_field_name").val(data.name_field);
    }
    if (data.email_field) {
      $("#signal_ua_email_field_name").val(data.email_field);
    }
  });

  function parse_form_html(form_html) {
    var $ = jQuery.noConflict();
    data = {
      action_url: "",
      email_field: "",
      name_field: "",
      extra_data: "",
      method: "",
      email_fields_extra: "",
    };

    html = $.parseHTML(
      '<div id="parse-form-tmp" style="display: none;">' + form_html + "</div>"
    );

    data.action_url = $("form", html).attr("action");
    if ($("form", html).attr("method")) {
      data.method = $("form", html).attr("method").toLowerCase();
    }

    email_fields = $("input[type=email]", html);
    if (email_fields.length == 1) {
      data.email_field = $("input[type=email]", html).attr("name");
    }

    inputs = "";
    $("input", html).each(function (ind, el) {
      type = $(el).attr("type");
      if (
        type == "email" ||
        type == "button" ||
        type == "reset" ||
        type == "submit"
      ) {
        return;
      }

      name = $(el).attr("name");
      name_tmp = name.toLowerCase();

      if (
        !data.email_field &&
        (name_tmp == "email" ||
          name_tmp == "from" ||
          name_tmp == "emailaddress")
      ) {
        data.email_field = name;
      } else if (
        name_tmp == "name" ||
        name_tmp == "fname" ||
        name_tmp == "firstname"
      ) {
        data.name_field = name;
      } else {
        data.email_fields_extra += name + ", ";
        data.extra_data += name + "=" + $(el).attr("value") + "&";
      }
    }); // foreach

    data.email_fields_extra = data.email_fields_extra.replace(/\, $/g, "");
    data.extra_data = data.extra_data.replace(/&$/g, "");

    return data;
  } // parse_form_html

  $("#signals_autoconfigure")
    .on("change keyup paste click input", function (e) {
      if ($(this).val().length < 10 || $(this).val().indexOf("</form>") == -1) {
        $("#form-fields-preview").html(
          '<p class="csmm-form-help-block">' +
            $("#form-fields-preview").data("default") +
            "</p>"
        );
        $("#prepopulate_fields").hide();
        return true;
      }

      data = parse_form_html($(this).val());
      preview = "";

      if (data.action_url) {
        preview += "Action URL: <code>" + data.action_url + "</code><br>";
      } else {
        preview += "Action URL: <b>not detected</b><br>";
      }
      if (data.method) {
        preview += "Method: <code>" + data.method + "</code><br>";
      } else {
        preview += "Method: <b>not detected</b>, using GET<br>";
      }
      if (data.email_field) {
        preview +=
          "Email field name: <code>" + data.email_field + "</code><br>";
      } else {
        preview += "Email field name: <b>not detected</b><br>";
        if (data.email_fields_extra) {
          preview +=
            "Possible email field names: <code>" +
            data.email_fields_extra +
            "</code><br>";
        }
      }
      if (data.name_field) {
        preview += "Name field name: <code>" + data.name_field + "</code><br>";
      } else {
        preview += "Name field name: <b>not detected</b><br>";
      }
      if (data.extra_data) {
        preview += "Extra data: <code>" + data.extra_data + "</code><br>";
      } else {
        preview += "Extra data: <b>not detected</b><br>";
      }
      $("#form-fields-preview").html(preview);

      $("#prepopulate_fields").show();
      return true;
    })
    .trigger("change"); // onchange

  // backedn email system integration
  $("#mail_system_to_use").change(function () {
    $(".single_mail_block").hide();
    $("." + $(this).val() + "_block_cont").fadeIn();
  });
  $("#mail_system_to_use").change();

  function reloadFont($fontValue) {
    baseFonts = [
      "Arial",
      "Helvetica",
      "Georgia",
      "Times New Roman",
      "Tahoma",
      "Verdana",
      "Geneva",
    ];
    if (baseFonts.indexOf($fontValue) != -1) {
      return;
    }

    WebFont.load({
      google: {
        families: [$fontValue],
      },
    });
  }

  function changeFont($font) {
    var $fontValue = $font.val();

    reloadFont($fontValue);
    $font.parent().find("h3").css("font-family", $fontValue);
  }

  var unsplash_page = 1;
  var total_pages = 9999;
  var total_results = 0;
  var unsplash_search_query = "";
  var custom_uploader;

  function ucp_get_unsplash_images() {
    jQuery
      .ajax({
        url: ajaxurl,
        method: "POST",
        crossDomain: true,
        dataType: "json",
        timeout: 30000,
        data: {
          action: "mcsm_editor_unsplash_api",
          page: unsplash_page,
          per_page: 60,
          search: unsplash_search_query,
        },
      })
      .success(function (response) {
        var unsplash_images = "";
        var unsplash_html = "";
        if (response.success) {
          if (response.data.results) {
            unsplash_images = JSON.parse(response.data.results);
            total_results = response.data.total_results;
            total_pages = response.data.total_pages;

            for (i in unsplash_images) {
              unsplash_html +=
                '<div class="ucp-unsplash-image" data-id="' +
                unsplash_images[i]["id"] +
                '" data-url="' +
                unsplash_images[i]["full"] +
                '" data-name="' +
                unsplash_images[i]["name"] +
                '">';
              unsplash_html +=
                '<img src="' + unsplash_images[i]["thumb"] + '">';
              unsplash_html += unsplash_images[i]["user"];
              unsplash_html += "</div>";
            }
          }

          unsplash_html += '<div class="ucp_unsplash_pagination">';

          if (total_pages > 1) {
            unsplash_html +=
              total_results.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, "$1,") +
              " images";
          }

          if (unsplash_page > 1) {
            unsplash_html += '<div id="ucp_unsplash_prev">&lt;- Previous</div>';
          }
          if (!total_pages || unsplash_page < total_pages) {
            unsplash_html += '<div id="ucp_unsplash_next">Next -&gt;</div>';
          }
          unsplash_html += "</div>";
          unsplash_html +=
            '<p style="text-align: center;"><small>Powered by <a href="https://unsplash.com/?utm_source=Coming+Soon+demo&utm_medium=referral" target="_blank">Unsplash</a></small></p>';
          jQuery(".unsplash-browser").html(unsplash_html);
        } else {
          jQuery(".unsplash-browser").html(
            '<div class="ucp-loader">An error occured contacting the Unsplash API.<br /><span class="ucp-unsplash-retry">Click here to try again.</span></div>'
          );
        }
      })
      .error(function (type) {
        jQuery(".unsplash-browser").html(
          '<div class="ucp-loader">An error occured contacting the Unsplash API.<br /><span class="ucp-unsplash-retry">Click here to try again.</span></div>'
        );
      });
  }

  var depositphotos_page = 1;
  var total_pages = 9999;
  var total_results = 0;
  var depositphotos_search_query = "";
  var custom_uploader;

  function ucp_get_depositphotos_images() {
    jQuery
      .ajax({
        url: ajaxurl,
        method: "POST",
        crossDomain: true,
        dataType: "json",
        timeout: 30000,
        data: {
          action: "mcsm_editor_depositphotos_api",
          page: depositphotos_page,
          per_page: 20,
          search: depositphotos_search_query,
        },
      })
      .success(function (response) {
        var depositphotos_images = "";
        var depositphotos_html = "";
        if (response.success) {
          if (response.data.results) {
            depositphotos_images = JSON.parse(response.data.results);
            total_results = response.data.total_results;
            total_pages = response.data.total_pages;

            for (i in depositphotos_images) {
              depositphotos_html +=
                '<a href="' +
                depositphotos_images[i]["itemurl"] +
                '" target="_blank" class="ucp-depositphotos-image" data-id="' +
                depositphotos_images[i]["id"] +
                '" data-url="' +
                depositphotos_images[i]["full"] +
                '" data-name="' +
                depositphotos_images[i]["name"] +
                '">';
              depositphotos_html +=
                '<img src="' + depositphotos_images[i]["thumb"] + '">';
              depositphotos_html += "</a>";
            }
          }

          depositphotos_html += '<div class="ucp_depositphotos_pagination">';

          if (total_pages > 1) {
            depositphotos_html +=
              total_results.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, "$1,") +
              " images";
          }

          if (depositphotos_page > 1) {
            depositphotos_html +=
              '<div id="ucp_depositphotos_prev">&lt;- Previous</div>';
          }
          if (!total_pages || depositphotos_page < total_pages) {
            depositphotos_html +=
              '<div id="ucp_depositphotos_next">Next -&gt;</div>';
          }
          depositphotos_html += "</div>";
          depositphotos_html +=
            '<p style="text-align: center;"><small>Powered by <a href="https://depositphotos.com/?ref=30484348" target="_blank">Deposit Photos</a></small></p>';
          jQuery(".depositphotos-browser").html(depositphotos_html);
        } else {
          jQuery(".depositphotos-browser").html(
            '<div class="ucp-loader">An error occured contacting the depositphotos API.<br /><span class="ucp-depositphotos-retry">Click here to try again.</span></div>'
          );
        }
      })
      .error(function (type) {
        jQuery(".depositphotos-browser").html(
          '<div class="ucp-loader">An error occured contacting the depositphotos API.<br /><span class="ucp-depositphotos-retry">Click here to try again.</span></div>'
        );
      });
  }

  // upload function

  function getUploader($text, $target, $3rdparty) {
    if (custom_uploader) {
      custom_uploader.detach();
    }

    // Extend the wp.media object
    custom_uploader = wp.media.frames.file_frame = wp.media({
      title: $text,
      button: {
        text: $text,
      },
      multiple: false,
    });

    if ($3rdparty) {
      custom_uploader.on("open", function () {
        var image_input_id = $target
          .parent()
          .children(".mm_upload_image_input")
          .attr("id");

        if (
          !jQuery(".media-frame-router .media-router .ucp-unsplash-images")
            .length
        ) {
          jQuery(".media-frame-router .media-router").append(
            '<a href="#" class="media-menu-item ucp-unsplash-images">Unsplash (free images)</a>'
          );
        }

        unsplash_search_query = "";

        $(".media-menu-item").removeClass("active");
        $(".ucp-unsplash-images").addClass("active");
        custom_uploader.content._mode = "unsplash";
        //mediaUploader.router.view=[];
        $(".media-button-select").hide();
        $(".ucp-media-button-select").show();
        $(".media-modal-content .media-frame-content").html(
          '<div class="unsplash_head"><button disabled="disabled" id="unsplash_search_btn" class="button button-primary">Search</button><input type="text" id="unsplash_search" placeholder="Search images..." /></div><div class="unsplash-browser"><div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ...</div> </div>'
        );

        if (jQuery(".media-toolbar .ucp-media-button-select").length) {
          jQuery(".media-toolbar .ucp-media-button-select").remove();
        }

        jQuery(".media-button-select").after(
          '<button type="button" disabled="disabled" ' +
            (jQuery(".media-menu-item.active").hasClass("ucp-unsplash-images")
              ? ""
              : ' style="display:none" ') +
            ' class="button button-primary button-large media-button ucp-media-button-select" data-id="' +
            image_input_id +
            '">Use Selected Image</button>'
        );

        ucp_get_unsplash_images(1);
      });

      custom_uploader.on("open", function () {
        var image_input_id = $target
          .parent()
          .children(".mm_upload_image_input")
          .attr("id");

        if (
          !jQuery(".media-frame-router .media-router .ucp-depositphotos-images")
            .length
        ) {
          jQuery(".media-frame-router .media-router").append(
            '<a href="#" class="media-menu-item ucp-depositphotos-images">Deposit Photos</a>'
          );
        }

        depositphotos_search_query = "";
      });
    }

    // When a file is selected, grab the URL and set it as the text field's value
    custom_uploader.on("select", function () {
      var attachment = custom_uploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $target.parent().find("input").val(attachment.url);
      $target
        .parent()
        .find(".csmm-preview-area")
        .html('<img src="' + attachment.url + '" />');
      $target
        .parent()
        .find(".csmm-upload-append")
        .html(
          '&nbsp;<a href="javascript: void(0);" class="csmm-remove-image">Remove</a>'
        );
    });

    // Open the uploader dialog
    custom_uploader.open();
  }

  // css and html editor
  function getEditor($editorID, $textareaID, $mode) {
    if ($("#" + $editorID).length > 0) {
      var editor = ace.edit($editorID),
        $textarea = $("#" + $textareaID).hide();

      editor.getSession().setValue($textarea.val());

      editor.getSession().on("change", function () {
        $textarea.val(editor.getSession().getValue());
      });

      editor.getSession().setMode("ace/mode/" + $mode);
      //editor.setTheme( 'ace/theme/xcode' );
      editor.getSession().setUseWrapMode(true);
      editor.getSession().setWrapLimitRange(null, null);
      editor.renderer.setShowPrintMargin(null);

      editor.session.setUseSoftTabs(null);
    }
  }

  // WP native uploader

  $(document).on("click", ".csmm-upload", function (e) {
    e.preventDefault();
    if ($(this).hasClass("mm-free-images")) {
      getUploader("Select Image", $(this), true);
    } else {
      getUploader("Select Image", $(this), false);
    }
  });

  // Removing photo from the canvas and emptying the text field
  $(document).on("click", ".csmm-remove-image", function (e) {
    e.preventDefault();
    $(this).parent().parent().find("input").val("");
    $(this)
      .parent()
      .parent()
      .find(".csmm-preview-area")
      .html("Select an image or upload a new one");
    $(this).hide();
  });

  $("body").on(
    "click",
    ".media-frame-router .media-router .media-menu-item",
    function () {
      if ($(this).hasClass("ucp-unsplash-images")) {
        $(".media-menu-item").removeClass("active");
        $(this).addClass("active");
        custom_uploader.content._mode = "unsplash";
        $(".media-button-select").hide();
        $(".ucp-media-button-select").show();
        $(".media-modal-content .media-frame-content").html(
          '<div class="unsplash_head"><button disabled="disabled" id="unsplash_search_btn" class="button button-primary">Search</button><input type="text" id="unsplash_search" placeholder="Search unsplash images..." /></div><div class="unsplash-browser"><div class="ucp-unsplash-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> </div>'
        );

        ucp_get_unsplash_images();
      } else if ($(this).hasClass("ucp-depositphotos-images")) {
        $(".media-menu-item").removeClass("active");
        $(this).addClass("active");
        custom_uploader.content._mode = "unsplash";
        $(".media-button-select").hide();
        $(".ucp-media-button-select").hide();
        $(".media-modal-content .media-frame-content").html(
          '<div class="depositphotos_head"><button disabled="disabled" id="depositphotos_search_btn" class="button button-primary">Search</button><input type="text" id="depositphotos_search" placeholder="Search depositphotos images..." /></div><div class="depositphotos-browser"><div class="ucp-depositphotos-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> </div>'
        );

        ucp_get_depositphotos_images();
      } else {
        $(".media-button-select").show();
        $(".ucp-media-button-select").hide();
      }
    }
  );

  // hide nags from other plugins
  $(
    "#wpbody-content .update-nag, #wpbody-content .notice-error, #wpbody-content .notice-info"
  ).hide();

  // google fonts
  $(".csmm-google-fonts").each(function () {
    var $font = $(this);
    changeFont($font);
  });

  $(document).on("change", ".csmm-google-fonts", function () {
    var $font = $(this);
    changeFont($font);
  });

  // ios switches
  var elements = Array.prototype.slice.call(
    document.querySelectorAll(".csmm-form-ios")
  );
  elements.forEach(function (html) {
    var switchery = new Switchery(html);
  });

  $("#csmm_mode").on("change", function () {
    $(".csmm-mode-options").hide();
    var mode = $(this).val();
    $("#csmm_mode_" + mode).show();

    if (mode == "layout") {
      $(".csmm-design-layout-modules").show();
    } else {
      $(".csmm-design-layout-modules").hide();
    }
  });

  // sortable
  sortable = $(".csmm-layout-builder")
    .sortable({
      animation: 150,
      connectWith: "ul",
      dataIdAttr: "data-id",
      forcePlaceholderSize: true,
      placeholder: "module-placeholder",
      start: function (event, ui) {
        $("#arrange-items li").addClass("drag-action");
      },
      stop: function (event, ui) {
        $("#arrange-items li").removeClass("drag-action");
      },
      create: function (event, ui) {
        $(".csmm-layout-builder").removeClass("empty");
        if (!$("#arrange-items li").length) {
          $("#arrange-items").addClass("empty");
        }
        if (!$("#arrange-items2 li").length) {
          $("#arrange-items2").addClass("empty");
        }
      },
      update: function (event, ui) {
        $(".csmm-layout-builder").removeClass("empty");
        if (!$("#arrange-items li").length) {
          $("#arrange-items").addClass("empty");
        }
        if (!$("#arrange-items2 li").length) {
          $("#arrange-items2").addClass("empty");
        }

        if (this.id != "arrange-items") {
          return;
        }
        order = $(this).sortable("toArray", { attribute: "data-id" });
        positions = order.join(",");
        $("#signals_csmm_arrange").val(positions).trigger("change");
      },
    })
    .disableSelection();

  $(".csmm-layout-builder").on("update", function () {
    $(".csmm-layout-builder").removeClass("empty");
    if (!$("#arrange-items li").length) {
      $("#arrange-items").addClass("empty");
    }
    if (!$("#arrange-items2 li").length) {
      $("#arrange-items2").addClass("empty");
    }

    if (this.id != "arrange-items") {
      return;
    }
    order = $(this).sortable("toArray", { attribute: "data-id" });
    positions = order.join(",");
    $("#signals_csmm_arrange").val(positions).trigger("change");
  });

  $("#arrange-items").on("click", ".remove-module", function (e) {
    e.preventDefault();
    module = $(this).parents("li");

    module.appendTo("#arrange-items2");
    $(".csmm-layout-builder").sortable("refresh").trigger("update");
  });

  $("#arrange-items2").on("click", ".add-module", function (e) {
    e.preventDefault();
    module = $(this).parents("li");

    module.appendTo("#arrange-items");
    $(".csmm-layout-builder").sortable("refresh").trigger("update");
  });

  // css and html editor
  getEditor("signals_csmm_html_editor", "signals_csmm_html", "html");
  getEditor("signals_csmm_wpm_editor", "signals_csmm_wpm_content", "html");
  getEditor("signals_csmm_css_editor", "signals_csmm_css", "css");
  getEditor(
    "signals_custom_html_layout_editor",
    "signals_custom_html_layout",
    "html"
  );
  getEditor("signals_csmm_custom_head_code", "custom_head_code", "html");
  getEditor("signals_csmm_custom_foot_code", "custom_foot_code", "html");

  $("input.csmm-color").spectrum({
    showAlpha: true,
    showInput: true,
    preferredFormat: "rgb",
    change: function (color) {
      color.toRgbString();
      $(this).val(rgb2hex(color.toRgbString()));
    },
  });

  function rgb2hex(orig) {
    var a,
      isPercent,
      rgb = orig
        .replace(/\s/g, "")
        .match(/^rgba?\((\d+),(\d+),(\d+),?([^,\s)]+)?/i),
      alpha = ((rgb && rgb[4]) || "").trim(),
      hex = rgb
        ? (rgb[1] | (1 << 8)).toString(16).slice(1) +
          (rgb[2] | (1 << 8)).toString(16).slice(1) +
          (rgb[3] | (1 << 8)).toString(16).slice(1)
        : orig;
    if (alpha !== "") {
    
      isPercent = alpha.indexOf("%") > -1;
      a = parseFloat(alpha);
      console.log('A: ' + alpha);
      if (!isPercent && a >= 0 && a <= 1) {
        a = Math.round(255 * a);
      } else if (isPercent && a >= 0 && a <= 100) {          
        a = Math.round((255 * a) / 100);
      } else {
        a = "00";
      }
    }
    if (a || a == 0) {
      hex += (a | (1 << 8)).toString(16).slice(1);
    }
    return hex;
  }

  if (
    typeof csmm != "undefined" &&
    csmm.rebranding == false &&
    csmm.whitelabel == true
  ) {
    // open Help Scout Beacon
    $(".settings_page_maintenance_mode_options").on(
      "click",
      ".open-beacon",
      function (e) {
        e.preventDefault();
        Beacon("open");
        return false;
      }
    );

    // init Help Scout beacon
    if (csmm.csmm_is_plugin_page) {
      Beacon("config", {
        enableFabAnimation: false,
        display: {},
        contactForm: {},
        labels: {},
      });
      Beacon("prefill", {
        name: "\n\n\n" + csmm.support_name,
        subject: "Coming Soon & Maintenance Mode PRO in-plugin support",
        email: "",
        text: "\n\n\n" + csmm.support_text,
      });
      Beacon("init", "8dea34f8-a21a-4390-923e-c55b82340cc9");
    }
  }

  $(".csmm-cnt-fix").on("click", ".csmm-change-tab", function (e) {
    e.preventDefault();

    tab_name = $(this).attr("href");
    csmm_change_tab(tab_name);

    return false;
  });

  $("#submit-save-api").on("click", function (e) {
    e.preventDefault();

    safe_refresh = true;
    $("#signals_doing_save").val("1");
    $("#signals_change_mc_api").val("1");
    $(this).parents("form").submit();

    return false;
  });

  $("#csmm_save_license").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    wf_licensing_verify_licence_ajax(
      "csmm",
      $("#signals_license_key").val(),
      button
    );
    return;
  });

  $("#csmm_keyless_activation").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    wf_licensing_verify_licence_ajax("csmm", "keyless", button);
    return;
  });

  $("#csmm_deactivate_license").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    wf_licensing_deactivate_licence_ajax(
      "csmm",
      $("#signals_license_key").val(),
      button
    );
    return;
  });

  $("#signals_license_key").on("keypress", function (e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      $("#csmm_save_license").trigger("click");
    }
  });

  $(".csmm-admin-form input[type='text']").on("keypress", function (e) {
    if (e.keyCode == 13) {
      e.preventDefault();
      $("#signals_csmm_submit").trigger("click");
    }
  });
  

  function csmm_change_tab(tab_name) {
    tab_name = "#" + tab_name.replace("#", "");

    $('.csmm-main-menu li a[href="' + tab_name + '"]').trigger("click");
    window.scrollTo(0, 0);
  } // csmm_change_tab

  // dismiss notice
  $(".csmm-alert .notice-dismiss").on("click", function (e) {
    e.preventDefault();

    $(this).parents(".csmm-alert").fadeOut();

    return false;
  });

  $("form.csmm-admin-form *").on("focus", function () {
    safe_refresh = false;
  });

  // alert user of unsaved changes when doing preview
  //
  old_settings = $("form.csmm-admin-form *").not(".skip-save").serialize();
  $("#csmm-preview").on("click", function (e) {
    if (
      $("form.csmm-admin-form *").not(".skip-save").serialize() != old_settings
    ) {
      e.preventDefault();
      csmm_swal
        .fire({
          type: "question",
          title:
            "There are unsaved changes that will not be visible in the preview. Please save changes first.<br />Continue?",
          text: "",
          confirmButtonText: "Continue",
          cancelButtonText: "Cancel",
          showConfirmButton: true,
          showCancelButton: true,
        })
        .then((result) => {
          if (result.value) {
            window.open($(this).attr("href"), "_blank");
          }
        });
    }

    return true;
  });

  $("#signals_csmm_submit").on("click", function (e) {
    e.preventDefault();
    save_ajax();
  });

  safe_refresh = true;

  $(window).on("beforeunload", function (e) {
    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_secondary")
    ) {
      $("#signals_csmm_secondary").val(
        tinymce.get("signals_csmm_secondary").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_content_2col_text_left")
    ) {
      $("#signals_csmm_content_2col_text_left").val(
        tinymce.get("signals_csmm_content_2col_text_left").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_content_2col_text_right")
    ) {
      $("#signals_csmm_content_2col_text_right").val(
        tinymce.get("signals_csmm_content_2col_text_right").getContent()
      );
    }

    if (
      csmm.is_activated &&
      $("form.csmm-admin-form *").not(".skip-save").serialize() != old_settings
    ) {
      msg =
        "There are unsaved changes that will not be visible in the preview. Please save changes first.\nContinue?";
      e.returnValue = msg;
      return msg;
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_wpm_content")
    ) {
      $("#signals_csmm_wpm_content").val(
        tinymce.get("signals_csmm_wpm_content").getContent()
      );
    }
  });

  $("body").on("click", ".ucp-unsplash-image", function () {
    $(".ucp-unsplash-image").removeClass("ucp-unsplash-image-selected");
    $(this).addClass("ucp-unsplash-image-selected");
    $(".ucp-media-button-select").removeAttr("disabled");
  });

  $("body").on("click", ".ucp-ucporiginal-image", function () {
    $(".ucp-ucporiginal-image").removeClass("ucp-ucporiginal-image-selected");
    $(this).addClass("ucp-ucporiginal-image-selected");
    $(".ucp-media-button-select").removeAttr("disabled");
  });

  $("body").on("keyup change", "#unsplash_search", function (e) {
    if ($(this).val().length == 0 || $(this).val().length >= 3) {
      $("#unsplash_search_btn").removeAttr("disabled");
      if (e.which == 13) {
        unsplash_execute_search();
      }
    } else {
      $("#unsplash_search_btn").attr("disabled", "disabled");
    }
  });

  $("body").on("click", "#unsplash_search_btn", function () {
    unsplash_execute_search();
  });

  function unsplash_execute_search() {
    if (
      $("#unsplash_search").val().length == 0 ||
      $("#unsplash_search").val().length >= 3
    ) {
      $(".unsplash-browser").html(
        '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Searching images ... </div> '
      );
      unsplash_search_query = $("#unsplash_search").val();
      unsplash_page = 1;
      ucp_get_unsplash_images();
    } else {
      $("#unsplash_search_btn").attr("disabed", "disabled");
    }
  }

  $("body").on("keyup change", "#depositphotos_search", function (e) {
    if ($(this).val().length == 0 || $(this).val().length >= 3) {
      $("#depositphotos_search_btn").removeAttr("disabled");
      if (e.which == 13) {
        depositphotos_execute_search();
      }
    } else {
      $("#depositphotos_search_btn").attr("disabled", "disabled");
    }
  });

  $("body").on("click", "#depositphotos_search_btn", function () {
    depositphotos_execute_search();
  });

  function depositphotos_execute_search() {
    if (
      $("#depositphotos_search").val().length == 0 ||
      $("#depositphotos_search").val().length >= 3
    ) {
      $(".depositphotos-browser").html(
        '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Searching images ... </div> '
      );
      depositphotos_search_query = $("#depositphotos_search").val();
      depositphotos_page = 1;
      ucp_get_depositphotos_images();
    } else {
      $("#depositphotos_search_btn").attr("disabed", "disabled");
    }
  }

  $("body").on("click", ".ucp-media-button-select", function () {
    $(".ucp-media-button-select").attr("disabled", "disabled");
    if ($(".media-menu-item.active").hasClass("ucp-unsplash-images")) {
      var ucp_unsplash_id = "";
      var image_input_id = $(this).data("id");
      $(".ucp-unsplash-image-selected").each(function () {
        ucp_unsplash_id = $(this).data("id");
        ucp_unsplash_url = $(this).data("url");
        ucp_unsplash_name = $(this).data("name");
      });

      if (ucp_unsplash_id != "") {
        $(".media-modal-content .media-frame-content").html(
          '<div class="unsplash-browser"><div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Downloading image ... </div> </div>'
        );
        $.ajax({
          url: ajaxurl,
          method: "POST",
          crossDomain: true,
          dataType: "json",
          timeout: 300000,
          data: {
            action: "mcsm_editor_unsplash_download",
            image_id: ucp_unsplash_id,
            image_url: ucp_unsplash_url,
            image_name: ucp_unsplash_name,
          },
        })
          .success(function (response) {
            if (response.success) {
              if (response.data) {
                /*
                    $('#'+image_input_id).val(response.data);
                    $('#'+image_input_id).trigger('change');
					*/
                var pnt = $("#signals_csmm_bg").parents(".csmm-upload-element");

                $("#" + image_input_id)
                  .parent()
                  .find(".csmm-preview-area")
                  .html('<img src="' + response.data + '" />');
                $("#" + image_input_id)
                  .parent()
                  .find(".csmm-upload-append")
                  .html(
                    '&nbsp;<a href="javascript: void(0);" class="csmm-remove-image">Remove</a>'
                  );
                $("#" + image_input_id).val(response.data);
                custom_uploader.close();
              }
            } else {
              $(".unsplash-browser").html(response.data);
              var message = "An error occured downloading the image.";
              if (response.data) {
                message = response.data;
              }
              $(".unsplash-browser").html(
                '<div class="ucp-loader">' +
                  message +
                  '<br /><span class="ucp-unsplash-retry">Click here to return to browsing.</span></div>'
              );
            }
          })
          .error(function (type) {
            $(".unsplash-browser").html(
              '<div class="ucp-loader">An error occured downloading the image.<br /><span class="ucp-unsplash-retry">Click here to return to browsing.</span></div>'
            );
          })
          .always(function (type) {
            $(".ucp-media-button-select").removeAttr("disabled");
          });
      }
    }
  });

  $("body").on("click", ".ucp-unsplash-retry", function () {
    $(".unsplash-browser").html(
      '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> '
    );
    ucp_get_unsplash_images();
  });

  $("body").on("click", "#ucp_unsplash_prev", function () {
    $(".unsplash-browser").html(
      '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> '
    );
    unsplash_page--;
    ucp_get_unsplash_images();
  });

  $("body").on("click", "#ucp_unsplash_next", function () {
    $(".unsplash-browser").html(
      '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> '
    );
    unsplash_page++;
    ucp_get_unsplash_images();
  });

  $("body").on("click", ".ucp-depositphotos-retry", function () {
    $(".depositphotos-browser").html(
      '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> '
    );
    ucp_get_depositphotos_images();
  });

  $("body").on("click", "#ucp_depositphotos_prev", function () {
    $(".depositphotos-browser").html(
      '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> '
    );
    depositphotos_page--;
    ucp_get_depositphotos_images();
  });

  $("body").on("click", "#ucp_depositphotos_next", function () {
    $(".depositphotos-browser").html(
      '<div class="ucp-loader"><span class="dashicons dashicons-spin dashicons-update"></span>&nbsp; Loading images ... </div> '
    );
    depositphotos_page++;
    ucp_get_depositphotos_images();
  });

  function parse_slider_label(label, value) {
    label = label.replace("%val%", value);

    return label;
  }

  function init_slider(slider_el, handle, input) {
    $(slider_el).slider({
      min: $(input).data("min"),
      max: $(input).data("max"),
      step: $(input).data("step"),
      value: $(input).val(),
      range: "max",
      create: function () {
        handle.text(parse_slider_label($(input).data("label"), $(input).val()));
      },
      slide: function (event, ui) {
        handle.text(parse_slider_label($(input).data("label"), ui.value));
        $(input).val(ui.value);
      },
    });
  }

  $(".csmm-slide-input").each(function (i, input) {
    slider_el =
      '<div title="Click to set the value" class="csmm-slider"><div title="Drag the handle to adjust value" class="ui-slider-handle"></div></div>';
    slider_el = $(slider_el).insertAfter(input);
    handle = $(".ui-slider-handle", slider_el);

    init_slider(slider_el, handle, input);
  }); // foreach slider

  $("#signals_csmm_ignore_styles")
    .on("change", function (e) {
      if ($(this).is(":checked")) {
        $("#custom-form-styles").show();
      } else {
        $("#custom-form-styles").hide();
      }
    })
    .trigger("change");

  $("#signals_csmm_site_password_toggle")
    .on("change", function (e) {
      if ($(this).is(":checked")) {
        $("#signals_csmm_site_password_wrapper").show();
      } else {
        $("#signals_csmm_site_password_wrapper").hide();
        $("#signals_csmm_site_password").val("");
      }
    })
    .trigger("change");

  $("#signals_csmm_wpm_replace")
    .on("change", function (e) {
      if ($(this).is(":checked")) {
        $("#signals_csmm_wpm_wrapper").show();
      } else {
        $("#signals_csmm_wpm_wrapper").hide();
        $("#signals_csmm_wpm_title").val("");
        $("#signals_csmm_wpm_content").val("");
      }
    })
    .trigger("change");

  // helper for linking anchors in different tabs
  $(".csmm-cnt-fix").on("click", ".confirm-action", function (e) {
    message = $(this).data("confirm");
    e.preventDefault();
    csmm_swal
      .fire({
        type: "question",
        title: message,
        text: "",
        confirmButtonText: "Continue",
        cancelButtonText: "Cancel",
        showConfirmButton: true,
        showCancelButton: true,
      })
      .then((result) => {
        if (result.value) {
          window.location.href = $(this).attr("href");
        }
      });
  }); // confirm action before link click

  new Clipboard(".copy-secret-link", {
    text: function (trigger) {
      url = $(trigger).data("base-url") + $("#direct_access_link").val();
      csmm_show_swal(
        "Secret access link has been copied to your clipboard.",
        "success",
        true,
        false,
        1500
      );
      return url;
    },
  });

  $(".show-datepicker").on("click", function (e) {
    e.preventDefault();
    $(this).prev("input").focus();
  });

  // init datepickers
  $(".datepicker").each(function () {
    options = {
      format: $(this).data("format")
        ? $(this).data("format")
        : "%Y-%m-%d %H:%i",
      firstDOW: 1,
      earliest: $(this).data("earliest") == "now" ? new Date() : "",
      labelTitle: $(this).data("title")
        ? $(this).data("title")
        : "Select a Date &amp; Time",
    };

    $(this).AnyTime_picker(options);
  });

  // stats
  var csmm_chart = false;
  var csmm_device_chart = false;
  function create_csmm_chart() {
    if (typeof csmm == "undefined" || !csmm.is_activated) {
      return;
    }
    if (!csmm.stats || !csmm.stats.days.length) {
      $("#csmm-chart").remove();
    } else {
      if (csmm_chart) csmm_chart.destroy();

      var chart_canvas = document.getElementById("csmm-chart").getContext("2d");
      var gradient = chart_canvas.createLinearGradient(0, 0, 0, 200);
      gradient.addColorStop(0, "#" + csmm.chart_colors[0]);
      gradient.addColorStop(1, "#ffffff");

      csmm_chart = new Chart(chart_canvas, {
        type: "line",
        data: {
          labels: csmm.stats.days,
          datasets: [
            {
              label: "Hits",
              yAxisID: "yleft",
              xAxisID: "xdown",
              data: csmm.stats.count,
              backgroundColor: gradient,
              borderColor: "#" + csmm.chart_colors[0],
              hoverBackgroundColor: "#" + csmm.chart_colors[0],
              borderWidth: 0,
            },
          ],
        },
        options: {
          animation: false,
          legend: false,
          maintainAspectRatio: false,
          tooltips: {
            mode: "index",
            intersect: false,
            callbacks: {
              title: function (value, values) {
                index = value[0].index;
                return moment(values.labels[index], "YYYY-MM-DD").format(
                  "dddd, MMMM Do"
                );
              },
            },
            displayColors: false,
          },

          scales: {
            xAxes: [
              {
                display: false,
                id: "xdown",
                stacked: true,
                ticks: {
                  callback: function (value, index, values) {
                    return moment(value, "YYYY-MM-DD").format("MMM Do");
                  },
                },
                categoryPercentage: 0.85,
                time: {
                  unit: "day",
                  displayFormats: { day: "MMM Do" },
                  tooltipFormat: "dddd, MMMM Do",
                },
                gridLines: { display: false },
              },
            ],
            yAxes: [
              {
                display: false,
                id: "yleft",
                position: "left",
                type: "linear",
                scaleLabel: {
                  display: true,
                  labelString: "Hits",
                },
                gridLines: { display: false },
                stacked: false,
                ticks: {
                  beginAtZero: false,
                  maxTicksLimit: 12,
                  callback: function (value, index, values) {
                    return Math.round(value);
                  },
                },
              },
            ],
          },
        },
      });
    }
  }

  create_csmm_chart();

  //seo

  function mm_kw_density(text, keyword) {
    var word_count = text.replace(/^\s+|\s+$/g, "").split(/\s+/).length;
    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = (text.match(kw_regex) || []).length;
    var kw_density = Math.round((kw_count / word_count) * 10000) / 100;
    return kw_density;
  }

  mm_seo_tests.exclude_serp = {};
  mm_seo_tests.exclude_serp.good = "The page is not blocking search engines.";
  mm_seo_tests.exclude_serp.bad =
    'The page is <a class="goto-anchor" href="#blockse">blocking search engines</a> so it will not appear in search results.';
  mm_seo_tests.exclude_serp.check = function () {
    var test_result = {};
    if ($("#signals_csmm_blockse").is(":checked")) {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.exclude_serp.bad;
    } else {
      test_result.grade = 10;
      test_result.message = mm_seo_tests.exclude_serp.good;
    }
    return test_result;
  };

  mm_seo_tests.keyword_title = {};
  mm_seo_tests.keyword_title.good = "Target keyword appears in the page title.";
  mm_seo_tests.keyword_title.bad =
    'Target keyword does not appear in the <a class="goto-anchor" href="#signals_csmm_title">page title</a>.';
  mm_seo_tests.keyword_title.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length < 1) {
      test_result.grade = -1;
      return test_result;
    }

    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = ($("#signals_csmm_title").val().match(kw_regex) || [])
      .length;

    if (kw_count > 0) {
      test_result.grade = 10;
      test_result.message = mm_seo_tests.keyword_title.good;
    } else {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_title.bad;
    }
    return test_result;
  };

  mm_seo_tests.keyword_description = {};
  mm_seo_tests.keyword_description.good =
    "Target keyword appears in the page meta description.";
  mm_seo_tests.keyword_description.bad =
    'Target keyword does not appear in page <a class="goto-anchor" href="#signals_csmm_description">meta description</a>.';
  mm_seo_tests.keyword_description.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length < 1) {
      test_result.grade = -1;
      return test_result;
    }

    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = ($("#signals_csmm_description").val().match(kw_regex) || [])
      .length;

    if (kw_count > 0) {
      test_result.grade = 10;
      test_result.message = mm_seo_tests.keyword_description.good;
    } else {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_description.bad;
    }
    return test_result;
  };

  mm_seo_tests.keyword_content = {};
  mm_seo_tests.keyword_content.good =
    "Target keyword appears in the page content.";
  mm_seo_tests.keyword_content.bad =
    'Target keyword does not appear in <a class="csmm-change-tab" href="#design-content">page content</a>.';
  mm_seo_tests.keyword_content.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length < 1) {
      test_result.grade = -1;
      return test_result;
    }

    var mm_content = "";
    if (tinymce.get("signals_csmm_secondary")) {
      mm_content = tinymce.get("signals_csmm_secondary").getContent();
    } else {
      mm_content = $("#signals_csmm_secondary").val();
    }

    if (tinymce.get("signals_csmm_content_2col_text_left")) {
      mm_content += tinymce
        .get("signals_csmm_content_2col_text_left")
        .getContent();
    } else {
      mm_content += $("#signals_csmm_content_2col_text_left").val();
    }

    if (tinymce.get("signals_csmm_content_2col_text_right")) {
      mm_content += tinymce
        .get("signals_csmm_content_2col_text_right")
        .getContent();
    } else {
      mm_content += $("#signals_csmm_content_2col_text_right").val();
    }

    if (tinymce.get("signals_csmm_wpm_content")) {
      mm_content += tinymce.get("signals_csmm_wpm_content").getContent();
    } else {
      mm_content += $("#signals_csmm_wpm_content").val();
    }

    var kw_density = mm_kw_density(mm_content, keyword);
    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = (mm_content.match(kw_regex) || []).length;

    if (kw_count > 0) {
      if (kw_density > 2) {
        test_result.grade = 0;
        test_result.message =
          "Keyword density in content is " +
          kw_density +
          "%, which is over the advised 2% maximum; the keyword was found " +
          kw_count +
          ' times in <a class="csmm-change-tab" href="#design-content">content</a>.';
      } else {
        test_result.grade = 10;
        test_result.message =
          "Keyword density in content is " +
          kw_density +
          "%, which is great; the keyword was found " +
          kw_count +
          " times.";
      }
    } else {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_content.bad;
    }

    return test_result;
  };

  mm_seo_tests.title_length = {};
  mm_seo_tests.title_length.good = "Page SEO title length is good.";
  mm_seo_tests.title_length.bad =
    'Page does not have a <a href="#signals_csmm_title" class="goto-anchor">SEO title</a>.';
  mm_seo_tests.title_length.check = function () {
    var test_result = {};
    var title = $("#signals_csmm_title").val();
    if (title.length < 1) {
      test_result.grade = 0;
      test_result.message =
        'Page does not have a <a href="#signals_csmm_title" class="goto-anchor">SEO title</a>.';
      return test_result;
    } else if (title.length < 40) {
      test_result.grade = 5;
      test_result.message =
        'Page <a href="#signals_csmm_title" class="goto-anchor">SEO title</a> is too short. Keep the length between 40 and 60 characters.';
    } else {
      test_result.grade = 10;
      test_result.message = "Page SEO title length is good.";
    }
    return test_result;
  };

  mm_seo_tests.description_length = {};
  mm_seo_tests.description_length.good =
    "Page meta description length is good.";
  mm_seo_tests.description_length.bad =
    'Page does not have a <a href="#signals_csmm_description" class="goto-anchor">meta description</a>.';
  mm_seo_tests.description_length.check = function () {
    var test_result = {};
    var description = $("#signals_csmm_description").val();
    if (description.length < 1) {
      test_result.grade = 0;
      test_result.message =
        'Page does not have a <a href="#signals_csmm_description" class="goto-anchor">meta description</a>.';
      return test_result;
    } else if (description.length < 50) {
      test_result.grade = 5;
      test_result.message =
        'Page <a href="#signals_csmm_description" class="goto-anchor">meta description</a> is too short. Keep the length between 50 and 300 characters.';
    } else {
      test_result.grade = 10;
      test_result.message = "Page meta description length is good.";
    }
    return test_result;
  };

  mm_seo_tests.keyword_url = {};
  mm_seo_tests.keyword_url.good = "Target keyword appears in the page URL.";
  mm_seo_tests.keyword_url.bad =
    'Target keyword does not appear in the <a href="options-general.php">page URL</a>.';
  mm_seo_tests.keyword_url.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length < 1) {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_url.bad;
      return test_result;
    }

    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = ($("#mm-seo-snippet-url").html().match(kw_regex) || [])
      .length;

    if (kw_count > 0) {
      test_result.grade = 10;
      test_result.message = mm_seo_tests.keyword_url.good;
    } else {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_url.bad;
    }
    return test_result;
  };

  mm_seo_tests.keyword_logo = {};
  mm_seo_tests.keyword_logo.good = "Target keyword appears in the logo title.";
  mm_seo_tests.keyword_logo.bad =
    'Target keyword does not appear in the <a class="csmm-change-tab" href="#design-logo">logo title</a>.';
  mm_seo_tests.keyword_logo.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length < 1) {
      test_result.grade = -1;
      return test_result;
    }

    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = ($("#logo_title").val().match(kw_regex) || []).length;

    if (kw_count > 0) {
      test_result.grade = 10;
      test_result.message = mm_seo_tests.keyword_logo.good;
    } else {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_logo.bad;
    }
    return test_result;
  };

  mm_seo_tests.keyword_header = {};
  mm_seo_tests.keyword_header.good =
    "Target keyword appears in the page header.";
  mm_seo_tests.keyword_header.bad =
    'Target keyword does not appear in the <a class="csmm-change-tab" href="#design-header">page header</a>.';
  mm_seo_tests.keyword_header.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length < 1) {
      test_result.grade = -1;
      return test_result;
    }

    var kw_regex = new RegExp(keyword, "gi");
    var kw_count = ($("#signals_csmm_header").val().match(kw_regex) || [])
      .length;

    if (kw_count > 0) {
      test_result.grade = 10;
      test_result.message = mm_seo_tests.keyword_header.good;
    } else {
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_header.bad;
    }
    return test_result;
  };

  mm_seo_tests.keyword_set = {};
  mm_seo_tests.keyword_set.good = "Target keyword is set.";
  mm_seo_tests.keyword_set.bad =
    'No <a class="goto-anchor" href="#signals_csmm_target_keyword">target keyword</a> set. If you don\'t set a keyword, SEO score can\'t be calculated.';
  mm_seo_tests.keyword_set.check = function () {
    var test_result = {};
    var keyword = $("#signals_csmm_target_keyword").val();
    if (keyword.length > 0) {
      keyword_set = true;
      test_result.grade = 10;
      test_result.message = mm_seo_tests.keyword_set.good;
    } else {
      keyword_set = false;
      test_result.grade = 0;
      test_result.message = mm_seo_tests.keyword_set.bad;
    }
    return test_result;
  };

  $(".csmm-admin-form").on(
    "change keyup",
    "input,select,textarea",
    function () {
      clearTimeout(mm_seo_test_running);
      mm_seo_test_running = setTimeout(run_seo_tests, 300);
    }
  );

  function run_seo_tests() {
    if ($state != "#seo") return;
    if (!seo_gage) {
      seo_gage = new JustGage({
        id: "mm-seo-gage",
        value: 0,
        min: 0,
        max: 100,
        title: "Page SEO Score",
        label: "",
        titleFontColor: "#666666",
        levelColors: ["#f9c802", "#ffb200", "#a9d70b"],
      });
    }

    $("#mm-seo-snippet-title").html(
      $("#signals_csmm_title")
        .val()
        .replace("%sitetitle%", $("#signals_csmm_title").data("site-title"))
        .replace(
          "%sitetagline%",
          $("#signals_csmm_description").data("site-description")
        )
    );
    $("#mm-seo-snippet-description").html(
      $("#signals_csmm_description")
        .val()
        .replace("%sitetitle%", $("#signals_csmm_title").data("site-title"))
        .replace(
          "%sitetagline%",
          $("#signals_csmm_description").data("site-description")
        )
    );
    var title_lenght = $("#signals_csmm_title").val().length;
    var title_bar_width = Math.round((title_lenght / 60) * 100);
    if (title_bar_width > 100) title_bar_width = 100;
    $("#mm-seo-progress-title .mm-seo-progress-bar").css(
      "width",
      title_bar_width + "%"
    );

    if (title_bar_width == 100) {
      $("#mm-seo-progress-title").removeClass("mm-seo-progress-good");
      $("#mm-seo-progress-title").addClass("mm-seo-progress-warning");
    } else if (title_bar_width < 80) {
      $("#mm-seo-progress-title").removeClass("mm-seo-progress-good");
      $("#mm-seo-progress-title").addClass("mm-seo-progress-warning");
    } else {
      $("#mm-seo-progress-title").removeClass("mm-seo-progress-warning");
      $("#mm-seo-progress-title").addClass("mm-seo-progress-good");
    }

    var description_lenght = $("#signals_csmm_description").val().length;
    var description_bar_width = Math.round((description_lenght / 300) * 100);
    if (description_bar_width > 100) description_bar_width = 100;
    $("#mm-seo-progress-description .mm-seo-progress-bar").css(
      "width",
      description_bar_width + "%"
    );

    if (description_bar_width == 100) {
      $("#mm-seo-progress-description").removeClass("mm-seo-progress-good");
      $("#mm-seo-progress-description").addClass("mm-seo-progress-warning");
    } else if (description_bar_width < 36) {
      $("#mm-seo-progress-description").removeClass("mm-seo-progress-good");
      $("#mm-seo-progress-description").addClass("mm-seo-progress-warning");
    } else {
      $("#mm-seo-progress-description").removeClass("mm-seo-progress-warning");
      $("#mm-seo-progress-description").addClass("mm-seo-progress-good");
    }

    var mm_test_results_html = "";
    test_results_html_good = "";
    test_results_html_warning = "";
    test_results_html_bad = "";
    var test_score = 0;
    for (test in mm_seo_tests) {
      var test_result = mm_seo_tests[test].check();
      var test_result_class = "";
      var test_result_message = "";
      test_score += test_result.grade;

      if (test_result.grade < 5) {
        test_results_html_bad +=
          '<li class="mm-test-bad">' + test_result.message + "</li>";
      } else if (test_result.grade < 10) {
        test_results_html_warning +=
          '<li class="mm-test-warning">' + test_result.message + "</li>";
      } else if (test_result.grade == 10) {
        test_results_html_good +=
          '<li class="mm-test-good">' + test_result.message + "</li>";
      }
    }

    if (keyword_set) {
      if (test_results_html_bad.length > 10) {
        mm_test_results_html +=
          '<div class="csmm-strong">Problems:</div><ul>' +
          test_results_html_bad +
          "</ul>";
      }
      if (test_results_html_warning.length > 10) {
        mm_test_results_html +=
          '<div class="csmm-strong">Potential Improvements:</div><ul>' +
          test_results_html_warning +
          "</ul>";
      }
      if (test_results_html_good.length > 10) {
        mm_test_results_html +=
          '<div class="csmm-strong">Good Results:</div><ul>' +
          test_results_html_good +
          "</ul>";
      }
      seo_gage.refresh(test_score);
    } else {
      mm_test_results_html +=
        '<div class="csmm-strong">Problems:</div><ul><li class="mm-test-bad">' +
        mm_seo_tests.keyword_set.bad +
        "</li></ul>";
      seo_gage.refresh(0);
    }

    $("#mm-seo-results").html(mm_test_results_html);
  }

  $(".csmm-cnt-fix").on("click", ".goto-anchor", function (e) {
    e.preventDefault();

    parts = this.href.split("#");
    target = parts[1];

    target_offset = $("#" + target).offset();
    target_top = target_offset.top;

    $("html, body").animate({ scrollTop: target_top - 70 }, 1000);
    $("#" + target).focus();
  });

  $("#video_type")
    .on("change", function (e) {
      val = $(this).val();
      $(".video-type-container").hide();
      $(".video-container-" + val).show();
    })
    .trigger("change");

  $("#per_url_settings")
    .on("change", function (e) {
      if ($(this).val() == "") {
        $(".per-url-wrapper").hide();
      } else {
        $(".per-url-wrapper").show();
      }
    })
    .trigger("change");

  function save_ajax(user_theme, overwrite) {
    var error = false;
    var error_message = "";

    if (
      $("#signals_csmm_direct_access_password").val().length > 0 &&
      $("#signals_csmm_direct_access_password").val().length < 4
    ) {
      error = true;
      error_message =
        "The Direct Access Password is too short. Please make it at least 4 characters long.";
    }

    if (
      $("#signals_csmm_site_password_toggle").is(":checked") &&
      $("#signals_csmm_site_password").val().length < 4
    ) {
      error = true;
      error_message =
        "The Password to Protect the Coming Soon Page is too short. Please make it at least 4 characters long.";
    }

    if (error !== false) {
      csmm_show_swal(error_message, "error", false, false, 1500);
      return false;
    }

    block_ui("Saving Settings");
    
    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_secondary") != null &&
      tinymce.get("signals_csmm_secondary").hidden == false
    ) {
      $("#signals_csmm_secondary").val(
        tinymce.get("signals_csmm_secondary").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_content_2col_text_left") != null &&
      tinymce.get("signals_csmm_content_2col_text_left").hidden == false
    ) {
      $("#signals_csmm_content_2col_text_left").val(
        tinymce.get("signals_csmm_content_2col_text_left").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_content_2col_text_right") != null &&
      tinymce.get("signals_csmm_content_2col_text_right").hidden == false
    ) {
      $("#signals_csmm_content_2col_text_right").val(
        tinymce.get("signals_csmm_content_2col_text_right").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_wpm_content") != null &&
      tinymce.get("signals_csmm_wpm_content").hidden == false
    ) {
      $("#signals_csmm_wpm_content").val(
        tinymce.get("signals_csmm_wpm_content").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("signals_csmm_gdpr_policy_text") != null &&
      tinymce.get("signals_csmm_gdpr_policy_text").hidden == false
    ) {
      $("#signals_csmm_gdpr_policy_text").val(
        tinymce.get("signals_csmm_gdpr_policy_text").getContent()
      );
    }

    if (
      typeof tinymce != "undefined" &&
      tinymce &&
      tinymce.get("csmm_contact_gdpr_policy_text") != null &&
      tinymce.get("csmm_contact_gdpr_policy_text").hidden == false
    ) {
      $("#csmm_contact_gdpr_policy_text").val(
        tinymce.get("csmm_contact_gdpr_policy_text").getContent()
      );
    }

    $("input.csmm-color").each(function () {
      $(this).val(rgb2hex($(this).val()));
    });

    data = $("form.csmm-admin-form *").not(".skip-save").serialize();
    $("#signals_csmm_submit")
      .addClass("loading")
      .find("strong")
      .html("Please wait ...");

    $.post(
      ajaxurl,
      {
        form_data: data,
        user_theme: user_theme,
        overwrite: overwrite,
        _ajax_nonce: csmm.nonce_save_settings,
        action: "csmm_save_settings",
      },
      "json"
    )
      .always(function () {
        $("#signals_csmm_submit")
          .removeClass("loading")
          .find("strong")
          .html($("#signals_csmm_submit").data("caption"));
        $("#signals_csmm_submit").blur();
        old_settings = $("form.csmm-admin-form *")
          .not(".skip-save")
          .serialize();
        csmm_swal.close();
        $.unblockUI();
      })
      .success(function (response) {
        if (
          typeof response != "object" ||
          response === null ||
          !response.success
        ) {
          if (response.data == "overwrite") {
            csmm_swal
              .fire({
                type: "question",
                title:
                  "User theme \"" + user_theme + "\" already exists.<br /> Overwrite?",
                text: "",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                showConfirmButton: true,
                showCancelButton: true,
              })
              .then((result) => {
                if (result.value) {
                  save_ajax(user_theme, true);
                } else {
                  $("#save-theme").trigger('click');
                }
              });
          } else {
            csmm_show_swal(
              response.data
                ? response.data
                : "An undocumented error has occured. Please reload the page and try again.",
              "error",
              true,
              false,
              false
            );
          }
        } else {
          csmm_show_swal(
            "Settings have been saved!",
            "success",
            false,
            false,
            1500
          );
          if (user_theme) {
            location.href = csmm.settings_url;
          }
        }
      })
      .error(function () {
        csmm_show_swal(
          "An undocumented error has occured. Please reload the page and try again.",
          "error",
          true,
          false,
          false
        );
      });
  } // save_ajax

  $("#save-theme").on("click", function () {
    csmm_swal
      .fire({
        title: "Save user theme",
        text: "Enter the theme name:",
        input: "text",
        inputValue: "My Theme",
        inputPlaceholder: "Enter your theme name",
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: "Save",
        width: 600,
      })
      .then((result) => {
        if (result.dismiss || typeof result.value == "undefined") {
          return;
        } else {
          save_ajax(result.value);
        }
      });
  });

  // display a message while an action is performed
  function block_ui(message) {
    tmp = csmm_swal.fire({
      text: message,
      type: false,
      imageUrl: csmm.loader_image,
      onOpen: () => {
        //$(csmm_swal.getImage()).addClass("dashicons-spin");
      },
      imageWidth: 100,
      imageHeight: 100,
      imageAlt: message,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
      timerProgressBar: true,
    });

    return tmp;
  } // block_ui

  // display a message while an action is performed
  function csmm_show_swal(message, type, confirm, cancel, close) {
    tmp = csmm_swal.fire({
      title: message,
      icon: type,
      imageWidth: 100,
      imageHeight: 100,
      imageAlt: message,
      timer: close,
      timerProgressBar: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: confirm,
      showCancelButton: cancel,
    });

    return tmp;
  } // block_ui

  if ($("#csmm-onboarding-tabs-wrapper").length > 0) {
    $("#csmm-onboarding-tabs-wrapper").dialog({
      dialogClass: "wp-dialog csmm-dialog csmm-onboarding-dialog",
      modal: 1,
      resizable: false,
      zIndex: 9999,
      width: 700,
      height: "auto",
      show: "fade",
      hide: "fade",
      open: function (event, ui) {
        $("#csmm-onboarding-tabs").tabs({ active: 0 });
        $(".ui-widget-overlay").addClass("csmm-onboarding-overlay");
        $("#csmm-onboarding-tabs-wrapper").dialog("option", "position", {
          my: "top",
          at: "top",
          of: window,
        });
      },
      close: function (event, ui) {},
      autoOpen: false,
      closeOnEscape: true,
    });

    $("#csmm-onboarding-tabs-wrapper")
      .dialog("option", "title", "Welcome to 301 Redirects")
      .dialog("open");

    $(".settings_page_maintenance_mode_options").on(
      "click",
      ".csmm-onboarding-tab-next",
      function () {
        $("#csmm-onboarding-tabs").tabs(
          "option",
          "active",
          $(this).closest("[data-tab]").data("tab") + 1
        );
      }
    );

    $(".settings_page_maintenance_mode_options").on(
      "click",
      ".csmm-onboarding-tab-previous",
      function () {
        $("#csmm-onboarding-tabs").tabs(
          "option",
          "active",
          $(this).closest("[data-tab]").data("tab") - 1
        );
        wf301_save_onboarding_settings();
      }
    );

    $(".settings_page_maintenance_mode_options").on(
      "click",
      ".csmm-onboarding-tab-skip",
      function () {
        $("#csmm-onboarding-tabs-wrapper").dialog("close");
      }
    );

    $(window).resize(function () {
      $("#csmm-onboarding-tabs-wrapper").dialog("option", "position", {
        my: "top",
        at: "top",
        of: window,
      });
      if ($(this).width() > 900) {
        $("#csmm-onboarding-tabs-wrapper").dialog("option", "width", "700px");
      } else {
        $("#csmm-onboarding-tabs-wrapper").dialog(
          "option",
          "width",
          $(window).width() * 0.9
        );
      }
    });
  }

  $("#signals_csmm_arrange")
    .on("change", function (e) {
      visible_el = $("#signals_csmm_arrange").val();
      $(".csmm-tile").each(function (ind, el) {
        id = $(this).attr("id");
        // exit early if we're not interested

        if (
          !id ||
          id.indexOf("design-") == -1 ||
          id.indexOf("design-layout") != -1 ||
          id.indexOf("design-background") != -1
        ) {
          return true;
        }
        original_title = $("div.csmm-tile-title", this).data("original-title");
        if (!original_title) {
          $("div.csmm-tile-title", this).data(
            "original-title",
            $("div.csmm-tile-title", this).html()
          );
          original_title = $("div.csmm-tile-title", this).html();
        }

        module = id.replace("design-", "");

        if (visible_el.indexOf(module) != -1) {
          $('.csmm-submenu [href="#' + id + '"] .dashicons').addClass(
            "dashicons-visibility"
          );
          $('.csmm-submenu [href="#' + id + '"] .dashicons').removeClass(
            "dashicons-hidden"
          );
          $("div.csmm-tile-title", this).html(
            original_title +
              ' <span title="This module is active and visible on the page. Use the Layout tab to move or remove it." class="visible"><a href="#design-layout" class="csmm-change-tab"><div class="dashicons dashicons-visibility"></div> visible</a></span>'
          );
        } else {
          $('.csmm-submenu [href="#' + id + '"] .dashicons').removeClass(
            "dashicons-visibility"
          );
          $('.csmm-submenu [href="#' + id + '"] .dashicons').addClass(
            "dashicons-hidden"
          );
          $("div.csmm-tile-title", this).html(
            original_title +
              ' <span title="This module is hidden. Use the Layout tab to add it to the page." class="not-visible"><a href="#design-layout" class="csmm-change-tab"><div class="dashicons dashicons-hidden"></div> hidden</a></span>'
          );
        }
      });
    })
    .trigger("change");

  $("#csmm-search-templates")
    .on("change mouseup keyup focus blur search", function (e) {
      e.preventDefault();

      if (!$(this).val()) {
        $("#csmm-themes-wrapper")
          .find(".theme-thumb")
          .css("display", "inline-block");
        return;
      }

      search_string = new RegExp($(this).val(), "i");

      $("#csmm-themes-wrapper")
        .find(".theme-thumb")
        .each(function () {
          if (search_string.test($(this).data("theme-name"))) {
            $(this).css("display", "inline-block");
          } else {
            $(this).hide();
          }
        });
    })
    .trigger("search");

  $("#background_image_filter")
    .on("change", function (e) {
      filter = $(this).val();
      image = $("#background-preview img");
      if (!image.length) {
        return;
      }

      $(image).removeClass();
      $(image).addClass(filter);
    })
    .trigger("change");

  $("#background_video_filter")
    .on("change", function (e) {
      filter = $(this).val();
      video = $("#video-preview");
      video_fallback = $("#video-fallback-preview img");

      $(video).removeClass().addClass(filter);
      $(video_fallback).removeClass().addClass(filter).addClass(filter);
    })
    .trigger("change");

  $("#background_video")
    .on("change keyup click", function (e) {
      video_url = $(this).val();
      preview = $("#video-preview .video-container");

      if (video_url == preview.data("video-id")) {
        return;
      }

      if (video_url) {
        video =
          '<iframe src="https://www.youtube.com/embed/' +
          video_url +
          "?controls=0&amp;showinfo=0&amp;rel=0&amp;autoplay=1&amp;loop=1&amp;mute=1&amp;playlist=" +
          video_url +
          '" frameborder="0"></iframe>';
      } else {
        video = "";
      }

      preview.data("video-id", video_url);
      preview.html(video);
    })
    .trigger("change");

  $("#header-status").on("click", function (e) {
    if ($("#signals_csmm_status").val() == "1") {
      $("#signals_csmm_status").val("0");
      $("#header-status .csmm-status-wrapper")
        .removeClass("on")
        .addClass("off");
    } else {
      $("#signals_csmm_status").val("1");
      $("#header-status .csmm-status-wrapper")
        .removeClass("off")
        .addClass("on");
    }
    save_ajax();
  });
}); // on ready
