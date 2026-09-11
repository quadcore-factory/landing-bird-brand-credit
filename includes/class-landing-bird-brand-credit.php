<?php

defined('ABSPATH') || exit;

final class Landing_Bird_Brand_Credit
{
    private const OPTION = 'landing_bird_brand_credit_options';
    private const SETTINGS_GROUP = 'landing_bird_brand_credit';

    public static function defaults(): array
    {
        return [
            'enabled' => true,
            'message' => 'Made with love by',
            'brand_name' => 'Landing Bird',
            'brand_url' => 'https://landingbird.mx',
            'placement' => 'bottom',
            'background_color' => '#f7f7f7',
            'text_color' => '#333333',
        ];
    }

    public static function boot(): void
    {
        $plugin = new self();
        add_action('admin_menu', [$plugin, 'admin_menu']);
        add_action('admin_init', [$plugin, 'register_settings']);
        add_action('wp_enqueue_scripts', [$plugin, 'enqueue_styles']);
        add_action('wp_body_open', [$plugin, 'render_top']);
        add_action('wp_footer', [$plugin, 'render_bottom']);
    }

    public static function sanitize_text($value, string $fallback): string
    {
        $value = is_scalar($value) ? (string) $value : '';
        $value = function_exists('sanitize_text_field') ? sanitize_text_field($value) : trim(strip_tags($value));
        return $value !== '' ? $value : $fallback;
    }

    public static function sanitize_url($value, string $fallback): string
    {
        $value = is_scalar($value) ? trim((string) $value) : '';
        $parts = function_exists('wp_parse_url') ? wp_parse_url($value) : parse_url($value);
        $scheme = isset($parts['scheme']) ? strtolower((string) $parts['scheme']) : '';
        if (!in_array($scheme, ['http', 'https'], true) || !filter_var($value, FILTER_VALIDATE_URL)) {
            return $fallback;
        }
        return $value;
    }

    public static function sanitize_color($value, string $fallback): string
    {
        $value = is_scalar($value) ? trim((string) $value) : '';
        return preg_match('/^#[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?$/', $value) ? strtolower($value) : $fallback;
    }

    public static function sanitize_options($input): array
    {
        $defaults = self::defaults();
        $input = is_array($input) ? $input : [];
        return [
            'enabled' => !empty($input['enabled']),
            'message' => self::sanitize_text($input['message'] ?? '', $defaults['message']),
            'brand_name' => self::sanitize_text($input['brand_name'] ?? '', $defaults['brand_name']),
            'brand_url' => self::sanitize_url($input['brand_url'] ?? '', $defaults['brand_url']),
            'placement' => in_array($input['placement'] ?? '', ['top', 'bottom'], true) ? $input['placement'] : $defaults['placement'],
            'background_color' => self::sanitize_color($input['background_color'] ?? '', $defaults['background_color']),
            'text_color' => self::sanitize_color($input['text_color'] ?? '', $defaults['text_color']),
        ];
    }

    public function admin_menu(): void
    {
        add_options_page('Landing Bird Brand Credit', 'Brand Credit', 'manage_options', 'landing-bird-brand-credit', [$this, 'settings_page']);
    }

    public function register_settings(): void
    {
        register_setting(self::SETTINGS_GROUP, self::OPTION, [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitize_options'],
            'default' => self::defaults(),
        ]);
        add_settings_section('lb_brand_credit_main', 'Credit appearance', '__return_false', 'landing-bird-brand-credit');
        $fields = [
            'enabled' => ['Enabled', 'checkbox'],
            'message' => ['Prefix / message text', 'text'],
            'brand_name' => ['Brand name', 'text'],
            'brand_url' => ['Brand URL', 'url'],
            'placement' => ['Placement', 'select'],
            'background_color' => ['Background color', 'color'],
            'text_color' => ['Text color', 'color'],
        ];
        foreach ($fields as $key => [$label, $type]) {
            add_settings_field('lb_' . $key, $label, [$this, 'field'], 'landing-bird-brand-credit', 'lb_brand_credit_main', ['key' => $key, 'type' => $type]);
        }
    }

    public function field(array $args): void
    {
        $options = self::get_options();
        $key = $args['key'];
        $value = $options[$key];
        $name = self::OPTION . '[' . $key . ']';
        if ($args['type'] === 'checkbox') {
            printf('<label><input type="checkbox" name="%s" value="1" %s> Show the credit on the site</label>', esc_attr($name), checked($value, true, false));
            return;
        }
        if ($args['type'] === 'select') {
            printf('<select name="%s">%s</select>', esc_attr($name), sprintf('<option value="top" %s>Top</option><option value="bottom" %s>Bottom</option>', selected($value, 'top', false), selected($value, 'bottom', false)));
            return;
        }
        printf('<input class="regular-text" type="%s" name="%s" value="%s">', esc_attr($args['type']), esc_attr($name), esc_attr($value));
    }

    public function settings_page(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>Landing Bird Brand Credit</h1><form method="post" action="options.php">';
        settings_fields(self::SETTINGS_GROUP);
        do_settings_sections('landing-bird-brand-credit');
        submit_button();
        echo '</form></div>';
    }

    public function enqueue_styles(): void
    {
        if (!self::get_options()['enabled']) {
            return;
        }
        wp_enqueue_style('landing-bird-brand-credit', plugins_url('assets/css/brand-credit.css', dirname(__FILE__, 2) . '/landing-bird-brand-credit.php'), [], '0.1.0');
    }

    public function render_top(): void
    {
        $this->render('top');
    }

    public function render_bottom(): void
    {
        $this->render('bottom');
    }

    private function render(string $placement): void
    {
        $options = self::get_options();
        if (!$options['enabled'] || $options['placement'] !== $placement) {
            return;
        }
        printf('<aside class="lb-brand-credit lb-brand-credit--%s" aria-label="Site credit" style="--lb-credit-bg:%s;--lb-credit-text:%s"><a href="%s" rel="noopener noreferrer">%s <span>%s</span></a></aside>', esc_attr($placement), esc_attr($options['background_color']), esc_attr($options['text_color']), esc_url($options['brand_url']), esc_html($options['message']), esc_html($options['brand_name']));
    }

    private static function get_options(): array
    {
        $stored = get_option(self::OPTION, []);
        return self::sanitize_options(array_merge(self::defaults(), is_array($stored) ? $stored : []));
    }
}
