<?php

define('ABSPATH', __DIR__ . '/../');
require_once __DIR__ . '/../includes/class-landing-bird-brand-credit.php';

function assert_same($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

$defaults = Landing_Bird_Brand_Credit::defaults();
assert_same('Landing Bird', $defaults['brand_name'], 'default brand');
assert_same('#f7f7f7', Landing_Bird_Brand_Credit::sanitize_color('red', $defaults['background_color']), 'invalid color fallback');
assert_same('https://landingbird.mx', Landing_Bird_Brand_Credit::sanitize_url('javascript:alert(1)', $defaults['brand_url']), 'unsafe URL fallback');
assert_same('Hello', Landing_Bird_Brand_Credit::sanitize_text('<b>Hello</b>', $defaults['message']), 'text sanitization');
$options = Landing_Bird_Brand_Credit::sanitize_options(['enabled' => 0, 'placement' => 'side']);
assert_same(false, $options['enabled'], 'disabled option');
assert_same('bottom', $options['placement'], 'placement fallback');
fwrite(STDOUT, "Brand credit tests passed.\n");
