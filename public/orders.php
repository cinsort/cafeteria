<?php
if (!(isset($_GET['payload'])))
    throw new Exception ('Login error: invalid token', 401);
