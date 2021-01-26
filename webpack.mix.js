const mix = require('laravel-mix')

mix
  .js('resources/js/admin/admin-app.js', 'public/js')
  .js('resources/js/frontend/pages/auth/index.js', 'public/js/frontend/pages/auth')
  .js('resources/js/frontend/pages/booking/index.js', 'public/js/frontend/pages/booking')
  .js('resources/js/frontend/pages/checkout/index.js', 'public/js/frontend/pages/checkout')
  .js('resources/js/frontend/pages/explore/index.js', 'public/js/frontend/pages/explore')
  .js('resources/js/frontend/pages/place/index.js', 'public/js/frontend/pages/place')
  .js('resources/js/frontend/pages/welcome/index.js', 'public/js/frontend/pages/welcome')

  .sass('resources/sass/frontend/pages/welcome/index.scss', 'public/css/frontend/pages/welcome')
  .sass('resources/sass/frontend/pages/explore/index.scss', 'public/css/frontend/pages/explore')
  .sass('resources/sass/frontend/pages/auth/index.scss', 'public/css/frontend/pages/auth')
  .sass('resources/sass/frontend/pages/city/index.scss', 'public/css/frontend/pages/city')
  .sass('resources/sass/frontend/pages/place/index.scss', 'public/css/frontend/pages/place')
  .sass('resources/sass/frontend/pages/booking/index.scss', 'public/css/frontend/pages/booking')
  .sass('resources/sass/frontend/pages/access-code/index.scss', 'public/css/frontend/pages/access-code')
  .sass('resources/sass/admin/admin-app.scss', 'public/css/admin')
