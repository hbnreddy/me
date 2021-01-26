<template>
  <footer class="welcome-footer">
    <div class="container">
      <div class="footer-wrapper">
        <div class="footer-social">
          <div class="footer-column">
            <div class="footer-title">
              <a href="/" class="navbar-brand">
                <img
                  src="../assets/logo/logo-blue.png"
                  alt="Rutugo Logo"
                  class="product-logo"
                >
              </a>
            </div>

            <ul class="nav icons">
              <li class="nav-item">
                <a
                  class="nav-link facebook"
                  href="#"
                >
                  <i class="fa fa-facebook" />
                </a>
              </li>

              <li class="nav-item">
                <a
                  class="nav-link youtube"
                  href="#"
                >
                  <i class="fa fa-youtube-play" />
                </a>
              </li>

              <li class="nav-item">
                <a
                  class="nav-link instagram"
                  href="#"
                >
                  <i class="fa fa-instagram" />
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div class="footer-links">
          <div class="footer-column">
            <div class="footer-title">
              {{ __('Browse') }}
            </div>

            <ul class="menu-items">
              <li>
                <a href="#">{{ __('Plans by other users') }}</a>
              </li>
              <li>
                <a href="#">{{ __('Destinations') }}</a>
              </li>
            </ul>
          </div>

          <div class="footer-column">
            <div class="footer-title">
              {{ __('Help') }}
            </div>

            <ul class="menu-items">
              <li>
                <a id="feedback-footer" href="#">{{ __('Partners') }}</a>
              </li>
              <li>
                <a href="#">{{ __('Terms of Use') }}</a>
              </li>
              <li>
                <a href="#">{{ __('Privacy Policy') }}</a>
              </li>
              <li>
                <a href="#">{{ __('Cookie policy') }}</a>
              </li>
              <li>
                <a href="#">{{ __('Copyright policy') }}</a>
              </li>
            </ul>
          </div>

          <div class="footer-column">
            <div class="footer-title">
              {{ __('Settings') }}
            </div>

            <ul class="menu-items">
              <li id="currency" data-dropdown-url="#">
                <a href="#">{{ __('Feedback') }}</a>
              </li>

              <li id="language" data-dropdown-url="#">
                <a href="#">{{ __('Contact Us') }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="footer-copyright">
        <div>
          {{ __('© 2020') }}
          <a href="#">{{ __('Rutugo') }}
            <span class="tm"> ®</span>
          </a>
          {{ __('- All rights reserved') }}
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  //
}
</script>
