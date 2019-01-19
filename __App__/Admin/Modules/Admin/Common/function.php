<?php

function get_main_menus()
{
    $module = session('module');
    $menus = C('ADMIN_MENUS');
    return array_merge($menus['Public'], $menus[$module]);
}