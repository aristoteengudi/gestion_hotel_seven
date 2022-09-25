<?php

$params = [
            'title'=>'Error 404 - Page not found',
            'app_full_url'=>get_app_full_url()];


render('error_404.html.twig', $params);