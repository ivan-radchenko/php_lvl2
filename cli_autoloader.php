<?php

use Ivan\Php\User\User;
use Ivan\Php\Blog\{Post, Comment};

spl_autoload_register('load');

function load($className)
{
    var_dump($className);
    $file = $className . ".php";
    $nameSpace = "Devavi\Leveltwo";
    $file = str_replace($nameSpace,"src", $file);
    $file = str_replace("\\","/", $file);
    $file = str_replace("_","/", $file);      
    if (file_exists($file)){
        var_dump($file);
        include $file;
    }
};
echo $user1 = new User(1,'ser','serg');
echo $post1 = new Post(1, 1,'ser','serg');
echo $Comment1 = new Comment(1, 1, 1,'serg');


