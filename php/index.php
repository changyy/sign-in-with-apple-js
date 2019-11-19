<?php
require 'config.php';
?>
<html>
    <head>
    </head>
    <body>
        <script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>
        <div id="appleid-signin" data-color="black" data-border="true" data-type="sign in"></div>
        <script type="text/javascript">
            AppleID.auth.init({
                clientId : '<?php echo $settings["CLIENT_ID"];?>',
                scope : '<?php echo $settings["SCOPES"];?>',
                redirectURI: '<?php echo $settings["REDIRECT_URI"];?>',
                state : '<?php echo $settings["STATE"];?>'
            });
        </script>
    </body>
</html>
