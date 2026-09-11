# Landing Bird Brand Credit

Landing Bird Brand Credit adds a small accessible site credit through standard WordPress hooks. It has no runtime dependencies and does not modify the active theme.

## Requirements and installation

The plugin supports WordPress 6.4 or later and PHP 7.4 or later, including PHP 8.3. Upload the `landing-bird-brand-credit` directory to `wp-content/plugins/`, activate it in Plugins, then open Settings > Brand Credit.

## Configuration

Administrators can enable or disable the credit, change its prefix/message, brand name, HTTPS or HTTP brand URL, top or bottom placement, and background and text colors. The default output is “Made with love by Landing Bird” linked to https://landingbird.mx. Colors accept hexadecimal values only and URLs must use HTTP(S).

The stylesheet is enqueued only while the credit is enabled. Settings are retained when the plugin is deactivated; remove the settings with the normal WordPress options tools if removal is required.

## Versioning and license

Releases follow semantic versioning. See [CHANGELOG.md](CHANGELOG.md) for changes. This plugin is available under the GPL-2.0-or-later license; see [LICENSE](LICENSE).
