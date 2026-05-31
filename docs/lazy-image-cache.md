# Lazy image cache for product photos

Lazy image cache serves product images through local site URLs and fills the local upload folders on the first request.

Public URLs:

```text
/upload/shop/products/12345.jpg
/upload/shop/products/thumb/preview_12345.jpg
/upload/shop/products/thumb/thumb_12345.jpg
/upload/shop/products/image/98765.jpg
/upload/shop/products/image/thumb/thumb_98765.jpg
```

The browser never receives the partner URL and Yii does not redirect to the partner. If the local file is missing, `frontend\controllers\ShopImageController` handles the legacy `/upload/shop/...` URL, delegates to `frontend\controllers\ImageController`, starts a one-shot C++ cache process when allowed, and proxies the remote image through Yii for the first response.

## Real file layout

Main product photo:

```text
frontend/web/upload/shop/products/12345.jpg
frontend/web/upload/shop/products/thumb/preview_12345.jpg
frontend/web/upload/shop/products/thumb/thumb_12345.jpg
```

Additional product photo:

```text
frontend/web/upload/shop/products/image/98765.jpg
frontend/web/upload/shop/products/image/thumb/thumb_98765.jpg
frontend/web/upload/shop/products/image/thumb/ico_98765.jpg
```

Partner products are stored physically under their partner folder. Eney uses the same partner-specific layout as `php yii eney-sync/update`:

```text
frontend/web/upload/shop/products/eney/132792.jpg
frontend/web/upload/shop/products/eney/thumb/preview_132792.jpg
frontend/web/upload/shop/products/eney/thumb/thumb_132792.jpg
frontend/web/upload/shop/products/eney/image/98765.jpg
frontend/web/upload/shop/products/eney/image/thumb/thumb_98765.jpg
frontend/web/upload/shop/products/eney/image/thumb/ico_98765.jpg
```

Totobi and other partners use the same pattern:

```text
frontend/web/upload/shop/products/totobi/14104.jpg
frontend/web/upload/shop/products/totobi/thumb/thumb_14104.jpg
frontend/web/upload/shop/products/bergamo/{productId}.jpg
```

The directory name is `upload`, not `uploads`.

## Cross-platform paths

PHP builds paths through Yii aliases and normalizes separators with `DIRECTORY_SEPARATOR`.

Linux example:

```text
@frontend/web/upload/shop/products/thumb/preview_12345.jpg
/mnt/vsemerch-200/www/agcity/agcity.com.ua/frontend/web/upload/shop/products/thumb/preview_12345.jpg
```

Windows example:

```text
@frontend/web/upload/shop/products/thumb/preview_12345.jpg
D:\DEV\htdocs\vsemerch.loc\frontend\web\upload\shop\products\thumb\preview_12345.jpg
```

## First request flow

For `/upload/shop/products/thumb/preview_12345.jpg` Yii resolves product `12345`, detects its partner, and searches local files in this order:

```text
frontend/web/upload/shop/products/{partner}/thumb/preview_12345.jpg
frontend/web/upload/shop/products/{partner}/thumb/preview_12345.jpeg
frontend/web/upload/shop/products/{partner}/thumb/preview_12345.webp
frontend/web/upload/shop/products/{partner}/thumb/preview_12345.png
```

If no preview exists, Yii checks the original file. If the original exists, it can be served while a cache process is started when a remote URL is known. If neither preview nor original exists, Yii resolves a remote URL from `remote_image_url` or an existing full `image` URL, creates an atomic lock, starts the C++ one-shot process, and proxies the remote image to the client.

Second and later requests are served from the local upload path.

For partner products, this local upload path is the partner folder under `products/{partner}`, so lazy-cache and partner syncs do not create two different copies of the same image.

## Eney sync modes

Lazy metadata mode syncs products and remote image URLs, but does not download image files. Images are downloaded only when site pages request `/upload/shop/products/...`:

```bash
php yii eney-sync/update --loadImages=0
```

Full preload mode downloads all images during sync. This is the existing default behavior:

```bash
php yii eney-sync/update
```

Both modes use the same local Eney file layout:

```text
frontend/web/upload/shop/products/eney/{productId}.jpg
frontend/web/upload/shop/products/eney/thumb/preview_{productId}.jpg
frontend/web/upload/shop/products/eney/thumb/thumb_{productId}.jpg
```

## C++ one-shot process

The PHP service starts:

```bash
tools/image-optimizer-cpp/build/vsemerch-image-optimizer cache-one \
  --remote-url "https://partner.example/photo.jpg" \
  --target "/mnt/vsemerch-200/www/agcity/agcity.com.ua/frontend/web/upload/shop/products/eney/12345.jpg" \
  --thumb-dir "/mnt/vsemerch-200/www/agcity/agcity.com.ua/frontend/web/upload/shop/products/eney/thumb" \
  --profiles "thumb:300x300,preview:400x400" \
  --max-width 1200 \
  --max-height 1200 \
  --quality 75 \
  --lock "/mnt/vsemerch-200/www/agcity/agcity.com.ua/frontend/runtime/image-cache/locks/product-12345-preview.lock" \
  --log "/mnt/vsemerch-200/www/agcity/agcity.com.ua/frontend/runtime/image-cache/logs/product-12345-preview.log"
```

Windows binary path:

```text
D:\DEV\htdocs\vsemerch.loc\tools\image-optimizer-cpp\build\Release\vsemerch-image-optimizer.exe
```

Linux binary path:

```text
/mnt/vsemerch-200/www/agcity/agcity.com.ua/tools/image-optimizer-cpp/build/vsemerch-image-optimizer
```

Rebuild the binary after deploying this code. PHP refuses to start an older binary when `src/CacheOne.cpp` is newer than the executable, so an old optimizer cannot leave lazy-cache locks behind.

Main photo profiles:

```text
thumb:300x300,preview:400x400
```

Additional photo profiles:

```text
thumb:400x400,ico:100x100
```

There is no cron, daemon, systemd unit, or persistent queue. Each first request starts one detached process. The process removes its lock file after success or failure.

## Runtime files

Runtime files live under:

```text
frontend/runtime/image-cache/locks
frontend/runtime/image-cache/failed
frontend/runtime/image-cache/logs
```

Locks are created atomically with `fopen($lockPath, 'x')`. Stale locks older than 30 minutes are removed. Fresh failed markers younger than 1 hour prevent another C++ process from being started.

## Security

Remote URLs are resolved only from DB fields. They are not read from GET parameters.

Allowed hosts are configured in `imageCacheAllowedHosts` in Yii params. The controller uses connect timeout 3 seconds, total timeout 15 seconds, maximum response size 30 MB, and MIME validation for `image/*`.

The C++ process writes only to targets generated by `ImagePathService`:

```text
@frontend/web/upload/shop/products
@frontend/web/upload/shop/products/image
@frontend/web/upload/shop/products/eney
@frontend/web/upload/shop/products/eney/image
```

Lock, failed, and log paths are generated only under:

```text
@frontend/runtime/image-cache
```

## Model usage

New lazy URL helpers:

```php
$product->getLazyPic('preview'); // /upload/shop/products/thumb/preview_12345.jpg
$product->getLazyPic('original'); // /upload/shop/products/12345.jpg
$image->getLazyPic('thumb'); // /upload/shop/products/image/thumb/thumb_98765.jpg
```

Existing views can move to these helpers gradually. The old `getPic()` behavior is not removed.

## Testing

Windows:

```powershell
php -l frontend/controllers/ImageController.php
php -l common/services/ImagePathService.php
php -l common/services/ImageCacheProcessService.php
php -l common/services/PartnerImageResolver.php
cd tools\image-optimizer-cpp
.\scripts\build-windows.ps1 -VcpkgRoot C:\vcpkg
```

Linux:

```bash
php -l frontend/controllers/ImageController.php
php -l common/services/ImagePathService.php
php -l common/services/ImageCacheProcessService.php
php -l common/services/PartnerImageResolver.php
cd tools/image-optimizer-cpp
./scripts/build-linux.sh
```

After `cache-one` finishes, verify:

```text
frontend/web/upload/shop/products/eney/12345.jpg
frontend/web/upload/shop/products/eney/thumb/preview_12345.jpg
frontend/web/upload/shop/products/eney/thumb/thumb_12345.jpg
```

and verify that the related lock file was removed.
