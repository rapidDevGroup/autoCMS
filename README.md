# autoCMS
Automatically create a CMS (Content Management System) using class names and a special attribute in a plain HTML/JavaScript/CSS site. PHP experience isn't needed, however, PHP is needed on the target server. Apache server with Mod Rewrite is also needed.


## Who Should Use This
This is for people who do not wish to use a complicated site management system like WordPress, Drupal, Joomla, or any other complicated system. This is for people who design simple HTML/Javascript/CSS sites and want to easily add a Content Management System without any hassle or learning a server side language and database system.


#### Please note: the master branch is in development!


## Install Steps
1. Create an HTML site and add the appropriate _auto-edit_ classes to heading tags, paragraph tags, span tags, or simply the whole div block or images.
2. Copy the admin folder into the root of your site.
3. Make sure that the admin folder and sub-folders are writable.
4. Visit http://yoursite.com/admin/
5. It will ask you to create a password, then scan your html files and ask you to process them and add to the CMS.
6. autoCMS will rename files to .php (scan for links and rename those as well) and add the appropriate php tags.
    * It will scan and look for edit tags and create a data file structure (JSON).
    * It will then bring you to a basic CMS to edit all your content.


## Tags Descriptions and Examples

**NOTE:** Do not use these tags for styling as they will be removed.


#### Head Information Tag

This will add information such as title, meta description, meta keywords, and meta author tags into the CMS for editing.

```HTML
<head class="auto-head">...</head>
```
**NOTE:** Cannot use autocms attribute on auto-head tag.


#### Navigation Text Tags

```HTML
<a href="..." class="auto-nav" autocms="home navigation">...</a>
```
**NOTE:**
* All auto-nav tags needs an autocms description attribute.
* All navigation links that link to the same page and have the same text must have the same autocms description.
* All internal site links require these tag.


#### Footer Tag **(Being Worked On)**

```HTML
<footer class="auto-footer">
    <div class="auto-edit">...</div>
    <div class="auto-edit-text">...</div>
    ...
</footer>
```
**NOTE:**
* Cannot use autocms attribute on auto-footer tag.
* All footer tags need to be identical, only one copy is kept and repeated.
* auto-footer can be added to div, section, or anything. Doesn't have to be footer.


#### Edit HTML Tags

```HTML
<div class="auto-edit">...</p>
```
**TIP:** Best practice is to use div tags so multiple paragraphs can be added and style tags be seen in the CMS.


#### Edit Text Tags

```HTML
<h1 class="auto-edit-text">...</h1>
<p class="auto-edit-text">...</p>
```
**TIP:** Best practice is to use non-div tags, auto-edit-text preserves the intended style by only allowing adding text.


#### Image Tags

```HTML
<img src="..." class="auto-edit-img" alt="...">
```
**NOTE:** Image alt text will also be available to edit on non-background images.


For background images:
```HTML
<div class="auto-edit-bg-img" style="background-image: url(path/to/original/image);">...</div>
```


#### Repeating Tags

```HTML
<div class="auto-repeat">
    <div class="auto-edit" autocms="...">...</div>
    <img src="..." class="auto-edit-img" autocms="...">
</div>
```

This would be the same as the following with one addition repeat added by autoCMS.

```HTML
<div>
    <div class="auto-edit" autocms="...">...</div>
    <img src="..." class="auto-edit-img" autocms="...">
</div>
<div>
    <div class="auto-edit" autocms="...">...</div>
    <img src="..." class="auto-edit-img" autocms="...">
</div>
```
**NOTE:** 
* Repeated content must be added by autoCMS, adding multiple copies in HTML will be interpreted as one repeat block.
* Can only have one repeat tag per page (for now).

**TIP:** We recommend using the attribute autocms to add a description.


#### Blog/News Feed Tags **(Being Worked On)**

This works like auto-repeat but has special blog tags. Use this to list all your blog posts on a page.
```HTML
<div class="auto-blog-list list-10">
    <div class="auto-blog-title"></div>
    <img src="" class="auto-blog-img">
    <div class="auto-blog-short"></div>
    <a class="auto-blog-link auto-blog-text"></a>
</div>
```
**NOTE:** Can use list-3, list-5, list-10, list-20, leaving list-X tag out will list all posts. **(Pagination Coming Soon)**

This works to display a single blog post. Use the head tag to edit head information for each blog post.
```HTML
<head class="auto-blog-head">...</head>
```

Use these tags to display actual blog content. 
```HTML
<div class="auto-blog-post">
    <div class="auto-blog-title"></div>
    <div src="" class="auto-blog-bg-img"></div>
    <div class="auto-blog-full"></div>
</div>
```
**NOTE:**
* Can use auto-blog-img and auto-blog-bg-img, however, this will be the same image used in the blog post.
* auto-blog-post's page will be linked to automatically from the list, and will load the correct blog post.
* Any other non-blog auto tags will be editable, however, will be the same for all blog posts.

## Special HTML Attributes


#### autocms Attribute

This data tag can be used to add an additional description to a field, also used to know which navigation link is which.

```HTML
<div class="auto-edit" autocms="section about firetrucks">... firetruck ...</div>
```
**TIP:** These descriptions can be edited with the CMS. Good to keep track of what's what.


## Coming Soon and Planned Future Updates

* Adding RSS Feed
* Multi-Language
* Blog Drafts
* Blog Pagination
* History Log
* Prevent Refresh Form Errors
* Check for Safe Files
* Inline Editing
* Repeat Bulk Load Images
* User Privilege Settings
* Multiple auto-repeat on a Page
* Scan for New Changes
* Commenting System
* Add couchDB Option
* Detect Tag Needs

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