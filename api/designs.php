<?php
/**
 * GET /api/designs.php
 *
 * Returns the gallery items as JSON. This is the single source of truth
 * for the front end — swap this array (or point it at a database/CMS)
 * once real design pieces are ready, and the site updates automatically.
 *
 * Each item's `image` field is a path under /images/ — drop the real
 * files there and update the path; until then the front end renders a
 * styled placeholder tile instead.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Only GET is supported here.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$designs = [
    [
        'id' => 1,
        'title' => 'Cardamom & Ash',
        'category' => 'Branding',
        'description' => 'Wordmark and mark for a single-origin roastery focused on Ethiopian lots.',
        'image' => 'images/cardamom-ash.jpg',
    ],
    [
        'id' => 2,
        'title' => 'Drip Line No.4',
        'category' => 'Illustration',
        'description' => 'Loose ink studies of pour-over drip patterns, later used as a repeat pattern.',
        'image' => 'images/drip-line-4.jpg',
    ],
    [
        'id' => 3,
        'title' => 'Field Notes Bag',
        'category' => 'Packaging',
        'description' => '250g retail bag with a fold-over top and a stamped roast-date box.',
        'image' => 'images/field-notes-bag.jpg',
    ],
    [
        'id' => 4,
        'title' => 'Steam Study',
        'category' => 'Photography',
        'description' => 'Backlit steam captures used across the roastery\'s social templates.',
        'image' => 'images/steam-study.jpg',
    ],
    [
        'id' => 5,
        'title' => 'Kiosk Signage Set',
        'category' => 'Branding',
        'description' => 'Menu boards and sandwich-board signage for a three-seat espresso kiosk.',
        'image' => 'images/kiosk-signage.jpg',
    ],
    [
        'id' => 6,
        'title' => 'Bean Taxonomy Print',
        'category' => 'Illustration',
        'description' => 'A field-guide-style poster charting roast levels from light to dark.',
        'image' => 'images/bean-taxonomy.jpg',
    ],
    [
        'id' => 7,
        'title' => 'Cold Brew Growler Label',
        'category' => 'Packaging',
        'description' => 'Wraparound label for a 32oz growler, printed on waterproof stock.',
        'image' => 'images/growler-label.jpg',
    ],
    [
        'id' => 8,
        'title' => 'Roast Log Cover',
        'category' => 'Branding',
        'description' => 'Cover design for the roastery\'s internal roast-tracking logbook.',
        'image' => 'images/roast-log-cover.jpg',
    ],
];

echo json_encode(['ok' => true, 'designs' => $designs]);
