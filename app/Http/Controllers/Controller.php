<?php

namespace App\Http\Controllers;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../../../src/views/' . $view . '.php';
    }

    protected function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 