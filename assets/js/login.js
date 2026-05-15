jQuery(document).ready(function ($) {
  // Dynamic data passed from PHP
  const { client_id, redirect_uri, login_uri, ajaxURL, credential } =
    window.googleLoginData;

  if (credential) handleCredentialResponse(credential);

  window.google.accounts.id.initialize({
    client_id,
    ux_mode: "redirect",
    redirect_uri,
    login_uri,
  });

  const createFakeGoogleWrapper = () => {
    const googleLoginWrapper = $("<div>", {
      class: "google-login-button",
      css: { visibility: "hidden", position: "absolute", top: 0, left: 0 },
    }).appendTo("body");

    // Add tabindex for accessibility on iOS
    window.google.accounts.id.renderButton(googleLoginWrapper[0], {
      type: "icon",
      width: "200",
    });

    const googleButton = googleLoginWrapper.find("div[role=button]");
    googleButton.attr("tabindex", "0"); // Ensure the button is focusable

    return {
      click: () => {
        googleButton.focus(); // Focus on the button
        googleButton.trigger("click"); // Trigger the click
      },
    };
  };

  const googleButtonWrapper = createFakeGoogleWrapper();

  window.handleGoogleLogin = () => {
    googleButtonWrapper.click();
  };

  function handleCredentialResponse(response) {
    $.post(ajaxURL, {
      action: "google_login",
      credential: response,
    }).done(function (result) {
      if (result.success) {
        window.location.href = result.data.redirect_url;
      } else {
        alert("Google login failed.");
      }
    });
  }

  $(document).on(
    "click",
    ".google-login-btn, .google-login-google-btn",
    function () {
      console.log("click work!");
      handleGoogleLogin();
    }
  );
});
