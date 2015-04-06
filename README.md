# autoCMS
Automagically create a CMS with class names and data attributes.


## Goal
The goal of this repository is to make a content management system that automatically scans HTML files, creates json data files, and then allows you to simply edit the text of your site through an admin without the need to know code. Future goal would be to add data files to a document database like couchDB.


### Steps
1. Create an HTML site and add the class "auto-edit" to heading tags, paragraph tags, span tags, or simply the whole div block.
2. Copy the .htaccess and index.php files into an admin folder.
3. Visit http://yoursite/admin/
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
<img src="..." class="auto-edit-img">
```
Or for background images

```HTML
<div class="auto-edit-bg-img">...</div>
```
**NOTE:**
The bg-img will add a style attribute with a background-image: url(img/url/imagename.jpg); and remove any other style attribute. Add a custom class for all your other background image attributes and do not use a style tag with this autoCRM class.


#### Repeating Tags

```HTML
<div class="auto-repeat">
    <p class="auto-edit">...</p>
    <img src="..." class="auto-edit">
</div>
```

This would be the same as the following with as many iterations as setup in the CMS

```HTML
<div>
    <p class="auto-edit">...</p>
    <img src="..." class="auto-edit">
    <p class="auto-edit">...</p>
    <img src="..." class="auto-edit">
    ...
</div>
```


#### Navigation Text Tags

```HTML
    <li><a href="..." class="auto-nav" data-auto-description="home navigation">...</a>
```


### Data Attributes

##### data-auto-description

This data tag can be used to add an additional description to a field, best used to know which navigation link is which.


## Special Thanks

Thanks to toroPHP https://github.com/anandkunal/ToroPHP for the wonderful small routing library.

Also thanks to David Miller for the free bootstrap theme that can be found here http://startbootstrap.com/template-overviews/sb-admin-2/


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