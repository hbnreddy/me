<template>
  <footer class="welcome-footer">
    <div class="container">
      <div class="footer-wrapper">
        <div class="footer-social">
          <div class="footer-column">
            <div class="footer-title">
              <a href="/" class="navbar-brand">
                <img
                  src="/logo/logo-blue.png"
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
            <div class="footer-title">{{ __('Browse')}}</div>

            <ul class="menu-items">
              <li>
                <a href="/recent-plans">{{ __('Plans by other users')}}</a>
              </li>
              <li>
                <a href="/destinations">{{ __('Destinations')}}</a>
              </li>
              <li class="credits">
                <a href="/credits/home" rel="nofollow">{{ __('Credits')}}</a>
              </li>
            </ul>
          </div>

          <div class="footer-column">
            <div class="footer-title">{{ __('Help')}}</div>

            <ul class="menu-items">
              <li class="faq">
                <a href="/faq">{{ __('FAQ')}}</a>
              </li>

              <li>
                <a href="/contact-us" id="feedback-footer">{{ __('Contact us')}}</a>
              </li>

              <li>
                <a href="/privacy-policy">{{ __('Privacy policy')}}</a>
              </li>

              <li>
                <a href="/cookie-policy">{{ __('Cookie policy')}}</a>
              </li>

              <li>
                <a href="/copyright-policy">{{ __('Copyright policy')}}</a>
              </li>

              <li>
                <a href="/terms-of-use">{{ __('Terms of use')}}</a>
              </li>
            </ul>
          </div>

          <div class="footer-column">
            <div class="footer-title">{{ __('Settings')}}</div>

            <ul class="menu-items">
              <li id="currency" data-dropdown-url="/currencyOptions">
                <span class="label">
                  {{ __('Currency:')}}
                  <span class="selected-currency">{{ __('Indian Rupee')}}</span>
                </span>
              </li>

              <li id="language" data-dropdown-url="/languageOptions">
                <span class="label">
                  {{ __('Language:')}}
                  <span class="language-text">{{ __('English')}}</span>
                </span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="footer-copyright">
        <div>
          {{ __('© 2020')}}
          <a href="#">{{ __('Rutugo')}}
            <span class="tm"> ®</span>
          </a>
          {{ __('- All rights reserved')}}
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
