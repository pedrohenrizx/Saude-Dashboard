<?php
// Define the base path
define('BASE_PATH', __DIR__);

// Get the requested URI without query strings
$request = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request);
$path = $parsed_url['path'];

// Remove trailing slash if present (except for root)
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Basic Router
switch ($path) {
    case '/':
    case '/login':
        require BASE_PATH . '/views/login.php';
        break;

    case '/dashboard':
        require BASE_PATH . '/views/dashboard.php';
        break;

    default:
        // Handle 404 Not Found
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='pt-BR'><head><title>404 - Página Não Encontrada</title><script src='https://cdn.tailwindcss.com'></script></head>
              <body class='h-screen flex flex-col items-center justify-center bg-gray-100 text-gray-800'>
                <h1 class='text-6xl font-bold mb-4 text-blue-900'>404</h1>
                <p class='text-xl mb-8'>A página que você procura não existe ou foi movida.</p>
                <a href='/' class='px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium'>Voltar para o Início</a>
              </body></html>";
        break;
}
?>