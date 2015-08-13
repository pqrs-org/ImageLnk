# ImageLnk

http://imagelnk.pqrs.org/

You can get image URLs from image page HTML.

For example:

<pre>
submit:
  url: https://twitter.com/ImageLnk/status/631948031873519616

result:
  pageurl:   https://twitter.com/ImageLnk/status/631948031873519616
  title:     twitter: ImageLnk: Penguins http://t.co/aQuSe9BGBD
  referer:   https://twitter.com/ImageLnk/status/631948031873519616
  imageurls: http://pbs.twimg.com/media/CMUhYqLVEAAOVT7.jpg:large
</pre>

# Installation

```
git clone https://github.com/tekezo/ImageLnk.git
cd ImageLnk
composer install
```

Then create config/config.yaml and overwrite default values in config/config.default.yaml.

```
# Example of config/config.yaml

auth_pixiv_id: 'your pixiv id'
auth_pixiv_password: 'your pixiv password'
```

Publish `www` directory by web server if you want to provide public web api.
