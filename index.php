<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="google-signin-client_id" content="1060450416021-o1re5ukm5uohjj7k8bg06u1d1t5hvcpf.apps.googleusercontent.com">
      <title>XML DOM Assignment2</title>
      <link rel="stylesheet" type="text/css" href="css/style.css" />
   </head>
   <body>
      <a id="skip-to-main" class="hidden" href="#view" tabindex="0">Skip to main content</a>
      <header id="main-header">
         <div id="main-header-content" class="flex-container page-wrapper">
            <div id="logo-and-sitename">
               <img id="main-header-logo" src="images/logo.png" alt="Beverly's
                  Site Logo" />
               <h1>Beverly's Chatroom App</h1>
            </div>
            <div id="userMenu"></div>
         </div>
      </header>
      <main>
         <div id="loginForm" class="page-wrapper">
            <h2>Login</h2>
            <form id="login">
               <div>
                  <label for="username">Username</label>
                  <input type="text" id="username" name="username" />
               </div>
               <div>
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" />
               </div>
               <div class="button-container">
                  <button id="login" type="submit" class="button">Login</button>
                  <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
               </div>
            </form>
            <div id="error" class="error"></div>
         </div>
         <div id="view"></div>
      </main>
      <footer id="main-footer">
         <h2 class="hidden">Main Footer</h2>
         <p class="copyright">Copyright &copy; 2018 Beverly Li</p>
      </footer>
      <script src="https://apis.google.com/js/platform.js" async defer></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="js/script.js"></script>
   </body>
</html>
