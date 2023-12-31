# file-upload
Trait to simplify uploading images in Laravel

## Installation
``` sh
composer require pfrug/file-upload 1.0
```

```php
// config/app.php
'providers' => [
    ...
    Pfrug\FileUpload\FileUploadServiceProvider::class,
];
```
## Usage

Implement HasImageUploads in you model  
Specify the fields that are of type file

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pfrug\FileUpload\Traits\HasImageUploads;

class Post extends Model
{
    use HasImageUploads;

    protected $uploadableImages = [
        'image',
        'thumb'
    ];

```

In the controller it is not necessary to specify anything.  
Only using the `fill` or `create` method the image will be saved correctly

```php
public function store(Request $request)
{    
    // ... you code here, try-catch validate etc..
    Post::create($request->all());
    ...


public function update(Request $request, Post $post)
    // ... you code here, try-catch, validate data, etc..
    $post->fill($request->all());
    $post->save();
```
Or use direct assign
```php

$post->title = $request->title ;
$post->image = $request->file('image');
$post->save();

```

## Views 
If you wish you can use the default view

```php
    @include('fileupload::imageField', [
            'item' => $post,
            'field' => 'image',
            'cant_delete' => true
        ])
```

## Configuration
```sh
php artisan vendor:publish --tag="fileupload-assets" --provider="Pfrug\FileUpload\FileUploadServiceProvider"
php artisan vendor:publish --tag="fileupload-lang" --provider="Pfrug\FileUpload\FileUploadServiceProvider"
php artisan vendor:publish --tag="fileupload-views" --provider="Pfrug\FileUpload\FileUploadServiceProvider"
```

This commands create files to:  

"fileupload-assets"  
`{project}resources/css/vendor/fileupload/file-upload.css`  
`{project}resources/js/vendor/fileupload/file-upload.js`  
`{project}resources/img/vendor/fileupload/default.png`  

"fileupload-lang"  
`{project}resources/lang/{en-es}/fileupload.php`

"fileupload-views"  
`{project}resources/view/vendor/fileupload/imageField.blade.php`

## Image storage
The images will be saved inside the folder `storage/app/public/{model}`  
It is important to create a symbolic link, this can be done using the command `php artisan storage:link`  

The path of the file will be stored in the database, so it is recommended that it be a field of type varchar(100)  
ex: `posts/9c744648c08730cd850c4d7dad9b998d.jpg`

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
