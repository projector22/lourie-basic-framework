# php-debuggin-tools

PHP Debugging tools

## Notes on Autoloading

The `Debug` class contains a bunch of static properties that link back various Tool classes. In order for this to function as expected, you need to either first call `Debug::__constructStatic()`, or more ideally, call it from your autoload function. For example.

```php
function autoload( string $class ) {
    /**
     * EXAMPLE
     */
    $path = realpath( __DIR__ . '/examplePath/' );
    $require_path = str_replace( '\\', '/', $path. $class );
    require_once $require_path . '.php';

    /**
     * THIS IS THE IMPORTANT BIT.
     * 
     * Check method `__constructStatic` exists and call it if so.
     */
    if ( method_exists( $class, '__constructStatic' ) ) {
        $class::__constructStatic();
    }
}
```

## Tools

### Display

Usage is as follows:

```php
Debug::$display->data( 'Example Data' );
Debug::$display->table( ['a' => 'b', 'c' => 'd'] );
Debug::$display->page_data();
```

### Timing

Usage is as follows:

```php
Debug::$timer->start();
// Some code
Debug::$timer->timestamp( 'label' );
// Some code
Debug::$timer->timestamp( 'another label' );
// Some code
Debug::$timer->end( true );
```

### Lorium

Usage is as follows:

```php
Debug::$lorium->generate( 2 );
```

### Cmd

Usage is as follows:

```php
Debug::$cmd->show_output( 'ls' );
```

### Js

Usage is as follows:

```php
Debug::$js->detect_keystroke();
```

### Log

Usage is as follows:

```php
Debug::$log->to_file( 'Some Data' );
Debug::$log->to_file( ['key' => 'Some Data'] );
Debug::$log->to_file( 'SELECT * FROM table_name', 'sql.log' );
```
