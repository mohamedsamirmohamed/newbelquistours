<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$toursFile = __DIR__ . '/tours.json';
$contactsFile = __DIR__ . '/contacts.json';
$adminConfigFile = __DIR__ . '/admin-config.json';

function defaultTrips() {
    return [
        [
            'id' => 'def1',
            'title' => 'Maldives Escape',
            'destination' => 'Maldives',
            'category' => 'Beach',
            'duration' => '7 Days',
            'price' => '2500',
            'oldPrice' => '3200',
            'rating' => '4.9',
            'reviews' => '312',
            'seats' => '8',
            'image' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=900&q=80',
            'description' => 'A luxury beach holiday with overwater villas, snorkeling trips, and unforgettable sunsets.',
            'includes' => 'Flights,5-star hotel,Meals,Tours',
            'includesList' => ['Flights', '5-star hotel', 'Meals', 'Tours'],
            'excludesList' => ['Visa fees'],
            'schedule' => [
                ['title' => 'Day 1', 'details' => 'Arrival, transfer, and resort check-in.'],
                ['title' => 'Day 2', 'details' => 'Snorkeling and island discovery trip.']
            ],
            'gallery' => ['https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=900&q=80'],
            'singlePrice' => '2740',
            'doublePrice' => '1815',
            'triplePrice' => '1640',
            'featured' => true,
            'hot' => true,
            'status' => true
        ],
        [
            'id' => 'def2',
            'title' => 'Paris Highlights',
            'destination' => 'France',
            'category' => 'Culture',
            'duration' => '5 Days',
            'price' => '1800',
            'oldPrice' => '',
            'rating' => '4.8',
            'reviews' => '248',
            'seats' => '15',
            'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=900&q=80',
            'description' => 'Explore the Eiffel Tower, charming streets, fine dining, and world-famous museums.',
            'includes' => 'Flights,4-star hotel,Breakfast,Tours',
            'includesList' => ['Flights', '4-star hotel', 'Breakfast', 'Tours'],
            'excludesList' => ['Lunch', 'Visa fees'],
            'schedule' => [],
            'gallery' => ['https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=900&q=80'],
            'singlePrice' => '2140',
            'doublePrice' => '1650',
            'triplePrice' => '1490',
            'featured' => false,
            'hot' => false,
            'status' => true
        ],
        [
            'id' => 'def3',
            'title' => 'Dubai Luxury City Break',
            'destination' => 'UAE',
            'category' => 'Luxury',
            'duration' => '4 Days',
            'price' => '1400',
            'oldPrice' => '',
            'rating' => '4.8',
            'reviews' => '530',
            'seats' => '25',
            'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=900&q=80',
            'description' => 'Luxury hotels, desert safari, shopping, and iconic skyline experiences.',
            'includes' => 'Flights,Luxury hotel,Meals,Sightseeing,Tours',
            'includesList' => ['Flights', 'Luxury hotel', 'Meals', 'Sightseeing', 'Tours'],
            'excludesList' => ['Personal expenses'],
            'schedule' => [],
            'gallery' => ['https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=900&q=80'],
            'singlePrice' => '1980',
            'doublePrice' => '1540',
            'triplePrice' => '1410',
            'featured' => true,
            'hot' => false,
            'status' => true
        ]
    ];
}

function readJsonFile($file, $fallback) {
    if (!file_exists($file)) {
        writeJsonFile($file, $fallback);
        return $fallback;
    }

    $content = file_get_contents($file);
    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : $fallback;
}

function writeJsonFile($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

function getTrips() {
    global $toursFile;
    return readJsonFile($toursFile, defaultTrips());
}

function saveTrips($trips) {
    global $toursFile;
    return writeJsonFile($toursFile, array_values($trips));
}

function getContacts() {
    global $contactsFile;
    return readJsonFile($contactsFile, []);
}

function saveContacts($contacts) {
    global $contactsFile;
    return writeJsonFile($contactsFile, array_values($contacts));
}

function defaultAdminConfig() {
    return [
        'user' => 'admin',
        'pass' => '1234'
    ];
}

function getAdminConfig() {
    global $adminConfigFile;
    $config = readJsonFile($adminConfigFile, defaultAdminConfig());
    $user = trim((string)($config['user'] ?? 'admin'));
    $pass = (string)($config['pass'] ?? '1234');

    return [
        'user' => $user !== '' ? $user : 'admin',
        'pass' => $pass !== '' ? $pass : '1234'
    ];
}

function saveAdminConfig($config) {
    global $adminConfigFile;
    return writeJsonFile($adminConfigFile, [
        'user' => trim((string)($config['user'] ?? 'admin')),
        'pass' => (string)($config['pass'] ?? '1234')
    ]);
}

function isAdminAuthenticated() {
    return !empty($_SESSION['admin_logged_in']);
}

function requireAdminAuth() {
    if (!isAdminAuthenticated()) {
        sendResponse(['error' => 'Unauthorized'], 401);
    }
}

function sendResponse($payload, $status = 200) {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'auth_status') {
        $config = getAdminConfig();
        sendResponse([
            'authenticated' => isAdminAuthenticated(),
            'user' => $config['user']
        ]);
    }
    if ($action === 'contact_get') {
        requireAdminAuth();
        sendResponse(getContacts());
    }
    if ($action === 'contact_delete') {
        requireAdminAuth();
        $id = trim((string)($_GET['id'] ?? ''));
        if ($id === '') {
            sendResponse(['error' => 'No message ID provided'], 400);
        }

        $contacts = array_values(array_filter(getContacts(), function ($item) use ($id) {
            return ($item['id'] ?? '') !== $id;
        }));

        if (!saveContacts($contacts)) {
            sendResponse(['error' => 'Failed to delete message'], 500);
        }

        sendResponse(['success' => true, 'message' => 'Message deleted successfully']);
    }
    sendResponse(getTrips());
}

if ($method !== 'POST') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    sendResponse(['error' => 'Invalid JSON request'], 400);
}

$action = $input['action'] ?? 'save';

if ($action === 'auth_login') {
    $user = trim((string)($input['user'] ?? ''));
    $pass = (string)($input['pass'] ?? '');
    $config = getAdminConfig();

    if ($user !== $config['user'] || $pass !== $config['pass']) {
        sendResponse(['error' => 'Invalid username or password'], 401);
    }

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $config['user'];
    sendResponse(['success' => true, 'user' => $config['user']]);
}

if ($action === 'auth_logout') {
    $_SESSION = [];
    if (session_id() !== '' || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    sendResponse(['success' => true]);
}

if ($action === 'auth_update') {
    requireAdminAuth();
    $user = trim((string)($input['user'] ?? ''));
    $pass = (string)($input['pass'] ?? '');
    if ($user === '') {
        sendResponse(['error' => 'Username cannot be empty'], 400);
    }

    $config = getAdminConfig();
    $nextConfig = [
        'user' => $user,
        'pass' => $pass !== '' ? $pass : $config['pass']
    ];

    if (!saveAdminConfig($nextConfig)) {
        sendResponse(['error' => 'Failed to save admin settings'], 500);
    }

    $_SESSION['admin_user'] = $nextConfig['user'];
    sendResponse(['success' => true, 'user' => $nextConfig['user']]);
}

if ($action === 'save') {
    requireAdminAuth();
    $trip = $input['trip'] ?? null;
    if (!$trip || empty($trip['id'])) {
        sendResponse(['error' => 'No trip data provided'], 400);
    }

    $trips = getTrips();
    $found = false;

    foreach ($trips as $index => $item) {
        if (($item['id'] ?? '') === $trip['id']) {
            $trips[$index] = $trip;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $trips[] = $trip;
    }

    if (!saveTrips($trips)) {
        sendResponse(['error' => 'Failed to save tour'], 500);
    }

    sendResponse(['success' => true, 'message' => 'Tour saved successfully']);
}

if ($action === 'delete') {
    requireAdminAuth();
    $id = $input['id'] ?? '';
    if ($id === '') {
        sendResponse(['error' => 'No ID provided'], 400);
    }

    $trips = array_values(array_filter(getTrips(), function ($trip) use ($id) {
        return ($trip['id'] ?? '') !== $id;
    }));

    if (!saveTrips($trips)) {
        sendResponse(['error' => 'Failed to delete tour'], 500);
    }

    sendResponse(['success' => true, 'message' => 'Tour deleted successfully']);
}

if ($action === 'reset') {
    requireAdminAuth();
    if (!saveTrips(defaultTrips())) {
        sendResponse(['error' => 'Failed to reset tours'], 500);
    }

    sendResponse(['success' => true, 'message' => 'Data reset successfully']);
}

if ($action === 'contact_save') {
    $message = $input['message'] ?? [];
    $name = trim((string)($message['name'] ?? ''));
    $email = trim((string)($message['email'] ?? ''));
    $body = trim((string)($message['message'] ?? ''));

    if ($name === '' || $email === '' || $body === '') {
        sendResponse(['error' => 'Missing contact message data'], 400);
    }

    $contacts = getContacts();
    array_unshift($contacts, [
        'id' => 'msg_' . time() . '_' . random_int(100, 999),
        'name' => $name,
        'phone' => trim((string)($message['phone'] ?? '')),
        'email' => $email,
        'tripType' => trim((string)($message['tripType'] ?? '')),
        'message' => $body,
        'createdAt' => date('c'),
        'status' => 'new'
    ]);

    if (!saveContacts($contacts)) {
        sendResponse(['error' => 'Failed to save contact message'], 500);
    }

    sendResponse(['success' => true, 'message' => 'Contact message saved successfully']);
}

if ($action === 'contact_get') {
    requireAdminAuth();
    sendResponse(getContacts());
}

if ($action === 'contact_delete') {
    requireAdminAuth();
    $id = $input['id'] ?? '';
    if ($id === '') {
        sendResponse(['error' => 'No message ID provided'], 400);
    }

    $contacts = array_values(array_filter(getContacts(), function ($item) use ($id) {
        return ($item['id'] ?? '') !== $id;
    }));

    if (!saveContacts($contacts)) {
        sendResponse(['error' => 'Failed to delete message'], 500);
    }

    sendResponse(['success' => true, 'message' => 'Message deleted successfully']);
}

sendResponse(['error' => 'Unknown action'], 400);
