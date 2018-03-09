<?php 
include "main.php";
main_template(get_defined_vars(), function($vars) {
    extract($vars);
?>
    <div id="list" class="pure-u-1">
        <div class="pure-g">
            <div class="pure-u-1-6">
            </div>
            <div class="pure-u-2-3">
                <div class="block">
                    <form class="inner-block pure-form pure-form-stacked" action="login.php" method="post">
                        <fieldset>
                            <label for="username">Username</label>
                            <input name="username" id="username" type="text" placeholder="Username" required>
                    
                            <label for="password">Password</label>
                            <input name="password" id="password" type="password" placeholder="Password" required>
                    
                            <label for="remember" class="pure-checkbox">
                                <input name="remember" id="remember" type="checkbox"> Remember me
                            </label>
                    
                            <button type="submit" class="pure-button pure-button-primary">Sign in</button>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="pure-u-1-6">
            </div>            
        </div>
    </div>
<?php
});
?>
