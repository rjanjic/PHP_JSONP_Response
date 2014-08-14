PHP_JSONP_Response
==================

Encode and generate a response to JSONP request

This class can encode and generate a response to JSONP request.

It generates JavaScript code to return JSON encoded variable value as response to a JSONP request.

The generated JavaScript may either invoke a callback function with a name defined by a request variable, or assign a JavaScript variable with the JSON encoded variable value.

## Example

### PHP
```php
require 'JSONP.class.php';

// Print output with correct headers informations
JSONP::output(array('foo' => 'bar'));
```

### jQuery call
```javascript
$.ajax({
	// Call to url
	url: '/path/to/example.php',
	
	// Data Type
	dataType: 'jsonp',
	
	// Send data
	data: {
	  foo: 'bar',
	  baz: 'qux'
	},
	
	// On success call
	success: function(data) {
		// Do stuff with data object 
		if (data.foo == 'bar') {
			alert('Baz!');
		}
	}
});
```

## Other PHP examples
```php
require 'JSONP.class.php';

// JSON encode data
$json_encoded = JSONP::encode(array('foo', 'bar'));

// JSONP string as returned value
$jsonp_encoded = JSONP::output(array('foo', 'bar'), FALSE);

// Print output
JSONP::output(array('foo', 'bar'));

// Print output without headers
JSONP::output(array('foo', 'bar'), TRUE, FALSE);
```
