# autoCMS
Automatically create a CMS (Content Management System) using class names and a special attribute in plain HTML/JavaScript/CSS site. PHP experience isn't needed, however, PHP is needed on the target server. Apache server with Mod Rewrite is also needed.


## Who Should Use This
This is for people who do not wish to use a complicated site management system like WordPress, Drupal, Joomla, or any other complicated system. This is for people who design simple HTML/Javascript/CSS sites and want to easily add a Content Management System without any hassle or learning a server side language and database system.


#### Please note: the master branch is in development!


## Install Steps
1. Create an HTML site and add the class "auto-edit" to heading tags, paragraph tags, span tags, or simply the whole div block.
2. Copy the admin folder into the root of your site
3. Make sure that the admin folder and sub-folders are writable
4. Visit http://yoursite.com/admin/
5. It will ask you to create a password, then scan your html files and ask you to process them and add to the CMS.
6. autoCMS will rename files to .php (scan for links and rename those as well) and add the appropriate php tags.
    * It will scan and look for edit tags and create a data file structure (JSON).
    * It will then bring you to a basic CMS to edit all your content.


## Tags Examples

**NOTE:** Do not use these tags for styling as they will be removed.

#### Navigation Text Tags

```HTML
<a href="..." class="auto-nav" autocms="home navigation">...</a>
```
**NOTE:**
* All auto-nav tags needs an autocms description attribute
* All navigation links that link to the same page and have the same text must have the same description
* All internal site links require this tag


#### Text Tags

```HTML
<h1 class="auto-edit">...</h1>

<p class="auto-edit">...</p>

<div class="auto-edit">...</p>
```
**TIP:** Best practice is to use div tags so multiple paragraphs can be added and style tags be seen in the CMS.


#### Image Tags

```HTML
<img src="..." class="auto-edit-img" alt="...">
```

**NOTE:** Image alt text will also be available to edit on non-background images.

Or for background images

```HTML
<div class="auto-edit-bg-img" style="background-image: url(path/to/original/image);">...</div>
```


#### Repeating Tags **(Almost Done)**

```HTML
<div class="auto-repeat">
    <p class="auto-edit" autocms="...">...</p>
    <img src="..." class="auto-edit-img" autocms="...">
</div>
```

This would be the same as the following with one addition repeat added by autoCMS

```HTML
<div>
    <p class="auto-edit" autocms="...">...</p>
    <img src="..." class="auto-edit-img" autocms="...">
</div>
<div>
    <p class="auto-edit" autocms="...">...</p>
    <img src="..." class="auto-edit-img" autocms="...">
</div>
```
**NOTE:** 
* Repeated content must be added by autoCMS, adding multiple copies in HTML will be interpreted as one repeat block.
* Can only have one repeat tag per page.

**TIP:** We recommend using the attribute autocms to add a description.


#### Blog/News Feed Tags **(Coming Soon)**

This works like auto-repeat but has special blog tags. Use this to list all your blog posts on a page.
```HTML
<div class="auto-blog">
    <div class="auto-blog-title">...</div>
    <img src="..." class="auto-blog-img">
    <div class="auto-blog-short">...</div>
    <a class="auto-blog-link">Read More</a>
</div>
```


## Special HTML Attributes

#### autocms Attribute

This data tag can be used to add an additional description to a field, also used to know which navigation link is which.

```HTML
<div class="auto-edit" autocms="section about firetrucks">... firetruck ...</div>
```
**TIP:** These descriptions can be edited with the CMS. Good to keep track of what's what.


## Coming Soon and Planed Future Updates

* Adding RSS Feed
* Multi-Language
* Inline Editing
* User Privilege Settings **(Future Plan)**


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