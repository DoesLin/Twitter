## Change default Mac OS X PHP to MAMP's PHP Installation and Install Composer Package Management
---

### Instructions to Change PHP Installation
---
First, Lets find out what version of PHP we're running (To find out if it's the default version).

To do that, Within the terminal, Fire this command:

    which php
    
This should output the path to the default PHP install which comes preinstalled by Mac OS X, by default it has to be (Assuming you've not changed it before):

    /usr/bin/php

Now, We just need to swap this over to the PHP that is installed with MAMP, which is located at `/Applications/MAMP/bin/php/php7.0.10/bin` (MAMP 2.1.3)

To do this, We need to edit the `.bash_profile` and add the MAMP version of PHP to the PATH variable.

#### Follow these simple steps:
---
1. Within the Terminal, run `nano ~/.bash_profile`

2. Paste the following at the top of the file:

        export PATH=/Applications/MAMP/bin/php/php7.0.10/bin:$PATH

3. Quit and save 

4. In Terminal, run `source ~/.bash_profile`

5. In Terminal, type in `which php` again and look for the updated string. If everything was successful, It should output the new path to MAMP PHP install.

6. In case it doesn't output the correct path, try closing the terminal window (exit fully) and open again, it should apply the changes (Restart in short). 
