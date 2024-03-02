jQuery(document).ready(function ($) {
  $(".wpacm-color-field").wpColorPicker();

  $("#save-menu-colors").click(function (e) {
    e.preventDefault();
    $(".wpacm_loader").addClass("show");

    var sidebarColor = $("#sidebar_color").val();
    var adminbarColor = $("#adminbar_color").val();
    var menuColors = {};
    var nonce = $("#nonce").val();
    $(".wpacm-color-field").each(function () {
      var menuId = $(this).attr("id").replace("menu-color-", "");
      var menuColor = $(this).val();
      menuColors[menuId] = menuColor;
    });

    var data = {
      action: "save_menu_colors",
      sidebarColor: sidebarColor,
      adminbarColor: adminbarColor,
      menuColors: menuColors,
      nonce: nonce,
    };

    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        $(".wpacm_alert").append(response.data.log);
        if (response.data.status === "success") {
          $(".wpacm_alert").addClass("wpacm_success");
        } else {
          $(".wpacm_alert").addClass("wpacm_error");
        }
        $(".wpacm_alert").addClass("show");
        setTimeout(function () {
          location.reload();
        }, 2000);
      },
    });
  });

  $("#reset-menu-colors").click(function (e) {
    e.preventDefault();
    $(".wpacm_confirmation_dialog").fadeIn();
  });

  $("#wpacm_cancel_reset").click(function (e) {
    e.preventDefault();
    $(".wpacm_confirmation_dialog").fadeOut();
  });

  $("#wpacm_confirm_reset").click(function (e) {
    e.preventDefault();
    $(".confirmation-dialog").fadeOut();

    $(".wpacm_loader").addClass("show");
    var nonce = $("#nonce").val();
    var data = {
      action: "reset_menu_colors",
      nonce: nonce,
    };
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: data,
      dataType: "json",
      success: function (response) {
        $(".wpacm_alert").append(response.data.log);
        if (response.data.status === "success") {
          $(".wpacm_alert").addClass("wpacm_success");
        } else {
          $(".wpacm_alert").addClass("wpacm_error");
        }
        $(".wpacm_alert").addClass("show");
        setTimeout(function () {
          location.reload();
        }, 2000);
      },
    });
  });
});
