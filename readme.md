# Simple Tika Client


## Install

    composer require hocvt/tika-simple

## Usage

```php
require __DIR__ . "/../vendor/autoload.php";


$client = new \HocVT\TikaSimple\TikaSimpleClient();

$file = __DIR__ . "/demo.docx";

$mime = $client->mimeFile($file);

echo "Tika version " . $client->version() . "\n";

var_dump($mime);

$html = $client->rmetaFile($file, 'html');

var_dump($html); 
```
