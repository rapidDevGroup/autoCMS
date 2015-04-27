# autoCMS
Automatically create a CMS using class names and data attributes in plain HTML/JavaScript/CSS site. PHP experience isn't needed, however, PHP is needed on the target server.

## Who Uses This
This is for people who do not wish to use a complicated site management system like WordPress, Drupal, Joomla, or any other complicated system. This is for people who design simple HTML/Javascript/CSS sites and want to easily add a Content Management System without any hassle or learning a server side language and database system.

### Steps
1. Create an HTML site and add the class "auto-edit" to heading tags, paragraph tags, span tags, or simply the whole div block.
2. Copy the admin folder into the root of your site
3. Visit http://yoursite.com/admin/
4. It will ask you to create a password, then scan your html files and ask you which ones to add to the CMS.
5. autoCMS will rename files to .php (scan for links and rename those as well) and add the appropriate php tags.
    * It will scan and look for edit tags and create a data file structure (JSON).
    * It will then bring you to a basic CMS to edit all your content.
    

## Tags Examples

#### Text Tags

```HTML
<h1 class="auto-edit">...</h1>

<p class="auto-edit">...</p>

<div class="auto-edit">...</p>
```


#### Image Tags

```HTML
<img src="..." class="auto-edit-img" alt="...">
```
Or for background images **(Coming Soon)**

```HTML
<div class="auto-edit-bg-img">...</div>
```
**NOTE:**
* The bg-img will add a style attribute with a background-image: url(img/url/imagename.jpg); and remove any other style attribute. Add a custom class for all your other background image attributes and do not use a style tag with this autoCRM class.
* Image alt text will also be available to edit.

#### Repeating Tags **(Coming Soon)**

```HTML
<div class="auto-repeat">
    <p class="auto-edit">...</p>
    <img src="..." class="auto-edit-img">
</div>
```

This would be the same as the following with as many iterations as setup in the CMS

```HTML
<div>
    <p class="auto-edit">...</p>
    <img src="..." class="auto-edit-img">
    <p class="auto-edit">...</p>
    <img src="..." class="auto-edit-img">
    ...
</div>
```


#### Navigation Text Tags **(Coming Soon)**

```HTML
    <li><a href="..." class="auto-nav" autocms="home navigation">...</a>
```
**NOTE:**
* All auto-nav tags need a description
* All navigation links that link to the same page must have the same description

### Data Attributes

##### autocms

This data tag can be used to add an additional description to a field, also used to know which navigation link is which.


## Special Thanks

Thanks to toroPHP https://github.com/anandkunal/ToroPHP for the wonderful small routing library.

Also thanks to David Miller for the free bootstrap theme that can be found here http://startbootstrap.com/template-overviews/sb-admin-2/

http://sourceforge.net/projects/simplehtmldom/

## License

MIT License

Copyright 2015 Rapid Dev Group Inc. http://rapiddevgroup.com

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.