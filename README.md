Wamland Amazon S3 PHP
=====================
This service allows to send and receive a file on Amazon S3


#### Recovering sources
```
composer require wamland/wam-amazon-s3-php:dev-master
```

#### Creation of the file containing the environment variables 
```
cp vendor/wamland/wam-amazon-s3-php/.env_sample.php .env.php
vim .env.sh
```

#### Default configuration
```
// AWS S3
define('AWS_ACL', 'public-read');
define('AWS_VERSION', 'latest');
define('AWS_REGION', 'ap-southeast-2');

define('AWS_ACCESS_KEY_ID', '');
define('AWS_SECRET_ACCESS_KEY', '');
define('AWS_BUCKET', '');
```

#### Loading libraries
```
require('.env.php');
require('vendor/autoload.php');
```

#### Initializing the Amazon S3 Connector
```
use App\Services\S3Service;

$s3 = new S3Service(
    AWS_REGION,
    AWS_VERSION,
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY
);

$s3->bucket = AWS_BUCKET;
```

#### Uploading a media
```
$s3->file = 'src/resources/assets/fixtures/pic.jpg';
$s3->key = 'pic.jpg'; // If the key is not set, the file name will be used

try {
    $s3->put();
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}
```
#### Retrieving a media
```
$s3->key = 'src/resources/assets/fixtures/pic.jpg';

try {
    $media = $s3->get();
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}
```

#### Deleting an item
```
try {
    $s3->delete();
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}
```

### Unit test ([Kahlan](https://kahlan.github.io/))
```
composer test
```

## Complete sending and receiving example
```
<?php
require_once('.env.php');
require_once('vendor/autoload.php');

use App\Services\S3Service;

$s3 = new S3Service(
    AWS_REGION,
    AWS_VERSION,
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY
);

$s3->bucket = AWS_BUCKET;
$s3->file = 'src/resources/assets/fixtures/pic.jpg';

/**
 * |-------------------------------------------
 * | Uploading a media to amazon S3
 * |-------------------------------------------
 */
try {
    $s3->put();
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}

/**
 * |-------------------------------------------
 * | Retrieving a media from Amazon S3
 * |-------------------------------------------
 */
try {
    $media = $s3->get();
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}

/**
 * |-------------------------------------------
 * | Display of the image previously sent
 * |-------------------------------------------
 */
echo sprintf("<img src='%s'>", $media['@metadata']['effectiveUri']);
```