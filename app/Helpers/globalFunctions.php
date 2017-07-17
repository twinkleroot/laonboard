<?php

function viewDefault($path, $params=[])
{
    $pathArr = explode('.', $path);
    $pathArr[1] = 'default';
    $defaultPath = implode('.', $pathArr);
    return view()->exists($path) ? view($path, $params) : view($defaultPath, $params);
}
