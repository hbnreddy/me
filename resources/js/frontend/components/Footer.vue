<template>
  <div class="footer">
    <div class="container-fluid">
      <div class="social">
        <div class="logo-bottom">
          <img
            src="/logo/logo-blue.png"
            class="product-logo"
            alt="Rutugo Logo"
          >
        </div>

        <ul class="nav justify-content-end icons">
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

      <div class="copyright">
        <div>&copy; 2020 {{ __('All rights reserved') }}.</div>

        <ul class="nav justify-content-end info">
          <li class="nav-item">
            <a
              class="nav-link active"
              href="#"
            >
              {{ __('Contact&nbsp;us') }}:&nbsp;info@rutugo.com
            </a>
          </li>

          <li class="nav-item">
            <a
              class="nav-link"
              href="#"
            >
              {{ __('Privacy Policy') }}
            </a>
          </li>

          <li class="nav-item">
            <a
              class="nav-link"
              href="#"
            >
              {{ __('Terms and Conditions') }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  //
}
</script>
