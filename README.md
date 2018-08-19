# Easy get and set photo's GPS data

## Get
```php
$photo = new \Maaaxim\Photo\Photo("photos/IMG_20160416_172622.jpg");
$gps = $photo->getGps()->asArray();
```

## Set
```php
$photo = new \Maaaxim\Photo\Photo("photos/IMG_20160416_172622.jpg");
$photo->setGps(57.684541, 39.806899);
```
