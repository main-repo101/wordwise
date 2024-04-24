<?php
$ROUTES = [
    404 => "/public/view/not-found.html",
    "/" => "/public/view/index.html",
    "/post-test" => "/public/view/post-test.html",
    "/pre-test" => "/public/view/pre-test.html",
];

function route($event = null) {
    $event = $event ?: $_GET['event'] ?? null;
    if (!$event) return;
    header("Location: $event");
    exit();
}

function handleLocation() {
    $path = $_SERVER['REQUEST_URI'];
    global $ROUTES;
    $route = $ROUTES[$path] ?? $ROUTES[404];
    $htmlRawText = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $route);
    if ($htmlRawText === false) {
        $error_message = http_response_code() === 404 ? "Resource not found" : "HTTP error! Status: " . http_response_code();
        $htmlRawText = "<h1>404 - $error_message</h1>";
    }
    echo $htmlRawText;
}

//REM: Call handleLocation on page load
handleLocation();
?>
