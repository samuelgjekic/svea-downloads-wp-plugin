# Svea Checkout Downloads Plugin

The **Svea Checkout Downloads** plugin displays download statistics for the Svea Checkout plugin, integrating with both the WordPress admin dashboard and frontend. It features caching, a settings page, and a widget display.

## Table of Contents

1. [Installation](#installation)
2. [Setup](#setup)
3. [Usage](#usage)
   * [Admin Settings](#admin-settings)
   * [Frontend Display](#frontend-display)
4. [Testing](#testing)
   * [Docker Testing](#docker)
5. [Classes and Methods](#classes-and-methods)
   * [Svea_Downloads_Widget](#svea_downloads_widget)
   * [Svea_Downloads_Settings](#svea_downloads_settings)
   * [Svea_Downloads_Scripts](#svea_downloads_scripts)
   * [Svea_Downloads_Getter](#svea_downloads_getter)
   * [Cache_Manager](#cache-manager)
   * [Language class(I18n)](#i18n)
7. [Template System](#widget-template-files)
9. [My Personal Notes](#my-personal-notes)

## Installation

1. Download the Github Repo.
2. Upload the plugin folder to `/wp-content/plugins/`.
3. Activate via the WordPress 'Plugins' menu.

## Setup

After activation, navigate to `Settings -> Svea Checkout Downloads Settings` to configure the plugin.

## Usage

### Admin Settings

- **Enable Caching**: Toggle caching on or off.
- **Enable Widget Outside Admin Dashboard**: Show the widget on the frontend.

### Frontend Display

If disabled, the widget will only be seen in the admin dashboard. If enabled however, the widget will be available as shortcode and as
a WP_Widget. You can add the widget on frontend (If you enabled it in Settings > Svea Checkout Downloads ) by using the shortcode: `[svea_downloads_widget]`

![widget-frontend](https://github.com/user-attachments/assets/4eec3394-65a0-41db-a98a-6dc6da7dc863)

## Testing

Verified plugin functionality by:
- Enabling/disabling caching.
- Displaying the widget on the frontend.
- Checking update display accuracy.
- Using error_logging and custom logging when debugging.
- Unit Testing with Wordpress testing suite(dev build).

### Docker

To test the **Svea Checkout Downloads** plugin using Docker follow the below instructions.

#### Docker Setup

1. **Start Docker Container**

   Clone the github repo and run docker by using the command:

    ```bash
   docker-compose up --build --no-cache
   ```

2. **Access WordPress**

   Open your web browser and go to `http://localhost:8000` to access the WordPress installation.

3. **Install and Activate Plugin**

   - Follow the WordPress installation process.
   - Go to the WordPress admin dashboard (`http://localhost:8000/wp-admin`).
   - Navigate to **Plugins** > **Installed Plugins**.
   - Locate **Svea Checkout Downloads** and activate it.


#### Notes

- Ensure that the `docker-compose.yml` and `Dockerfile` are available in the root folder.
- To avoid committing sensitive information or unnecessary files, `.gitignore` is used to exclude files such as `wp-content` from being tracked in Github.


## Classes and Methods


### Plugin Class: `Svea_Checkout_Downloads\Plugin`

The `Plugin` class acts as the core controller for the plugin, managing initialization, loading dependencies, and setting up hooks. This design ensures the plugin is modular and easy to maintain.

#### Key Responsibilities:
1. **Initialization**:
   - `init_plugin()`: This method initializes the plugin and loads its various modules (such as language files, widgets, and settings).
   
2. **Autoloading**:
   - Composer is used for autoloading external dependencies and internal classes, ensuring efficient file loading and management.

3. **Module Loading**:
   - **`I18n`**: Handles plugin localization and ensures language files are loaded.
   - **`Svea_Downloads_Widget`**: Manages the widget displaying download statistics.
   - **`Svea_Downloads_Settings`**: Registers and manages plugin settings, allowing customization.

4. **Hook Registration**:
   - Hooks into WordPress for enqueuing scripts and styles in both the admin and frontend environments.
   
5. **Singleton Design Pattern**:
   - The class uses a singleton pattern to ensure only one instance of the plugin is created, promoting memory efficiency and ensuring a single point of control.

#### Class Properties:
- **`PLUGIN_TITLE`**: Defines the plugin's internal title.
- **`VERSION`**: Tracks the plugin version.
- **`$I18n`**, **`$widget`**, **`$settings`**: References to core modules for language handling, widget logic, and settings configuration.

---

This structure is efficient and provides flexibility for future improvements, such as adding more features or customizing functionality without altering core logic.


### `Svea_Downloads_Getter`

This class handles fetching download data for the **Svea Checkout for WooCommerce** plugin from the WordPress.org repository. It also includes caching functionality to minimize API calls and improve performance.

#### `get_download_count(bool $useCaching = false): int`

Fetches the total download count from the WordPress.org API, with optional caching.

- **Parameters**:
  - `$useCaching` (bool): If true, attempts to use cached data (stored in transients). If false, fetches fresh data from the API. The `$useCaching` value is overridden by the 'Enable Caching' option from the plugin settings, if available.

- **Logic**:
  1. **Without caching**: If caching is disabled or unavailable, it will directly fetch the download count using `fetchDownloads()`.
  2. **With caching**: If caching is enabled, it first checks the cached value in the transient. If the cached value does not exist, it fetches the data from the API and stores it in the cache.

```php
public function get_download_count(bool $useCaching = false): int {
    /**
     * Check if caching is enabled from settings or passed parameter
     */
    $options = get_option('svea_checkout_downloads_options');
    $useCaching = isset($options['enable_caching']) ? (bool)$options['enable_caching'] : $useCaching;
    
    if (!$useCaching) {
        // Fetch directly from the API if caching is disabled
        $downloads = $this->fetchDownloads();
    } else {
        // Check if the cached download count exists
        $cached_downloads = $this->cache_manager->exists(self::CACHE_KEY);
    }

    /**
     * If caching is enabled but no cached value exists, fetch from API and update cache
     */
    if ($useCaching && ($cached_downloads === false)) {
        $downloads = $this->fetchDownloads();
        $success = $this->cache_manager->set(self::CACHE_KEY, $downloads, $this->get_cache_interval());
        if (!$success) {
            error_log(__METHOD__ . ' - Failed to set transient for ' . self::CACHE_KEY . ' with value: ' . $downloads);
        }
    }

    return $downloads;
}
```


#### `fetchDownloads(): int`

A private method that directly fetches the total download count from the WordPress.org API for the **Svea Checkout for WooCommerce** plugin.

```php
private function fetchDownloads(): int {
    $response = wp_remote_get('https://api.wordpress.org/plugins/info/1.0/svea-checkout-for-woocommerce.json');
    
    if (is_wp_error($response)) {
        error_log(__METHOD__ . ' - API Error: ' . $response->get_error_message());
        return 0;
    }
    
    $data = json_decode(wp_remote_retrieve_body($response), true);
    return $data['downloaded'] ?? 0;
}
```
Note: `api.wordpress.org/stats/plugin/1.0/downloads.php?slug=[svea-checkout-for-woocommerce]` can be used to retrieve daily statistics. However this is not implemented.
#### `set_cache_interval(int $seconds): void`


- **Parameters**:
  - `$seconds` (int): The time in seconds to store the transient

- **Logic**:
  1. Sets a new interval in seconds for how long to store a transient

#### `get_cache_interval(): int`

- **Logic**:
  1. Gets the current interval set in seconds




### `Svea_Downloads_Widget`

This class extends `WP_Widget` and is responsible for rendering the **Svea Checkout Downloads** widget, which displays the total number of downloads for the Svea Checkout plugin.

#### Key Features:
- **`__construct($isEnabledOutsideAdmin)`**:
  - Initializes the widget with optional display outside the admin area based on the `$isEnabledOutsideAdmin` parameter or saved settings.
  - Registers the widget in the admin dashboard or as a shortcode depending on the configuration.
  
- **`widget($args, $instance)`**:
  - Renders the widget content on both the frontend and the WordPress admin dashboard.
  - Fetches the total download count from a transient or, if unavailable, fetches it directly from the API.
  - If a valid template exists, it will include the widget template; otherwise, it will log an error.

```php
public function widget($args, $instance) {
    $downloads = get_transient('svea_downloads_count');
    if (false === $downloads) {
        $downloads = $this->getter->get_download_count();
        if (false === $downloads) {
            error_log('Svea Checkout: Could not retrieve download count.');
            echo '<p>' . esc_html__('Could not retrieve download count. Please try again later.', 'svea-checkout-downloads') . '</p>';
            return;
        }
    }
    echo $args['before_widget'];
    $template_file = Template_Loader::get_template_path('widget');
    if (file_exists($template_file)) {
        include_once $template_file;
    } else {
        echo '<p>' . esc_html__('Template not found!', 'svea-checkout-downloads') . '</p>';
    }
    echo $args['after_widget'];
}
```

- **`form($instance)`**:
  - Displays the widget settings form in the WordPress admin for customizing the title and layout. ( Works with Classic Widgets )
  - Includes a template for the form or logs an error if it is missing.

```php
public function form($instance) {
    $template_file = Template_Loader::get_template_path('form');
    if (file_exists($template_file)) {
        include_once $template_file;
    } else {
        echo '<p>' . esc_html__('Template not found!', 'svea-checkout-downloads') . '</p>';
    }
}
```

- **`update($new_instance, $old_instance)`**:
  - Handles saving the widget settings, sanitizing user input for security.

```php
public function update($new_instance, $old_instance) {
     $instance = [];
     $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
     $instance['before_widget'] = (!empty($new_instance['before_widget'])) ? sanitize_text_field($new_instance['before_widget']) : '<div>';
     $instance['after_widget'] = (!empty($new_instance['after_widget'])) ? sanitize_text_field($new_instance['after_widget']) : '</div>';
     return $instance;
    }
}
```

#### Additional Features:
- **Dashboard Integration**:
  - Registers the widget in the WordPress admin dashboard using `wp_add_dashboard_widget()`.
  
- **Shortcode Support**:
  - Adds a shortcode to display the widget outside of the admin dashboard, improving flexibility for frontend usage.
Here’s a GitHub documentation snippet for the `I18n` class, which manages the loading of translation files for the plugin:


### `I18n`

This class is responsible for handling the loading of translation files for the **Svea Checkout Downloads** plugin, ensuring the plugin is translatable into different languages.

#### `initialize(): void`

Initializes the translation class by setting up the path to the language files and hooking the language loading method into WordPress’s `plugins_loaded` action.

```php
public function initialize(): void {
    // Set the path to the languages folder
    $this->language_files_path = dirname(plugin_basename(__FILE__)) . '/languages';
    
    // Hook into 'plugins_loaded' to load the language files
    add_action('plugins_loaded', [ $this, 'load_language_files'], 1);
}
```

#### `load_language_files(): void`

Loads the translation files for the plugin using `load_plugin_textdomain`. If the language files cannot be loaded, it logs a warning to help with debugging.

- **Logic**:
  - It sets the correct path to the language files using the `plugin_basename` function.
  - It attempts to load the language files with `load_plugin_textdomain`.
  - If the language files cannot be loaded, it logs an error message to the error log.

```php
public function load_language_files(): void {
    // Set the path to the plugin's language files
    $this->language_files_path = dirname(dirname(plugin_basename(__FILE__))) . '/languages/';
    
    // Attempt to load the text domain for translations
    $loaded = load_plugin_textdomain('svea-checkout-downloads', false, $this->getPath());

    // Log a warning if language files cannot be loaded
    if (!$loaded) {
        error_log('Warning: Could not load language files! Path: ' . $this->getPath());
    }
}
```

#### `getPath(): string`

Returns the path to the plugin's language files. This method is used internally to ensure the correct path is provided to WordPress functions that load the translation files.

```php
public function getPath(): string {
    return $this->language_files_path;
}
```

#### Translation Files

The translation files should be located in the `/languages` directory inside the plugin folder. The file names should follow the pattern `svea-checkout-downloads-{locale}.mo` for each supported language.

---

### `Svea_Downloads_Scripts`

Enqueues CSS styles for the admin dashboard and frontend.

**Key Methods:**
- `enqueue_styles_admin()`: Enqueues admin CSS.
- `enqueue_styles_frontend()`: Enqueues frontend CSS.

### `Svea_Downloads_Settings`

Handles plugin settings and settings page integration.

**Key Methods:**
- `__construct()`: Initializes settings and adds the settings page.
- `add_settings_page()`: Adds the settings page to the admin menu.
- `settings_page()`: Renders the settings page.
- `register_settings()`: Registers settings and fields.
- `sanitize_options()`: Sanitizes input data.
- `enable_caching_callback()`: Renders 'Enable Caching' field.
- `enable_outside_admin_callback()`: Renders 'Enable Widget Outside Admin Dashboard' field.

  
### `Cache_Manager`

This simple class manages caching mechanisms, ensuring that API calls are minimized and the data remains up to date.

- **`set_transient($key, $value, $expiration)`**: Stores the value in WordPress' transients API for a specified period.
  
```php
public static function set_transient($key, $value, $expiration) {
    if (false === get_transient($key)) {
        set_transient($key, $value, $expiration);
    }
}
```

- **`get_transient($key)`**: Retrieves a value from the cache, if available.

```php
public static function get_transient($key) {
    return get_transient($key);
}
```


## Widget Template Files

The plugin includes specific template files used to render various parts of the widget, ensuring ease of maintenance.

### **Template_Loader Class**

The `Template_Loader` class is responsible for managing the template files for rendering the widget, forms, and settings. This class offers flexibility in organizing the template system.

#### Key Properties:

- `$templates`: An associative array that maps template keys to their filenames, making it easy to reference them.
  
- `$template_path`: A string holding the path to the directory where templates are stored.

- `$widget_args`: An array containing arguments used to render the widget's HTML, which can be customized for specific use cases.

#### Key Methods:

- **`init()`**: Initializes the template loader by defining the template path constant (`SVEA_TEMPLATE_PATH`) and setting default widget arguments for HTML structure and title.

```php
public static function init() {
    if (!defined('SVEA_TEMPLATE_PATH')) {
        define('SVEA_TEMPLATE_PATH', plugin_dir_path(__FILE__) . '../templates/');
    }
    self::$template_path = SVEA_TEMPLATE_PATH;
    self::$widget_args = [
        'before_widget' => '<div class="wrap">',
        'after_widget' => '</div>',
        'title' => 'Svea Checkout Downloads',
    ];
}
```

- **`get_template_path(string $template_name)`**: Fetches the file path of a specific template based on the template name provided. This is useful for dynamically loading different templates.

```php
public static function get_template_path(string $template_name): string {
    if (isset(self::$templates[$template_name])) {
        return self::$template_path . self::$templates[$template_name];
    }
    return '';
}
```

#### Template Files

The following template files are loaded by the `Template_Loader` class and serve specific purposes:

- **svea-downloads-widget.php**: Template for rendering the widget on the frontend or admin dashboard.
- **svea-downloads-widget-form.php**: Template for the widget form that appears in the widget settings.
- **svea-downloads-widget-settings.php**: Template for rendering additional settings related to the widget in the plugin settings.

The `$templates` property in the `Template_Loader` class links the template keys to the corresponding files:

```php
private static $templates = [
    'widget' => 'svea-downloads-widget.php',
    'form' => 'svea-downloads-widget-form.php',
    'widget_settings' => 'svea-downloads-widget-settings.php',
];
```



## My Personal Notes

#### Development Time: 
The plugin was developed over the course of 3-4 days, primarily during the weekend, as I was balancing other work commitments throughout the week.

#### Reflection: 
I believe I have been thorough in my approach, demonstrating a solid understanding of WordPress development. The plugin adheres to best practices and closely follows the project’s guidelines, while also incorporating additional functionality beyond the basic requirements. Throughout the development process, I have ensured that the code adheres to PHP standards, with a focus on security measures aligned with WordPress security practices. I implemented Composer for autoloading, utilized WordPress internationalization (i18n) for handling multiple languages, and integrated caching mechanisms to optimize performance. Furthermore, the plugin is designed with scalability in mind, ensuring future enhancements can be easily integrated.

#### Areas for Improvement: 
While I focused on delivering a functional and scalable solution, I had several ideas for further enhancements. However, I aimed to strike a balance between staying within the project scope and showcasing my capabilities. Given the simplicity of the task—displaying the download count of another plugin—I ensured the code is structured in a way that allows for future scalability and ease of testing. Additional unit tests could be implemented to further verify functionality.
