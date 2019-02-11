# yii2-content-tools

![Latest Stable Version](https://img.shields.io/packagist/v/bizley/contenttools.svg)
![Total Downloads](https://img.shields.io/packagist/dt/bizley/contenttools.svg)
![License](https://img.shields.io/packagist/l/bizley/contenttools.svg)

ContentTools editor implementation for Yii 2.

## ContentTools

Check out ContentTools website http://getcontenttools.com for more information about the editor.

## Installation

Add the package to your composer.json:

    {
        "require": {
            "bizley/contenttools": "^1.2"
        }
    }

and run `composer update` or alternatively run `composer require bizley/contenttools`

## Usage

### 1. The widget.

Wrap any part of the content in `\bizley\contenttools\ContentTools::begin();` and `\bizley\contenttools\ContentTools::end();`.

    <?php \bizley\contenttools\ContentTools::begin(); ?>
    This is the part of view that is editable.
    <p>There are paragraphs</p>
    <div>and more...</div>
    <?php \bizley\contenttools\ContentTools::end(); ?>

You can use the widget multiple times on one page.

### 2. Backend.

Yii 2 ContentTools saves content and uploaded images asynchronously and it requires some preparation on the backend side.

You have to create few controllers' actions:
 - "upload new image" action,
 - "rotate uploaded image" action,
 - "insert & crop uploaded image" action,
 - "save content" action.

Three first actions are already prepared if you don't want any special operations. You can find them in 'actions' folder.
- _UploadAction_ - takes care of validating the uploaded images using `\bizley\contenttools\models\ImageForm` 
  (jpg, png and gif images are allowed, maximum width and height is 1000px and maximum size is 2 MB), images are saved 
  in `content-tools-uploads` folder accessible from web.
- _RotateAction_ - takes care of rotating the uploaded image using Imagine library (through `yii2-imagine` extension 
  required in the composer.json).
- _InsertAction_ - takes care of inserting image into the content with optional cropping using Imagine library.

The default option for the image URLs is:

    'imagesEngine' => [
        'upload' => '/site/content-tools-image-upload',
        'rotate' => '/site/content-tools-image-rotate',
        'insert' => '/site/content-tools-image-insert',
    ],

So if you don't want to change the `imagesEngine` parameter add in your SiteController:

    public function actions()
    {
        return [
            'content-tools-image-upload' => \bizley\contenttools\actions\UploadAction::className(),
            'content-tools-image-insert' => \bizley\contenttools\actions\InsertAction::className(),
            'content-tools-image-rotate' => \bizley\contenttools\actions\RotateAction::className(),
        ];
    }

See [Standalone Actions section in Yii 2 Guide](https://www.yiiframework.com/doc/guide/2.0/en/structure-controllers#standalone-actions) 
for more info about adding actions.
 
The last "save content" action is not prepared because it depends on the business logic of your application. 
See *Saving content* part at the end of this file.
  
Default configuration for this is:

    'saveEngine' => [
        'save' => '/site/save-content',
    ],


## Options

You can add options for the widget by passing the configuration array in the `begin()` method.

### id

_default:_ `null`  
Identifier of the editable region (must be unique). 
If left empty it is automatically set to 'contentToolsXXX' where XXX is the number of next widget.

### page

_default:_ `null`  
Page identifier. If `null` it is set to the current URL.

### tag

_default:_ `'div'`  
HTML tag that is used to wrap the editable content.

### dataName

_default:_ `'name'`  
Name of the `data-*` attribute that stores the identifier of editable region.

### dataInit

_default:_ `'editable'`  
Name of the `data-*` attribute that marks the region as editable.

### options

_default:_ `[]`  
Array of HTML options that are applied to editable region's tag.

### imagesEngine

_default:_ 

    [
        'upload' => '/site/content-tools-image-upload',
        'rotate' => '/site/content-tools-image-rotate',
        'insert' => '/site/content-tools-image-insert',
    ]
    
Array of the URLs of the image actions *OR* `false` to switch off the default image engine (you will have to prepare JS 
for handling images on your own).

### saveEngine

_default:_

    [
        'save' => '/site/save-content',
    ]
    
Array with the URL of the content saving action *OR* `false` to switch off the default saving engine (you will have to 
prepare JS for handling content saving on your own).

### styles

_default:_ `[]`  
Array of styles that can be applied to the edited content.  
Every style should be added in separate array like:

    'Name of the style' => [
        'class' => 'Name of the CSS class',
        'tags'  => [Array of the html tags this can be applied to] or 'comma-separated list of the html tags this can be applied to'
    ],

Example:

    'Bootstrap Green' => [
        'class' => 'text-success',
        'tags'  => ['p', 'h2', 'h1']
    ],

`tags` key is optional and if omitted style can be applied to every element.

### language

_default:_ `false`  
Boolean flag or language code of the widget translation.  
You can find the list of prepared translations in `@bower/contenttools/translations` folder.  
`false` means that widget will not be translated (default language is English).  
`true` means that widget will be translated using the application language.  
If this parameter is a string widget tries to load the translation file with the given name.  
If it cannot be found and string is longer that 2 characters widget tries again this time with parameter shortened to 2 characters.  
If again it cannot be found language sets back to default.

### globalConfig

_default:_ `true`  
Boolean flag whether the configuration should be global.  
Global configuration means that every succeeding widget ignores `page`, `tag`, `dataName`, `dataInit`, `imagesEngine`, 
`saveEngine`, and `language` parameters and sets them to be the same as in the first one. Also `styles` are added only 
if they've got unique names.

### customJs

_default:_ `null`  
String with custom JS to be initialized with editor.  
Use `editor` variable to point to instance of `ContentTools.EditorApp.get()`.  
See http://getcontenttools.com/api/content-tools for more details about CT API.

## Actions callbacks

The default JS image callbacks assume the following action response:

    {
        'size': [image-width-in-px, image-height-in-px],
        'url': image-url
    }

with optional `'alt'` for insert-action. In case of any errors response should be:

    {
        'errors': [array-of-error-descriptions]
    }

At the moment errors are only displayed in browser's console (user sees only the big transparent cross).


## Saving content

Action responsible for saving the content should expect the array of every page region data in pairs 
`'region-identifier' => 'region-content'`.

Typical structure could look like this:

    [
        'contentTools0' => '...', // HTML content of the contentTools0 region
        'contentTools1' => '...', // HTML content of the contentTools1 region
        '_csrf' => '...', // CRSF token value
        'page' => '/site/index' // page identifier
    ]

Now you just need to validate and save the regions linked to the page with the given identifier.
