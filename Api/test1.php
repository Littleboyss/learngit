<?php
$memcache = new Memcache;
$memcache->connect('192.168.5.199','11211');
$memcache->set('test1','1');
echo  $memcache->get('test1');
