<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lab Railway - Professional Profiles & Tutorial Videos</title>
    <meta name="description" content="A Railway project to control professional profiles and tutorial videos. Backend Laravel, Vue.js frontend, MySQL database.">
    @if(app()->environment('production'))
      @vite(['resources/js/app.js'])
    @else
      <script type="module" src="http://localhost:3000/@vite/client"></script>
      <script type="module" src="http://localhost:3000/src/main.js"></script>
    @endif
  </head>
  <body>
    <div id="app"></div>
  </body>
</html>