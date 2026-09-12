<?php include("servermain.php"); ?>
<html>
    <head>
        <title>Web Server - YouTube2012L</title>
        <link rel="stylesheet" type="text/css" href="webserver-r1_lyfkjGt1d.css"></link>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script> $(document).ready(function() { $(".iframe_toggle").click(function() { if (document.querySelector(".serverResponse.open")) { $(".iframe_toggle").text("View JSON response"); $(".serverResponse").removeClass("open"); } else { $(".iframe_toggle").text("Hide JSON response"); $(".serverResponse").addClass("open"); } }); }); </script>
    </head>
    <body class="server-body">
        <div class="web-content">
            <section class="response-shelf">
                <nav class="navbar">
                    <h4 class="shelf-header">Server</h4>
                </nav>
                <div class="shelf-content">
                    <article></article>
                    <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="100%" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
<path fill="#000000" opacity="1.000000" stroke="none" class="dropdown-arrow" d=" M28.250343,47.250038   C35.948071,39.972263 43.395756,32.944481 51.048069,25.723608   C64.555054,38.732563 77.908112,51.593254 91.102623,64.301254   C88.458145,66.922035 86.144493,69.214943 83.904732,71.434639   C73.575630,61.006462 62.839375,50.167229 51.932911,39.156151   C40.332344,50.791504 29.474123,61.682281 18.660192,72.528633   C15.705250,69.577965 13.401955,67.278008 10.549920,64.430099   C16.345896,58.806938 22.173100,53.153488 28.250343,47.250038  z"/>
</svg></button>
                </div>
            </section>
            <section class="response-shelf">
                <nav class="navbar">
                    <h4 class="shelf-header">Api</h4>
                </nav>
                <div class="shelf-content">
                    <article></article>
                    <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="100%" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
<path fill="#000000" opacity="1.000000" stroke="none" class="dropdown-arrow" d=" M28.250343,47.250038   C35.948071,39.972263 43.395756,32.944481 51.048069,25.723608   C64.555054,38.732563 77.908112,51.593254 91.102623,64.301254   C88.458145,66.922035 86.144493,69.214943 83.904732,71.434639   C73.575630,61.006462 62.839375,50.167229 51.932911,39.156151   C40.332344,50.791504 29.474123,61.682281 18.660192,72.528633   C15.705250,69.577965 13.401955,67.278008 10.549920,64.430099   C16.345896,58.806938 22.173100,53.153488 28.250343,47.250038  z"/>
</svg></button>
                </div>
            </section>
        </div>
        <div class="serverResponse"><iframe width="100%" height="100%" src="servermain_response" title="Server Response" class="serverResponse_iframe"></iframe></div>
        <a class="iframe_toggle">View JSON response</a>
    </body>
</html>