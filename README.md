# json-schema-from-stream

Build a JSON object from a DataSift HTTP stream.

This script provides a simple way to generate a JSON object with all properties that can be recived via a DataSift stream.

This can be useful if you are designing a database schema and need full visability of what keys and values may be delivered.


### Overview

Each interaction is merged with the previous so that a single large object is built. Every 100 interactions, the stored object is saved to out.json.


### Install
 * Edit your DataSift username and API key on line 62 of app.php.
 * OPTIONAL: Edit the CSDL definition to meet your requirments on line 64 of app.php.
 * ```php app.php```