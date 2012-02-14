<?php
Router::connect('/image/delete/*', array('controller' => 'image', 'action' => 'delete','plugin'=>'ImageTable'));
Router::connect('/image/*', array('controller' => 'image', 'action' => 'display','plugin'=>'ImageTable'));

