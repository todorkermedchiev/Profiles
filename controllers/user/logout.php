<?php

// controllers/profiles/logout.php

session_destroy();

redirect_to('user', 'login');
