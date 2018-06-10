README
======

This Bundles purpose is to enable the Open Source CMS Sulu to serve as a headless back-end.
Content pages can be created in the Sulu admin area. All returned content data is stored in simple key-value-pairs.
The keys can be set freely and the content is created like in usual Sulu applications.

Use it for ReactJs or Angular WebApps or React Native, native, Expo JS or other mobile apps.

# Available Sulu Content Types

Sulu offers many different content types. A content type may be something like a text line input, a full text editor or a media selection for images and other files.
Since they may differ a lot in the way they need to be handled for headless usage, they all need to be handled differently.
By now I managed to implement the ones I identified as most important, at least for my situation.

## The following Sulu content types are available at the moment

### [Text line](http://docs.sulu.io/en/latest/reference/content-types/text_line.html)

Shows a simple text line, the inserted content will be saved as simple string. (From the Sulu CMS docs)

### [Text editor](http://docs.sulu.io/en/latest/reference/content-types/text_editor.html)

Shows a rich text editor, capable of formatting text as well. The output of the editor will be stored as HTML in a string field. (From the Sulu CMS docs)

### [Media selection](http://docs.sulu.io/en/latest/reference/content-types/media_selection.html)

Shows a list with the possibility to assign some assets from the media section to a page. Also allows to define a position, which can be handled later in the template. (From the Sulu CMS docs)

# Installation

Install the bundle using composer:
```
composer require yanc3k/api-content-provider-bundle
```

Enable the Bundle in the `app/AdminKernel.php` *AND* `app/WebsiteKernel.php` file:
___notice: This is done a little bit differently as in vanilla Symfony applications.___
```
$bundles = [
    ...
	$bundles[] = new yanc3k\ApiContentProviderBundle\ApiContentProviderBundle();
    ...
];
```

# Usage

This bundle provides you with an easy way to create page content using the Sulu CMS as a headless back end.
In Sulu you can easily create new templates for the CMS admin area. Those templates are written in XML.
The ApiContentProviderBundle features a controller that can handle the data as it comes from Sulu on a page request.

To use it you need to do two things, explained in detail below. ___Important:___ This is in no way interfering with other functionalities of Sulu.
You won't lose anything by installing this bundle alongside a common setup.

## Easiest way

The ApiContentProviderBundle comes with a Sulu XML template ready to go. You need to copy it to your `app/Resources/templates/pages` folder.
You can do that manually or by using this when in the project root:

```
cp vendor/yanc3k/api-content-provider-bundle/docs/Resources/api-content.xml app/Resources/templates/pages/
```

From now on you can choose the api-content template in any content page in the Sulu CMS admim area.

### The Content page

The content page, defined by the XML template you just copied, features the following:

* Definition of the title of the page, that will be present in the JSON reponse under the `title` key.
* Definition of the URL the page content will be available at. This value will be included in the JSON response with the key `url`.
* Definition of key-value-pairs that are used for the content you want to use in the frontend.

___IMPORTANT___
Every key-value-pair must be ___one___ block! In each block you have the possibily to define a `key` that will be the key the content of this block will be stored under in the JSON response.
Then you can set a value, or content, for this key. Only use one of the inputs (text line, text editor, media selection, ...).

[Screenshot of the page in Sulu with expanded key-value block](./docs/Resources/screenshot-text-editor.png)

The screenshot shows the content page in Sulu using the api-content template. There are three key-values defined.

* One with the key `headline-1` and the value `this is a headline`
* One with the key `image-user` and the value of a selcted image from the media library of the admin area.
* One with the key `atricle-1` and the value of a formatted text written in a rich text editor.

The response for a request to `http://canvas.lo/api-content-canvas` in the browser will return the following JSON.
___Note:___ The URL obove is based on my local setup and will differ based on the URL you defined in your virual host settings for your webserver. The `/api-content-canvas` part is defined in the content page and free of choice.

```json
{
    "url": "/api-content-canvas",
    "title": "canvas",
    "headline-1": "this is a headline",
    "image-user": "/media/1/download/penguin.jpeg?v=1",
    "article-1": "<p><strong>yeah, this is a bold text</strong></p>\n\n<p>&nbsp;</p>\n\n<p>asdasdad</p>\n"
}
```

Lets give that a litte more detail:

The `url` in the response is the url you requested in the browser without locale and base URL.
```
    "url": "/api-content-canvas",
```

The `title` in the response is the content page title defined in Sulu.
```
	"title": "canvas",
```

The `headline-1` in the response is the first dynamic key in the JSON response.
It is the one that was defined in the first key-value block in Sulu. The name of this key is entirely up to you.
```
    "headline-1": "this is a headline",
```

The `image-user` key in the response is a media selection in Sulu. There you can chose a file from the easily manageable media library of Sulu. The relative path to that file will be returned. This path is versioned.
```
    "image-user": "/media/1/download/penguin.jpeg?v=1",
```

The `article-1` key in the JSON response holds the content of a rich text editor input provided by Sulu. As you can see, it is HTML, that can be used in the front end, formatted as it is.
```
    "article-1": "<p><strong>yeah, this is a bold text</strong></p>\n\n<p>&nbsp;</p>\n\n<p>asdasdad</p>\n"
```
